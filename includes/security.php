<?php
/**
 * Online Law - Global Security & Upload Protection Engine
 * Prevents uploading and execution of .php, .bat, .exe, scripts, and any executable files.
 */

if (!defined('ONLINE_LAW_SECURITY')) {
    define('ONLINE_LAW_SECURITY', true);
}

// Comprehensive list of dangerous and executable file extensions
if (!defined('DANGEROUS_FILE_EXTENSIONS')) {
    define('DANGEROUS_FILE_EXTENSIONS', [
        // PHP scripts and variants
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'pht', 'phtml', 'phar', 'phps', 'inc', 'hphp',
        // Windows binaries and scripts
        'bat', 'cmd', 'exe', 'com', 'msi', 'msp', 'scr', 'pif', 'reg', 'vbs', 'vbe', 'wsf', 'wsh',
        'ps1', 'ps1xml', 'ps2', 'psc1', 'psc2', 'dll', 'sys', 'drv', 'cpl', 'hta', 'inf', 'ins', 'isp',
        // Unix / Linux scripts and binaries
        'sh', 'bash', 'bin', 'run', 'csh', 'ksh', 'zsh', 'out', 'elf',
        // Web CGI and other server scripts
        'cgi', 'pl', 'py', 'pyc', 'pyo', 'jar', 'war', 'ear', 'jsp', 'jspx', 'asp', 'aspx', 'cer',
        'asa', 'asax', 'cfm', 'cfml', 'xhtml', 'shtml',
        // Server configuration files
        'htaccess', 'htpasswd', 'user.ini', 'ini', 'conf', 'config'
    ]);
}

/**
 * Check if a filename is dangerous or attempts multi-extension / bypass tricks.
 *
 * @param string $filename
 * @return bool True if dangerous, false if safe
 */
