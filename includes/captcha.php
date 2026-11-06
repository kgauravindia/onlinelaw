<?php
/**
 * Online Law - Secure Anti-Bot Graphical SVG CAPTCHA Engine
 * Lightweight, zero external dependencies, robust against OCR bots
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

/**
 * Generate a random unambiguous alphanumeric CAPTCHA code
 */
function olaw_generate_captcha_code($length = 5) {
    // Exclude easily confused characters: 0/O, 1/I/l, 8/B
    $charset = '2345679ACDEFGHJKLMNPQRSTUVWXYZ';
    $code = '';
    $max_idx = strlen($charset) - 1;
    for ($i = 0; $i < $length; $i++) {
        $code .= $charset[random_int(0, $max_idx)];
    }
    $_SESSION['olaw_captcha_code'] = strtolower($code);
    $_SESSION['olaw_captcha_time'] = time();
    return $code;
}

/**
 * Render a security-hardened SVG CAPTCHA image as Data URI
 */
function olaw_render_captcha_svg($code = null, $width = 160, $height = 46) {
    if (empty($code)) {
        $code = !empty($_SESSION['olaw_captcha_code']) ? strtoupper($_SESSION['olaw_captcha_code']) : olaw_generate_captcha_code(5);
    }

    $chars = str_split($code);
    $char_count = count($chars);
    $spacing = ($width - 24) / max(1, $char_count);

    // Professional theme palette: saffron, green, navy, slate
    $colors = ['#ff671f', '#046a38', '#1e3a8a', '#d97706', '#059669', '#2563eb', '#0f172a'];

    // Generate random noise dots
    $dots = '';
    for ($i = 0; $i < 24; $i++) {
        $cx = random_int(5, $width - 5);
        $cy = random_int(5, $height - 5);
        $r = random_int(1, 2);
        $dot_color = $colors[array_rand($colors)];
        $opacity = random_int(20, 50) / 100;
        $dots .= "<circle cx='{$cx}' cy='{$cy}' r='{$r}' fill='{$dot_color}' opacity='{$opacity}' />";
    }

    // Generate random interference wavy lines
    $lines = '';
    for ($i = 0; $i < 3; $i++) {
        $y1 = random_int(8, $height - 8);
        $y2 = random_int(8, $height - 8);
        $ctrl_x1 = random_int(30, 60);
        $ctrl_y1 = random_int(4, $height - 4);
        $ctrl_x2 = random_int(90, 130);
        $ctrl_y2 = random_int(4, $height - 4);
        $line_color = $colors[array_rand($colors)];
        $stroke_w = random_int(1, 2);
        $opacity = random_int(35, 65) / 100;
        $lines .= "<path d='M 4 {$y1} C {$ctrl_x1} {$ctrl_y1}, {$ctrl_x2} {$ctrl_y2}, " . ($width - 4) . " {$y2}' stroke='{$line_color}' stroke-width='{$stroke_w}' fill='none' opacity='{$opacity}' stroke-linecap='round' />";
    }

    // Render characters with randomized rotation and position
    $text_elements = '';
    foreach ($chars as $idx => $char) {
        $x = 16 + ($idx * $spacing) + random_int(-2, 3);
        $y = random_int(29, 34);
        $rot = random_int(-15, 15);
        $font_size = random_int(24, 28);
        $char_color = $colors[array_rand($colors)];
        $text_elements .= "<text x='{$x}' y='{$y}' font-family=\"'Outfit', 'Courier New', monospace, sans-serif\" font-size='{$font_size}' font-weight='800' fill='{$char_color}' transform='rotate({$rot}, {$x}, {$y})'>{$char}</text>";
    }

    $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='{$width}' height='{$height}' viewBox='0 0 {$width} {$height}' style='background: linear-gradient(135deg, #f8fafc 0%, #eef2f6 100%); border-radius: 8px; border: 1px solid #cbd5e1; user-select: none;'>
        <defs>
            <filter id='turb' x='0%' y='0%' width='100%' height='100%'>
                <feTurbulence type='fractalNoise' baseFrequency='0.03' numOctaves='1' result='noise' />
                <feDisplacementMap in='SourceGraphic' in2='noise' scale='1.5' xChannelSelector='R' yChannelSelector='G' />
            </filter>
        </defs>
        {$dots}
        {$lines}
        <g filter='url(#turb)'>
            {$text_elements}
        </g>
    </svg>";

    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

/**
 * Verify submitted CAPTCHA code against session
 */
function olaw_verify_captcha($user_input) {
    if (empty($user_input)) {
        return false;
    }

    $expected = $_SESSION['olaw_captcha_code'] ?? '';
    $captcha_time = $_SESSION['olaw_captcha_time'] ?? 0;

    // Check expiration (valid for 10 minutes)
    if (empty($expected) || (time() - $captcha_time > 600)) {
        unset($_SESSION['olaw_captcha_code'], $_SESSION['olaw_captcha_time']);
        return false;
    }

    $clean_input = strtolower(trim($user_input));
    $is_valid = hash_equals($expected, $clean_input);

    // One-time use: clear session on verification attempt
    unset($_SESSION['olaw_captcha_code'], $_SESSION['olaw_captcha_time']);

    return $is_valid;
}

// Handle AJAX refresh request if called directly
if (isset($_GET['action']) && $_GET['action'] === 'refresh_captcha') {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    $new_code = olaw_generate_captcha_code(5);
    $img_data = olaw_render_captcha_svg($new_code);
    echo json_encode([
        'status' => 'success',
        'image' => $img_data
    ]);
    exit;
}

/**
 * Check if the user is already verified for search in the current session
 */
function olaw_is_search_verified() {
    $verified_at = $_SESSION['olaw_search_verified_at'] ?? 0;
    return ($verified_at > 0 && (time() - $verified_at < 1800)); // 30 minutes window
}

/**
 * Validate search CAPTCHA request.
 * Returns true if session is verified or valid code is provided.
 */
function olaw_validate_search_captcha() {
    // 1. If session is already verified within 30 mins, allow seamless pagination & searches
    if (olaw_is_search_verified()) {
        return true;
    }

    // 2. If captcha_code is submitted with the search
    $submitted_code = trim($_REQUEST['captcha_code'] ?? '');
    if (!empty($submitted_code)) {
        if (olaw_verify_captcha($submitted_code)) {
            $_SESSION['olaw_search_verified_at'] = time();
            return true;
        }
    }

    return false;
}

/**
 * Render compact inline Search CAPTCHA input HTML
 */
function olaw_render_search_captcha_widget($id_suffix = '') {
    global $base_url;
    $is_verified = olaw_is_search_verified();
    if ($is_verified) {
        return '<div class="d-inline-flex align-items-center text-success small font-weight-bold px-2 py-1 bg-white rounded border border-success" style="font-size: 11.5px; height: 38px;">
            <i class="fas fa-shield-alt mr-1 text-success"></i> Verified Human
        </div>';
    }

    $new_code = olaw_generate_captcha_code(5);
    $svg = olaw_render_captcha_svg($new_code, 120, 38);
    $img_id = 'searchCaptchaImg' . $id_suffix;
    $input_id = 'searchCaptchaInput' . $id_suffix;
    $btn_id = 'searchCaptchaBtn' . $id_suffix;

    $base = !empty($base_url) ? $base_url : '/';

    $html = '
    <div class="search-captcha-wrapper d-inline-flex align-items-center" style="gap: 6px; background: #ffffff; padding: 3px 6px; border-radius: 8px; border: 1px solid #cbd5e1;">
        <img id="' . $img_id . '" src="' . $svg . '" alt="CAPTCHA" style="height: 32px; width: 110px; border-radius: 6px; border: 1px solid #e2e8f0; display: block;" user-select="none">
        <button type="button" id="' . $btn_id . '" class="btn btn-sm btn-light border py-0 px-2 text-muted" onclick="olawRefreshSearchCaptcha(\'' . $img_id . '\', \'' . $btn_id . '\')" title="Refresh security code" style="height: 32px;">
            <i class="fas fa-sync-alt" style="font-size: 11px;"></i>
        </button>
        <input type="text" name="captcha_code" id="' . $input_id . '" class="form-control form-control-sm text-uppercase font-weight-bold" placeholder="Code" maxlength="6" required style="width: 75px; height: 32px; font-size: 13px; letter-spacing: 1.5px; text-align: center; border-color: #cbd5e1;" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
    </div>
    <script>
    if (typeof olawRefreshSearchCaptcha === "undefined") {
        function olawRefreshSearchCaptcha(imgId, btnId) {
            var img = document.getElementById(imgId);
            var btn = document.getElementById(btnId);
            if (btn) { var icon = btn.querySelector("i"); if (icon) icon.classList.add("fa-spin"); }
            fetch("' . $base . 'includes/captcha.php?action=refresh_captcha")
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    if (d && d.status === "success" && d.image && img) {
                        img.src = d.image;
                    }
                })
                .finally(function() {
                    if (btn) { var icon = btn.querySelector("i"); if (icon) icon.classList.remove("fa-spin"); }
                });
        }
    }
    </script>
    ';
    return $html;
}