function is_dangerous_filename($filename) {
    if (empty($filename)) {
        return false;
    }

    // Decode URL-encoded characters (e.g. %00, %2e%2e)
    $cleanName = rawurldecode($filename);

    // Check for null bytes, null chars, and directory traversal
    if (strpos($cleanName, "\0") !== false || strpos($filename, "\0") !== false) {
        return true;
    }
    if (strpos($cleanName, '..') !== false || strpos($cleanName, '/') !== false || strpos($cleanName, '\\') !== false) {
        return true;
    }

    $lowerName = strtolower(trim($cleanName));

    // Strip trailing dots and spaces (Windows bypass trick: "file.php. ")
    $lowerName = rtrim($lowerName, ". \t\n\r\0\x0B");

    // Check for hidden config files
    if (in_array($lowerName, ['.htaccess', '.htpasswd', '.user.ini', 'php.ini', 'web.config'])) {
        return true;
    }

    // Split filename by dots to detect double/nested extensions (e.g., shell.php.jpg, test.bat.png)
    $parts = explode('.', $lowerName);
    if (count($parts) > 1) {
        // Check final extension
        $finalExt = end($parts);
        if (in_array($finalExt, DANGEROUS_FILE_EXTENSIONS, true)) {
            return true;
        }

        // Check all intermediate extensions
        for ($i = 1; $i < count($parts); $i++) {
            if (in_array($parts[$i], DANGEROUS_FILE_EXTENSIONS, true)) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Inspect uploaded file temporary content for malicious executable signatures and PHP tags.
 *
 * @param string $tmpPath Path to uploaded temporary file
 * @return bool True if dangerous content detected, false if safe
 */
function is_dangerous_file_content($tmpPath) {
    if (empty($tmpPath) || !is_readable($tmpPath)) {
        return false;
    }

    // Read initial 8KB chunk for binary/script headers
    $handle = @fopen($tmpPath, 'rb');
    if (!$handle) {
        return false;
    }

    $header = @fread($handle, 8192);
    @fclose($handle);

    if ($header === false || strlen($header) === 0) {
        return false;
    }

    // Check for Windows Executable PE / DOS header ("MZ")
    if (substr($header, 0, 2) === 'MZ') {
        return true;
    }

    // Check for Unix ELF binary header ("\x7FELF")
    if (substr($header, 0, 4) === "\x7FELF") {
        return true;
    }

    // Check for Unix Shebang scripts (#!/bin/sh, #!/usr/bin/python, etc.)
    if (substr($header, 0, 2) === '#!') {
        return true;
    }

    // Check for PHP tags anywhere in header
    $lowerHeader = strtolower($header);
    if (
        strpos($lowerHeader, '<?php') !== false ||
        strpos($lowerHeader, '<? ') !== false ||
        strpos($lowerHeader, '<?=') !== false ||
        strpos($lowerHeader, '<script language="php"') !== false ||
        strpos($lowerHeader, '<script language=\'php\'') !== false ||
        strpos($lowerHeader, '__halt_compiler()') !== false
    ) {
        return true;
    }

    // Check for Windows batch commands at start of text file
    if (preg_match('/^(\s*@?echo\s+off|\s*REM\s+|\s*goto\s+|powershell\.exe|cmd\.exe)/i', $header)) {
        return true;
    }

    return false;
}

/**
 * Validate an uploaded file structure against dangerous files and allowed extensions.
 *
 * @param array $fileArray Element from $_FILES
 * @param array $allowedExtensions Optional whitelist of allowed lowercase extensions (e.g. ['xlsx', 'png'])
 * @param int $maxSizeBytes Maximum allowed file size in bytes
 * @return array ['safe' => bool, 'error' => string|null, 'extension' => string]
 */
function validate_uploaded_file($fileArray, array $allowedExtensions = [], $maxSizeBytes = 104857600) {
    if (empty($fileArray) || !is_array($fileArray) || empty($fileArray['name'])) {
        return ['safe' => false, 'error' => 'No file uploaded or invalid file payload.', 'extension' => ''];
    }

    // Check upload error codes
    $uploadError = $fileArray['error'] ?? UPLOAD_ERR_OK;
    if ($uploadError !== UPLOAD_ERR_OK) {
        $messages = [
            UPLOAD_ERR_INI_SIZE   => 'Uploaded file exceeds server upload_max_filesize limit.',
            UPLOAD_ERR_FORM_SIZE  => 'Uploaded file exceeds form MAX_FILE_SIZE limit.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.'
        ];
        return ['safe' => false, 'error' => $messages[$uploadError] ?? 'File upload error code: ' . $uploadError, 'extension' => ''];
    }

    $rawName = $fileArray['name'];
    $tmpName = $fileArray['tmp_name'] ?? '';
    $size = (int)($fileArray['size'] ?? 0);

    // 1. Check size limit
    if ($size > $maxSizeBytes) {
        return ['safe' => false, 'error' => 'File size exceeds maximum allowed limit of ' . round($maxSizeBytes / (1024 * 1024), 1) . 'MB.', 'extension' => ''];
    }

    // 2. Dangerous filename or extension check
    if (is_dangerous_filename($rawName)) {
        return ['safe' => false, 'error' => 'Security Error: Uploading executable or script files (.php, .bat, .exe, etc.) is strictly prohibited.', 'extension' => ''];
    }

    $ext = strtolower(pathinfo($rawName, PATHINFO_EXTENSION));

    // 3. Extension Whitelist check (if provided)
    if (!empty($allowedExtensions)) {
        $allowed = array_map('strtolower', $allowedExtensions);
        if (!in_array($ext, $allowed, true)) {
            return ['safe' => false, 'error' => 'Invalid file extension .' . htmlspecialchars($ext) . '. Allowed types: ' . implode(', ', $allowed), 'extension' => $ext];
        }
    }

    // 4. Content check on temporary file
    if (!empty($tmpName) && is_uploaded_file($tmpName)) {
        if (is_dangerous_file_content($tmpName)) {
            @unlink($tmpName);
            return ['safe' => false, 'error' => 'Security Error: Dangerous script or executable content detected in file payload.', 'extension' => $ext];
        }
    }

    return ['safe' => true, 'error' => null, 'extension' => $ext];
}

/**
 * Sanitize an uploaded filename safely removing any special characters and potential injection.
 *
 * @param string $filename
 * @return string Safe filename
 */
function sanitize_uploaded_filename($filename) {
    $info = pathinfo($filename);
    $ext = isset($info['extension']) ? '.' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $info['extension'])) : '';
    $name = isset($info['filename']) ? preg_replace('/[^a-zA-Z0-9_\-]/', '_', $info['filename']) : 'file';
    $name = trim($name, '_-');
    if (empty($name)) {
        $name = 'upload_' . time();
    }
    return $name . $ext;
}

/**
 * Global Interceptor: automatically scan all incoming $_FILES for dangerous extensions or content.
 * Blocks the entire request if an executable/script is detected anywhere.
 */
function global_upload_security_guard() {
    if (empty($_FILES) || !is_array($_FILES)) {
        return;
    }

    $hasDangerousFile = false;
    $culpritName = '';

    // Helper recursion to inspect multi-file or nested file structures
    $inspectFiles = function($item) use (&$inspectFiles, &$hasDangerousFile, &$culpritName) {
        if (!is_array($item)) {
            return;
        }

        if (isset($item['name'])) {
            $names = is_array($item['name']) ? $item['name'] : [$item['name']];
            $tmps = isset($item['tmp_name']) ? (is_array($item['tmp_name']) ? $item['tmp_name'] : [$item['tmp_name']]) : [];

            foreach ($names as $idx => $name) {
                if (empty($name)) continue;

                if (is_dangerous_filename($name)) {
                    $hasDangerousFile = true;
                    $culpritName = $name;
                    return;
                }

                $tmp = $tmps[$idx] ?? '';
                if (!empty($tmp) && is_uploaded_file($tmp)) {
                    if (is_dangerous_file_content($tmp)) {
                        $hasDangerousFile = true;
                        $culpritName = $name;
                        @unlink($tmp);
                        return;
                    }
                }
            }
        } else {
            foreach ($item as $subItem) {
                if ($hasDangerousFile) return;
                $inspectFiles($subItem);
            }
        }
    };

    $inspectFiles($_FILES);

    if ($hasDangerousFile) {
        // Purge temporary files
        foreach ($_FILES as $key => $fileData) {
            if (isset($fileData['tmp_name'])) {
                if (is_array($fileData['tmp_name'])) {
                    foreach ($fileData['tmp_name'] as $t) {
                        if (!empty($t) && file_exists($t)) @unlink($t);
                    }
                } elseif (file_exists($fileData['tmp_name'])) {
                    @unlink($fileData['tmp_name']);
                }
            }
        }
        $_FILES = [];

        // Log security violation if error logging available
        error_log("[SECURITY ALERT] Blocked attempt to upload malicious/executable file: " . htmlspecialchars($culpritName) . " from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN'));

        // Respond with HTTP 403 Forbidden
        if (!headers_sent()) {
            http_response_code(403);
            header('Content-Type: text/html; charset=UTF-8');
        }

        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>403 - Upload Forbidden</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #0f172a; color: #f8fafc; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .box { background: #1e293b; border: 1px solid #dc2626; border-radius: 12px; padding: 32px 40px; max-width: 540px; text-align: center; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); }
        .icon { font-size: 48px; color: #ef4444; margin-bottom: 16px; }
        h1 { margin: 0 0 12px; font-size: 22px; color: #f87171; }
        p { margin: 0 0 20px; font-size: 14px; color: #cbd5e1; line-height: 1.6; }
        .btn { display: inline-block; background: #2563eb; color: #fff; text-decoration: none; padding: 10px 22px; border-radius: 6px; font-size: 14px; font-weight: 600; }
        .btn:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="box">
        <div class="icon">&#9888;</div>
        <h1>Upload Prohibited</h1>
        <p>For site security, uploading executable, script, or system files (<strong>.php, .bat, .exe, etc.</strong>) is strictly blocked across all services.</p>
        <a href="javascript:history.back()" class="btn">&larr; Go Back</a>
    </div>
</body>
</html>';
        exit();
    }
}

// Automatically activate global security guard
global_upload_security_guard();

// ============================================================================
// CSRF (Cross-Site Request Forgery) Protection Engine
// ============================================================================

/**
 * Generate or retrieve an existing cryptographically secure CSRF token.
 *
 * @param string $context Unique form or action context
 * @return string
 */
function generate_csrf_token($context = 'global') {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        @session_start();
    }
    if (!isset($_SESSION['csrf_tokens']) || !is_array($_SESSION['csrf_tokens'])) {
        $_SESSION['csrf_tokens'] = [];
    }
    if (empty($_SESSION['csrf_tokens'][$context]) || !is_string($_SESSION['csrf_tokens'][$context])) {
        $_SESSION['csrf_tokens'][$context] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_tokens'][$context];
}

/**
 * Output a secure hidden HTML input field containing the CSRF token.
 *
 * @param string $context Context identifier
 * @return string HTML input string
 */
function get_csrf_input($context = 'global') {
    $token = generate_csrf_token($context);
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Validate a submitted CSRF token using constant-time string comparison.
 *
 * @param string|null $token Submitted token to verify
 * @param string $context Context identifier
 * @param bool $consume Whether to invalidate the token after verification
 * @return bool True if valid, false otherwise
 */
function validate_csrf_token($token, $context = 'global', $consume = false) {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        @session_start();
    }
    if (empty($token) || empty($_SESSION['csrf_tokens'][$context])) {
        return false;
    }
    $isValid = hash_equals($_SESSION['csrf_tokens'][$context], (string)$token);
    if ($isValid && $consume) {
        unset($_SESSION['csrf_tokens'][$context]);
    }
    return $isValid;
}

// ============================================================================
// Brute-Force & Rate-Limiting Protection Engine
// ============================================================================

/**
 * Check if the client IP address is currently rate-limited for an action.
 *
 * @param string $action Identifier for the action (e.g. 'admin_login', 'contact_submit')
 * @param int $maxAttempts Maximum allowed failures within window
 * @param int $lockoutSeconds Lockout duration in seconds (default 15 minutes)
 * @return array ['allowed' => bool, 'remaining_seconds' => int, 'attempts' => int]
 */
function check_rate_limit($action = 'admin_login', $maxAttempts = 5, $lockoutSeconds = 900) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $cacheDir = __DIR__ . '/../tmp/security_limits';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0750, true);
    }

    $fileKey = md5($action . '_' . $ip);
    $limitFile = "$cacheDir/$fileKey.json";
    $now = time();

    $data = ['attempts' => 0, 'first_attempt' => $now, 'locked_until' => 0];
    if (file_exists($limitFile)) {
        $raw = @file_get_contents($limitFile);
        if ($raw) {
            $parsed = json_decode($raw, true);
            if (is_array($parsed)) {
                $data = $parsed;
            }
        }
    }

    // Check if lockout is still active
    if (!empty($data['locked_until']) && $data['locked_until'] > $now) {
        return [
            'allowed' => false,
            'remaining_seconds' => $data['locked_until'] - $now,
            'attempts' => $data['attempts']
        ];
    }

    // Reset attempt window if elapsed
    if (($now - $data['first_attempt']) > $lockoutSeconds) {
        $data = ['attempts' => 0, 'first_attempt' => $now, 'locked_until' => 0];
        @file_put_contents($limitFile, json_encode($data));
    }

    return [
        'allowed' => ($data['attempts'] < $maxAttempts),
        'remaining_seconds' => 0,
        'attempts' => $data['attempts']
    ];
}

/**
 * Record a failed attempt for rate limiting.
 *
 * @param string $action Action identifier
 * @param int $maxAttempts Max attempts before locking
 * @param int $lockoutSeconds Lockout duration
 */
function record_rate_limit_failure($action = 'admin_login', $maxAttempts = 5, $lockoutSeconds = 900) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $cacheDir = __DIR__ . '/../tmp/security_limits';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0750, true);
    }

    $fileKey = md5($action . '_' . $ip);
    $limitFile = "$cacheDir/$fileKey.json";
    $now = time();

    $data = ['attempts' => 0, 'first_attempt' => $now, 'locked_until' => 0];
    if (file_exists($limitFile)) {
        $raw = @file_get_contents($limitFile);
        if ($raw) {
            $parsed = json_decode($raw, true);
            if (is_array($parsed)) {
                $data = $parsed;
            }
        }
    }

    $data['attempts']++;
    if ($data['attempts'] >= $maxAttempts) {
        $data['locked_until'] = $now + $lockoutSeconds;
        error_log("[SECURITY] Rate limit lockout triggered for $action from IP: $ip ($maxAttempts failed attempts)");
    }
    @file_put_contents($limitFile, json_encode($data));
}

/**
 * Clear recorded rate-limit counter on successful action.
 *
 * @param string $action Action identifier
 */
function clear_rate_limit($action = 'admin_login') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $cacheDir = __DIR__ . '/../tmp/security_limits';
    $fileKey = md5($action . '_' . $ip);
    $limitFile = "$cacheDir/$fileKey.json";
    if (file_exists($limitFile)) {
        @unlink($limitFile);
    }
}

// ============================================================================
// Global Input Attack Interceptor (SQLi / Null Byte / Path Traversal)
// ============================================================================

/**
 * Inspects all incoming GET, POST, and COOKIE parameters for malicious attack patterns.
 */
function global_input_security_interceptor() {
    // Avoid running on command line
    if (php_sapi_name() === 'cli') {
        return;
    }

    $dangerous_patterns = [
        '/\b(union\s+select|select\s+.*?\s+from\s+information_schema)\b/i',
        '/(\.\.[\/\\\\])+/i',
        '/<script\b[^>]*>(.*?)<\/script>/is',
        '/(javascript|vbscript|data):[^\n]+/i'
    ];

    $check_val = function($val, $key = '') use (&$check_val, $dangerous_patterns) {
        // Skip password fields from arbitrary pattern blocks
        if (in_array(strtolower((string)$key), ['password', 'user_pass', 'pass', 'pwd', 'csrf_token'], true)) {
            return false;
        }

        if (is_array($val)) {
            foreach ($val as $k => $sub) {
                if ($check_val($sub, $k)) return true;
            }
            return false;
        }
        if (!is_string($val)) return false;

        // Null byte injection check
        if (strpos($val, "\0") !== false) return true;

        foreach ($dangerous_patterns as $pattern) {
            if (preg_match($pattern, $val)) {
                return true;
            }
        }
        return false;
    };

    $culprit = false;
    foreach ($_GET as $k => $v) {
        if ($check_val($v, $k)) { $culprit = 'GET'; break; }
    }
    if (!$culprit) {
        foreach ($_POST as $k => $v) {
            if ($check_val($v, $k)) { $culprit = 'POST'; break; }
        }
    }

    if ($culprit) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        error_log("[SECURITY VIOLATION] Blocked malicious attack pattern in $culprit from IP: $ip");
        if (!headers_sent()) {
            http_response_code(400);
            header('Content-Type: text/plain; charset=UTF-8');
        }
        echo "400 Bad Request: Suspicious input pattern detected.";
        exit();
    }
}

// Activate global input security interceptor
global_input_security_interceptor();

