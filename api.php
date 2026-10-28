<?php
define('REST_API', true);
/**
 * Online Law - Developer API Hub & Documentation
 * RESTful JSON APIs for Bank IFSC, MICR, Pincode, GST HSN/SAC, MCA Companies, and NIC Codes
 */
require_once ('includes/function.php');

// --- API Logic with Access Control & Security ---
$api_key_input = '';
if (isset($_REQUEST['api_key'])) {
    $api_key_input = trim($_REQUEST['api_key']);
} elseif (isset($_SERVER['HTTP_X_API_KEY'])) {
    $api_key_input = trim($_SERVER['HTTP_X_API_KEY']);
} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $auth_header = trim($_SERVER['HTTP_AUTHORIZATION']);
    if (stripos($auth_header, 'Bearer ') === 0) {
        $api_key_input = trim(substr($auth_header, 7));
    }
} elseif (function_exists('getallheaders')) {
    $all_hdrs = getallheaders();
    if (!empty($all_hdrs['X-API-Key'])) {
        $api_key_input = trim($all_hdrs['X-API-Key']);
    } elseif (!empty($all_hdrs['x-api-key'])) {
        $api_key_input = trim($all_hdrs['x-api-key']);
    } elseif (!empty($all_hdrs['Authorization']) && stripos($all_hdrs['Authorization'], 'Bearer ') === 0) {
        $api_key_input = trim(substr($all_hdrs['Authorization'], 7));
    }
}

if (isset($_REQUEST['action'])) {
    if (empty($api_key_input)) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required API Key. Please provide a valid key via query parameter api_key, X-API-Key header, or Bearer Authorization.'
        ]);
        exit;
    }
}

if (!empty($api_key_input) && isset($_REQUEST['action'])) {
    $api_start_time = microtime(true);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-API-Key');

    // Handle preflight OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    $action = strtolower(trim($_REQUEST['action']));
    $response = ['status' => 'success', 'data' => []];

    // Helper to send API response & log
    $send_api_response = function($status_code, $payload, $key_id = null, $key_str = '') use ($api_start_time, $action) {
        http_response_code($status_code);
        $latency = round((microtime(true) - $api_start_time) * 1000, 2);

        // Extract client IP
        $client_ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $client_ip = trim($parts[0]);
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $client_ip = $_SERVER['HTTP_CLIENT_IP'];
        }

        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250);
        $ref = substr($_SERVER['HTTP_REFERER'] ?? '', 0, 250);
        $safe_ip = addslashes($client_ip);
        $safe_ua = addslashes($ua);
        $safe_ref = addslashes($ref);
        $safe_endpoint = addslashes($action);
        $safe_key_str = addslashes($key_str);
        $key_id_sql = $key_id ? intval($key_id) : "NULL";

        // Log request
        @direct_sql("INSERT INTO api_logs (api_key_id, api_key, endpoint, ip_address, user_agent, referer, status_code, response_time_ms) VALUES ($key_id_sql, '$safe_key_str', '$safe_endpoint', '$safe_ip', '$safe_ua', '$safe_ref', $status_code, $latency)", 'set');

        echo json_encode($payload);
        exit;
    };

    // 1. Validate API Key against Database
    $safe_key = addslashes($api_key_input);
    $key_res = direct_sql("SELECT * FROM api_keys WHERE api_key = '$safe_key' LIMIT 1");
    
    if (empty($key_res['count']) || $key_res['count'] == 0) {
        $send_api_response(401, ['status' => 'error', 'message' => 'Invalid API Key.'], null, $api_key_input);
    }

    $key_data = $key_res['data'][0];
    $key_id = intval($key_data['id']);

    // 2. Check Active Status
    if (empty($key_data['is_active']) || intval($key_data['is_active']) !== 1) {
        $send_api_response(403, ['status' => 'error', 'message' => 'API Key is suspended or inactive.'], $key_id, $api_key_input);
    }

    // 3. Check Validity / Expiry Date (if set)
    if (!empty($key_data['valid_until']) && strtotime($key_data['valid_until'] . ' 23:59:59') < time()) {
        $send_api_response(403, ['status' => 'error', 'message' => 'API Key has expired on ' . date('d M Y', strtotime($key_data['valid_until'])) . '.'], $key_id, $api_key_input);
    }

    // 4. Granular Endpoint Access Control Check
    if (!empty($key_data['allowed_endpoints']) && trim($key_data['allowed_endpoints']) !== '*' && trim($key_data['allowed_endpoints']) !== 'all') {
        $raw_endpoints = trim($key_data['allowed_endpoints']);
        $allowed_list = [];
        if (strpos($raw_endpoints, '[') === 0) {
            $decoded = json_decode($raw_endpoints, true);
            if (is_array($decoded)) {
                $allowed_list = array_map('strtolower', array_map('trim', $decoded));
            }
        }
        if (empty($allowed_list)) {
            $allowed_list = array_map('strtolower', array_map('trim', explode(',', $raw_endpoints)));
        }

        if (!in_array($action, $allowed_list)) {
            $send_api_response(403, [
                'status' => 'error', 
                'message' => "Access denied: Endpoint '{$action}' is not permitted for this API Key."
            ], $key_id, $api_key_input);
        }
    }

    // 5. IP Whitelist Restriction Check
    $client_ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $client_ip = trim($parts[0]);
    }
    if (!empty($key_data['allowed_ips'])) {
        $allowed_ips = array_map('trim', explode(',', $key_data['allowed_ips']));
        $ip_matched = false;
        foreach ($allowed_ips as $allowed_ip) {
            if ($allowed_ip === '*' || $allowed_ip === $client_ip) {
                $ip_matched = true;
                break;
            }
        }
        if (!$ip_matched) {
            $send_api_response(403, [
                'status' => 'error', 
                'message' => "Access denied: Requesting IP address '{$client_ip}' is not in the allowed IP whitelist."
            ], $key_id, $api_key_input);
        }
    }

    // 6. Domain / Origin Whitelist Check
    if (!empty($key_data['allowed_domains'])) {
        $allowed_domains = array_map('strtolower', array_map('trim', explode(',', $key_data['allowed_domains'])));
        $request_origin = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '';
        if (!empty($request_origin)) {
            $host = parse_url($request_origin, PHP_URL_HOST) ?: $request_origin;
            $host = strtolower(trim($host));
            $domain_matched = false;
            foreach ($allowed_domains as $allowed_dom) {
                if ($allowed_dom === '*' || $allowed_dom === $host || strpos($host, $allowed_dom) !== false) {
                    $domain_matched = true;
                    break;
                }
            }
            if (!$domain_matched) {
                $send_api_response(403, [
                    'status' => 'error', 
                    'message' => "Access denied: Origin domain '{$host}' is not authorized."
                ], $key_id, $api_key_input);
            }
        }
    }

    // 7. Rate Limiting Check (Daily Quota & Per-Minute RPM)
    $rate_limit_day = isset($key_data['rate_limit_per_day']) ? intval($key_data['rate_limit_per_day']) : 0;
    if ($rate_limit_day > 0) {
        $today_start = date('Y-m-d 00:00:00');
        $day_calls = direct_sql("SELECT COUNT(*) as total FROM api_logs WHERE api_key_id = $key_id AND created_at >= '$today_start'");
        $today_count = isset($day_calls['data'][0]['total']) ? intval($day_calls['data'][0]['total']) : 0;
        if ($today_count >= $rate_limit_day) {
            $send_api_response(429, [
                'status' => 'error', 
                'message' => "Daily API quota limit exceeded ({$rate_limit_day} requests/day). Quota resets at midnight UTC."
            ], $key_id, $api_key_input);
        }
    }

    $rate_limit_min = isset($key_data['rate_limit_per_min']) ? intval($key_data['rate_limit_per_min']) : 0;
    if ($rate_limit_min > 0) {
        $min_ago = date('Y-m-d H:i:s', time() - 60);
        $min_calls = direct_sql("SELECT COUNT(*) as total FROM api_logs WHERE api_key_id = $key_id AND created_at >= '$min_ago'");
        $min_count = isset($min_calls['data'][0]['total']) ? intval($min_calls['data'][0]['total']) : 0;
        if ($min_count >= $rate_limit_min) {
            $send_api_response(429, [
                'status' => 'error', 
                'message' => "Rate limit exceeded ({$rate_limit_min} requests/minute). Please slow down your requests."
            ], $key_id, $api_key_input);
        }
    }

    // Execute Requested Action
    switch ($action) {
        case 'bank-ifsc':
            $ifsc = isset($_REQUEST['ifsc']) ? strtoupper(trim($_REQUEST['ifsc'])) : '';
            if (!empty($ifsc)) {
                $safe_ifsc = addslashes($ifsc);
                $res = direct_sql("SELECT * FROM bank_ifsc WHERE ifsc = '$safe_ifsc' LIMIT 1");
                if ($res['count'] > 0) {
                    $response['data'] = $res['data'][0];
                } else {
                    $response = ['status' => 'error', 'message' => 'No record found for IFSC: ' . $ifsc];
                }
            } else {
                $response = ['status' => 'error', 'message' => 'IFSC parameter is required'];
            }
            break;

        case 'bank-micr':
            $micr = isset($_REQUEST['micr']) ? trim($_REQUEST['micr']) : '';
            if (!empty($micr)) {
                $safe_micr = addslashes($micr);
                $res = direct_sql("SELECT * FROM bank_ifsc WHERE micr = '$safe_micr' LIMIT 10");
                if ($res['count'] > 0) {
                    $response['data'] = ($res['count'] == 1) ? $res['data'][0] : $res['data'];
                } else {
                    $response = ['status' => 'error', 'message' => 'No record found for MICR: ' . $micr];
                }
            } else {
                $response = ['status' => 'error', 'message' => 'MICR parameter is required'];
            }
            break;

        case 'pincode':
            $pincode = isset($_REQUEST['pincode']) ? trim($_REQUEST['pincode']) : '';
            if (!empty($pincode)) {
                $safe_pin = addslashes($pincode);
                $res = direct_sql("SELECT * FROM pincode WHERE pincode = '$safe_pin' LIMIT 100");
                if ($res['count'] > 0) {
                    $response['data'] = $res['data'];
                } else {
                    $response = ['status' => 'error', 'message' => 'No record found for Pincode: ' . $pincode];
                }
            } else {
                $response = ['status' => 'error', 'message' => 'Pincode parameter is required'];
            }
            break;

        case 'hsn-sac':
            $q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : '';
            if (!empty($q)) {
                $safe_q = addslashes($q);
                $res = direct_sql("SELECT * FROM hsnsac WHERE code LIKE '%$safe_q%' OR description LIKE '%$safe_q%' OR hsn_sac LIKE '%$safe_q%' LIMIT 50");
                if ($res['count'] > 0) {
                    $response['data'] = $res['data'];
                } else {
                    $response = ['status' => 'error', 'message' => 'No record found for query: ' . $q];
                }
            } else {
                $response = ['status' => 'error', 'message' => 'Query parameter (q) is required'];
            }
            break;

        case 'passport':
            $q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : '';
            if (!empty($q)) {
                $safe_q = addslashes($q);
                $res = direct_sql("SELECT * FROM passport WHERE name LIKE '%$safe_q%' OR rpo LIKE '%$safe_q%' OR state LIKE '%$safe_q%' LIMIT 50");
                if ($res['count'] > 0) {
                    $response['data'] = $res['data'];
                } else {
                    $response = ['status' => 'error', 'message' => 'No record found for passport query: ' . $q];
                }
            } else {
                $response = ['status' => 'error', 'message' => 'Query parameter (q) is required for passport search'];
            }
            break;

        case 'company':
            $cin = isset($_REQUEST['cin']) ? trim($_REQUEST['cin']) : '';
            $q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : $cin;
            if (!empty($q)) {
                $safe_q = addslashes($q);
                // Fast unique index seek first if alphanumeric CIN candidate
                $res = null;
                if (strlen($safe_q) >= 8 && preg_match('/^[A-Z0-9_\-]+$/i', $safe_q)) {
                    $res = direct_sql("SELECT * FROM companies_incorporated WHERE cin = '$safe_q' AND (status = 'ACTIVE' OR status IS NULL OR status = '') LIMIT 1");
                }
                if (empty($res['count'])) {
                    $res = direct_sql("SELECT * FROM companies_incorporated WHERE (status = 'ACTIVE' OR status IS NULL OR status = '') AND (cin LIKE '$safe_q%' OR company_name LIKE '%$safe_q%') LIMIT 50");
                }
                if ($res['count'] > 0) {
                    foreach ($res['data'] as &$row) {
                        $row['nic_details'] = get_nic_details($row['cin'] ?: $row['activity_code']);
                    }
                    $response['data'] = $res['data'];
                } else {
                    $response = ['status' => 'error', 'message' => 'No record found for company: ' . $q];
                }
            } else {
                $response = ['status' => 'error', 'message' => 'CIN or query parameter (q) is required'];
            }
            break;

        case 'nic':
            $code = isset($_REQUEST['code']) ? trim($_REQUEST['code']) : '';
            $cin = isset($_REQUEST['cin']) ? trim($_REQUEST['cin']) : '';
            $q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : ($code ?: $cin);
            
            if (!empty($code) || !empty($cin)) {
                $nic_info = get_nic_details($code ?: $cin);
                $response['data'] = $nic_info;
            } elseif (!empty($q)) {
                $safe_q = addslashes($q);
                $level = isset($_REQUEST['level']) ? strtoupper(trim($_REQUEST['level'])) : '';
                $level_sql = !empty($level) ? "AND level = '$level'" : "";
                $res = direct_sql("SELECT code, level, title, description, section_code, division_code FROM `nic` WHERE (code LIKE '%$safe_q%' OR title LIKE '%$safe_q%' OR description LIKE '%$safe_q%') $level_sql ORDER BY CHAR_LENGTH(code) ASC, code ASC LIMIT 50");
                if ($res['count'] > 0) {
                    $response['data'] = $res['data'];
                } else {
                    $response = ['status' => 'error', 'message' => 'No NIC activity code found for query: ' . $q];
                }
            } else {
                $response = ['status' => 'error', 'message' => 'Query parameter (q), code, or cin is required for NIC lookup'];
            }
            break;

        default:
            $send_api_response(400, ['status' => 'error', 'message' => 'Invalid action requested.'], $key_id, $api_key_input);
            break;
    }

    // Update Usage Count & Timestamp on Success
    direct_sql("UPDATE api_keys SET usage_count = usage_count + 1, last_used_at = NOW() WHERE id = " . $key_id, 'set');

    $status_code = ($response['status'] === 'success') ? 200 : 404;
    $send_api_response($status_code, $response, $key_id, $api_key_input);
}

// --- Internal API Stats Handler ---
if (isset($_REQUEST['check_usage'])) {
    header('Content-Type: application/json');
    $key = trim($_REQUEST['check_usage']);
    $safe_key = addslashes($key);
    $res = direct_sql("SELECT website_name, usage_count, is_active, valid_until FROM api_keys WHERE api_key = '$safe_key' LIMIT 1");
    if ($res['count'] > 0) {
        echo json_encode(['status' => 'success', 'data' => $res['data'][0]]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'API Key not found or invalid.']);
    }
    exit;
}

// --- Temporary Public Documentation Restriction ---
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}
$is_admin = (check_admin_auth(false) !== false);

// If public visitor (not logged in as admin), display clean maintenance notice
if (!$is_admin) {
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'maintenance',
            'message' => 'API documentation is temporarily under maintenance. Active API endpoints continue to function normally with valid API keys.'
        ]);
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>API Documentation Under Maintenance - Online Law</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-dark: #090d16;
            --card-dark: #111827;
            --accent-amber: #f59e0b;
            --accent-blue: #3b82f6;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: radial-gradient(circle at 50% 20%, #1e1b4b 0%, #090d16 65%, #030712 100%);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .maintenance-card {
            background: rgba(17, 24, 39, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-top: 3px solid var(--accent-amber);
            border-radius: 20px;
            max-width: 580px;
            width: 100%;
            padding: 44px 36px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        }
        .icon-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            border-radius: 24px;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.2) 0%, rgba(217, 119, 6, 0.05) 100%);
            border: 1px solid rgba(245, 158, 11, 0.35);
            color: var(--accent-amber);
            font-size: 32px;
            margin-bottom: 24px;
            box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.2);
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(245, 158, 11, 0.12);
            color: #fbbf24;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 20px;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }
        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #f59e0b;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 8px #f59e0b;
            animation: pulse 1.8s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 14px;
            letter-spacing: -0.02em;
        }
        p {
            color: var(--text-secondary);
            font-size: 15px;
            line-height: 1.65;
            margin-bottom: 24px;
        }
        .notice-box {
            background: rgba(15, 23, 42, 0.6);
            border: 1px dashed rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            padding: 16px;
            font-size: 13.5px;
            color: #cbd5e1;
            margin-bottom: 30px;
            text-align: left;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .notice-box i {
            color: var(--accent-blue);
            font-size: 16px;
            margin-top: 3px;
            flex-shrink: 0;
        }
        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-primary-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        }
        .btn-primary-home:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-1px);
            color: #fff;
        }
        .btn-secondary-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: #cbd5e1;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.2s ease;
        }
        .btn-secondary-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }
        .brand-footer {
            margin-top: 26px;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="maintenance-card">
        <div class="icon-badge">
            <i class="fas fa-code"></i>
        </div>
        <div>
            <span class="status-pill"><span class="pulse-dot"></span> Under Maintenance</span>
        </div>
        <h1>API Documentation</h1>
        <p>The Developer API portal and interactive testing console are temporarily offline for scheduled system updates and maintenance.</p>
        <div class="notice-box">
            <i class="fas fa-shield-alt"></i>
            <div>
                <strong>Active Endpoints Unaffected:</strong> If your applications already integrate with Online Law via an API Key, programmatic JSON endpoints remain active and fully operational.
            </div>
        </div>
        <div class="actions">
            <a href="<?php echo $base_url; ?>" class="btn-primary-home">
                <i class="fas fa-home"></i> Back to Homepage
            </a>
            <a href="<?php echo $base_url; ?>contact-us.php" class="btn-secondary-link">
                <i class="fas fa-envelope"></i> Contact Support
            </a>
        </div>
        <div class="brand-footer">
            &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($inst_name); ?>. All rights reserved.
        </div>
    </div>
</body>
</html>
<?php
    exit;
}

// --- Documentation Data (Admin View Only) ---
$stats = direct_sql("SELECT COUNT(*) as total_keys, SUM(usage_count) as total_calls FROM api_keys WHERE is_active = 1");
$total_keys = $stats['data'][0]['total_keys'] ?? 0;
$total_calls = $stats['data'][0]['total_calls'] ?? 0;

$page_title = "Developer API Documentation | Bank IFSC, PIN Code & NIC JSON API - Online Law";
$meta_desc = "Integrate real-time Bank IFSC, PIN Code, GST HSN/SAC, MCA Companies, and NIC 2025 activity data into your apps with the Online Law Developer REST API.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo htmlspecialchars($meta_desc); ?>">
    <meta name="keywords" content="Developer API India, Bank IFSC API JSON, Pincode API for website, GST HSN API service, Indian Financial Data API, Free REST API India, Passport Office API, NIC API">
    <meta name="author" content="OfferPlant">
    <title><?php echo htmlspecialchars($page_title); ?></title>

    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-code: 'JetBrains Mono', monospace;
            --code-bg: #0b1120;
        }

        body {
            font-family: var(--font-main) !important;
            background-color: #f8fafc;
            color: #1e293b;
        }

        /* Hero Banner */
        .api-hero-section {
            background-color: #030712 !important;
            background: radial-gradient(135% 100% at 50% 0%, #030712 0%, #0f172a 45%, #1e1b4b 100%) !important;
            padding: 56px 0 65px 0;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .api-glow-orb-1 {
            position: absolute;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.25) 0%, transparent 70%);
            top: -120px;
            left: -80px;
            pointer-events: none;
            filter: blur(55px);
        }
        .api-glow-orb-2 {
            position: absolute;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.22) 0%, transparent 70%);
            bottom: -100px;
            right: -60px;
            pointer-events: none;
            filter: blur(60px);
        }

        .api-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(6, 182, 212, 0.15);
            border: 1px solid rgba(56, 189, 248, 0.45);
            color: #67e8f9;
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.15);
        }

        .hero-title-gradient {
            background: linear-gradient(135deg, #ffffff 0%, #67e8f9 40%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .doc-sidebar {
            position: sticky;
            top: 24px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 22px 18px;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
        }
        .doc-sidebar a {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            color: #475569;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none !important;
            border-radius: 10px;
            transition: all 0.2s ease;
            margin-bottom: 4px;
        }
        .doc-sidebar a:hover {
            background: #f0fdf4;
            color: #059669;
            transform: translateX(3px);
        }
        .doc-sidebar a.active {
            background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }
            color: #2563eb;
        }

        .endpoint-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 28px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
            scroll-margin-top: 30px;
        }

        .method-badge {
            font-family: var(--font-code);
            font-size: 12px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            background: #10b981;
            color: #ffffff;
        }

        .endpoint-url-box {
            background: #0f172a;
            color: #f8fafc;
            padding: 12px 16px;
            border-radius: 10px;
            font-family: var(--font-code);
            font-size: 13.5px;
            margin: 14px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            overflow-x: auto;
        }

        .code-block-box {
            background: #0b1120;
            border: 1px solid #1e293b;
            border-radius: 12px;
            padding: 18px;
            margin: 16px 0;
            position: relative;
            overflow-x: auto;
        }
        .code-block-box pre {
            margin: 0;
            color: #e2e8f0;
            font-family: var(--font-code);
            font-size: 13px;
            line-height: 1.6;
        }

        .copy-btn-api {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e2e8f0;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 12px;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .copy-btn-api:hover {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }
    </style>
</head>
<body>

<?php require_once('includes/header.php'); ?>

<?php if ($is_admin): ?>
<div style="background: linear-gradient(90deg, #92400e, #b45309); color: #ffffff; padding: 12px 20px; font-size: 13.5px; font-weight: 600; text-align: center; border-bottom: 1px solid rgba(0,0,0,0.15); box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
    <i class="fas fa-eye-slash mr-2"></i> <strong>Admin Preview Mode:</strong> Public API documentation is currently hidden under maintenance for regular visitors. You can see this documentation because you are logged in as Administrator.
    <a href="<?php echo $base_url; ?>admin/manage_api_keys.php" style="color: #fef08a; text-decoration: underline; margin-left: 14px; font-weight: 700;">Admin API Dashboard &rarr;</a>
</div>
<?php endif; ?>
<!-- Hero Section -->
<section class="api-hero-section">
    <div class="api-glow-orb-1"></div>
    <div class="api-glow-orb-2"></div>
    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10 col-12">
                <div class="api-badge-pill mb-3">
                    <i class="fas fa-terminal mr-1.5"></i> RESTFUL JSON API &bull; DEVELOPER HUB
                </div>
                <h1 class="hero-title-gradient mb-2 font-weight-bold" style="font-size: clamp(26px, 4vw, 40px); letter-spacing: -0.5px;">
                    Developer API Documentation
                </h1>
                <p class="text-white-50 mb-3" style="font-size: 15.5px; max-width: 720px; margin: 0 auto; line-height: 1.7;">
                    High-performance, secure RESTful JSON endpoints for Indian Bank IFSC/MICR, PIN Codes, GST HSN/SAC, MCA Company Incorporations, and NIC 2025 Activity Codes.
                </p>
                <ul class="list-inline mb-0" style="color: #94a3b8; font-size: 13.5px;">
                    <li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: #cbd5e1; text-decoration: none;"><i class="fas fa-home mr-1"></i> Home</a></li>
                    <li class="list-inline-item mx-2 text-muted">/</li>
                    <li class="list-inline-item text-white font-weight-bold">Developer API</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- API Content Layout -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container">
        <div class="row">
            <!-- Sidebar Links -->
            <div class="col-lg-3 col-12 mb-4 mb-lg-0">
                <div class="doc-sidebar">
                    <div class="text-dark font-weight-bold mb-3 pb-2 border-bottom" style="font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fas fa-book mr-1.5 text-primary"></i> API Overview
                    </div>
                    <a href="#intro" class="active"><i class="fas fa-info-circle mr-1.5"></i> Introduction</a>
                    <a href="#auth"><i class="fas fa-key mr-1.5"></i> Authentication</a>
                    <a href="#bank-ifsc"><i class="fas fa-landmark mr-1.5"></i> Bank IFSC &amp; MICR</a>
                    <a href="#pincode"><i class="fas fa-map-pin mr-1.5"></i> PIN Code API</a>
                    <a href="#hsn-sac"><i class="fas fa-file-invoice-dollar mr-1.5"></i> GST HSN &amp; SAC</a>
                    <a href="#nic"><i class="fas fa-sitemap mr-1.5"></i> NIC 2025 Codes</a>
                    <a href="#company"><i class="fas fa-building mr-1.5"></i> MCA Company Search</a>
                    <a href="#passport"><i class="fas fa-passport mr-1.5"></i> Passport Office API</a>
                    <a href="#errors"><i class="fas fa-exclamation-triangle mr-1.5"></i> Error Codes</a>

                    <div class="mt-4 p-3 rounded" style="background: #eff6ff; border: 1px dashed #bfdbfe;">
                        <div class="small font-weight-bold text-primary mb-1">Live Platform Stats:</div>
                        <div class="small text-muted">&bull; <strong><?php echo number_format($total_calls); ?>+</strong> Requests Served</div>
                        <div class="small text-muted">&bull; <strong><?php echo number_format($total_keys); ?>+</strong> Active Developers</div>
                    </div>
                </div>
            </div>

            <!-- Main Documentation -->
            <div class="col-lg-9 col-12">

                <!-- Section: Introduction -->
                <div class="endpoint-card" id="intro">
                    <h3 class="font-weight-bold text-dark mb-2" style="font-size: 22px;">Introduction</h3>
                    <p style="font-size: 14.5px; color: #475569; line-height: 1.7;">
                        The <strong>Online Law REST API</strong> provides programmatic access to authoritative public datasets across India. Integrate instant Bank branch validation, PIN code resolution, and MCA corporate lookups directly into your onboarding forms, accounting software, or web applications.
                    </p>
                    <div class="endpoint-url-box">
                        <span><strong>Base Endpoint:</strong> <code><?php echo $base_url; ?>api.php</code></span>
                        <button class="copy-btn-api" onclick="copyText('<?php echo $base_url; ?>api.php', this)"><i class="fas fa-copy"></i> Copy</button>
                    </div>
                </div>

                <!-- Section: Authentication -->
                <div class="endpoint-card" id="auth">
                    <h3 class="font-weight-bold text-dark mb-2" style="font-size: 22px;">Authentication</h3>
                    <p style="font-size: 14.5px; color: #475569; line-height: 1.7;">
                        Authenticate your requests by passing your API Key via URL parameter (<code>?api_key=...</code>), Request Header (<code>X-API-Key: ...</code>), or Bearer Authorization header.
                    </p>
                    <div class="p-3 rounded mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <span class="text-muted small d-block mb-1 font-weight-bold text-uppercase"><i class="fas fa-shield-alt text-primary mr-1"></i> API Key Access</span>
                        <p class="mb-0 text-muted" style="font-size: 13.5px; line-height: 1.6;">
                            A valid API key is required for authentication. Replace <code>YOUR_API_KEY</code> with your assigned access key in requests. Contact your system administrator to generate or manage API keys.
                        </p>
                    </div>

                    <!-- Usage Tracker -->
                    <div class="p-3 rounded" style="background: #ffffff; border: 1px solid #cbd5e1;">
                        <h6 class="font-weight-bold mb-2 text-dark" style="font-size: 14px;">Check API Key Quota &amp; Status:</h6>
                        <div class="row">
                            <div class="col-md-9 mb-2 mb-md-0">
                                <input type="text" id="usage_key" class="form-control" style="height: 42px; font-size: 14px;" placeholder="Enter your API Key...">
                            </div>
                            <div class="col-md-3">
                                <button onclick="checkUsage()" class="btn btn-primary w-100 font-weight-bold" style="height: 42px;">Check</button>
                            </div>
                        </div>
                        <div id="usage_result" class="mt-2 small" style="display: none;"></div>
                    </div>
                </div>

                <!-- Section: Bank IFSC -->
                <div class="endpoint-card" id="bank-ifsc">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h3 class="font-weight-bold text-dark mb-0" style="font-size: 20px;">Bank IFSC &amp; MICR Lookup</h3>
                        <span class="method-badge">GET</span>
                    </div>
                    <p class="text-muted small mb-2">Lookup official bank name, branch address, state, district, and MICR code.</p>
                    <div class="endpoint-url-box">
                        <span><?php echo $base_url; ?>api.php?api_key=YOUR_API_KEY&amp;action=bank-ifsc&amp;ifsc=SBIN0000001</span>
                        <button class="copy-btn-api" onclick="copyText('<?php echo $base_url; ?>api.php?api_key=YOUR_API_KEY&action=bank-ifsc&ifsc=SBIN0000001', this)"><i class="fas fa-copy"></i></button>
                    </div>
                    <h6 class="font-weight-bold mt-3 mb-2" style="font-size: 13.5px;">Sample Response:</h6>
                    <div class="code-block-box">
                        <pre>{
  "status": "success",
  "data": {
    "bank": "STATE BANK OF INDIA",
    "ifsc": "SBIN0000001",
    "branch": "ADONI",
    "address": "P.B.NO.20, CHHABI COMPLEX, STATION ROAD, ADONI 518301",
    "city1": "ADONI",
    "city2": "KURNOOL",
    "state": "ANDHRA PRADESH",
    "micr": "518002102"
  }
}</pre>
                    </div>
                </div>

                <!-- Section: PIN Code -->
                <div class="endpoint-card" id="pincode">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h3 class="font-weight-bold text-dark mb-0" style="font-size: 20px;">PIN Code &amp; Post Office API</h3>
                        <span class="method-badge">GET</span>
                    </div>
                    <p class="text-muted small mb-2">Resolve 6-digit Indian PIN codes to post office names, districts, states, and circles.</p>
                    <div class="endpoint-url-box">
                        <span><?php echo $base_url; ?>api.php?api_key=YOUR_API_KEY&amp;action=pincode&amp;pincode=110001</span>
                        <button class="copy-btn-api" onclick="copyText('<?php echo $base_url; ?>api.php?api_key=YOUR_API_KEY&action=pincode&pincode=110001', this)"><i class="fas fa-copy"></i></button>
                    </div>
                </div>

                <!-- Section: GST HSN/SAC -->
                <div class="endpoint-card" id="hsn-sac">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h3 class="font-weight-bold text-dark mb-0" style="font-size: 20px;">GST HSN &amp; SAC Classification</h3>
                        <span class="method-badge">GET</span>
                    </div>
                    <p class="text-muted small mb-2">Search Harmonized System Nomenclature (HSN) and Service Accounting Codes (SAC).</p>
                    <div class="endpoint-url-box">
                        <span><?php echo $base_url; ?>api.php?api_key=YOUR_API_KEY&amp;action=hsn-sac&amp;q=998311</span>
                        <button class="copy-btn-api" onclick="copyText('<?php echo $base_url; ?>api.php?api_key=YOUR_API_KEY&action=hsn-sac&q=998311', this)"><i class="fas fa-copy"></i></button>
                    </div>
                </div>

                <!-- Section: NIC 2025 -->
                <div class="endpoint-card" id="nic">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h3 class="font-weight-bold text-dark mb-0" style="font-size: 20px;">NIC 2025 Activity Code API</h3>
                        <span class="method-badge">GET</span>
                    </div>
                    <p class="text-muted small mb-2">Decode 5-digit National Industrial Classification codes for SPICe+ &amp; Udyam registration.</p>
                    <div class="endpoint-url-box">
                        <span><?php echo $base_url; ?>api.php?api_key=YOUR_API_KEY&amp;action=nic&amp;code=62011</span>
                        <button class="copy-btn-api" onclick="copyText('<?php echo $base_url; ?>api.php?api_key=YOUR_API_KEY&action=nic&code=62011', this)"><i class="fas fa-copy"></i></button>
                    </div>
                </div>

                <!-- Section: Errors -->
                <div class="endpoint-card" id="errors">
                    <h3 class="font-weight-bold text-dark mb-2" style="font-size: 20px;">Standard Error Responses</h3>
                    <p style="font-size: 14px; color: #475569;">The API returns standard HTTP status codes along with JSON error payloads:</p>
                    <div class="table-responsive">
                        <table class="table table-bordered small">
                            <thead style="background: #f1f5f9;">
                                <tr><th>Code</th><th>Reason</th><th>Description</th></tr>
                            </thead>
                            <tbody>
                                <tr><td><code>401</code></td><td>Unauthorized</td><td>Missing or invalid API key</td></tr>
                                <tr><td><code>403</code></td><td>Forbidden</td><td>API key suspended, expired, or unauthorized endpoint/IP</td></tr>
                                <tr><td><code>404</code></td><td>Not Found</td><td>No matching data records found</td></tr>
                                <tr><td><code>429</code></td><td>Too Many Requests</td><td>Exceeded RPM rate limit or daily request quota</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php require_once('includes/footer.php'); ?>

<script>
function copyText(text, btn) {
    if (!text) return;
    navigator.clipboard.writeText(text).then(function() {
        var original = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check text-success"></i>';
        setTimeout(function() { btn.innerHTML = original; }, 1500);
    });
}

function checkUsage() {
    const key = document.getElementById('usage_key').value.trim();
    const resultDiv = document.getElementById('usage_result');
    if (!key) return alert('Please enter an API Key');
    
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '<span class="text-muted">Querying database...</span>';
    
    fetch(`api.php?check_usage=${encodeURIComponent(key)}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                const statusClass = data.data.is_active == 1 ? 'text-success font-weight-bold' : 'text-danger font-weight-bold';
                const statusText = data.data.is_active == 1 ? 'Active' : 'Inactive';
                resultDiv.innerHTML = `
                    <div class="alert mt-2 p-2 border" style="background: #f8fafc;">
                        <div><strong>Website:</strong> ${data.data.website_name || 'Public Sandbox'}</div>
                        <div><strong>Status:</strong> <span class="${statusClass}">${statusText}</span></div>
                        <div><strong>Total Calls:</strong> <span class="text-primary font-weight-bold">${data.data.usage_count}</span></div>
                        <div><strong>Valid Until:</strong> ${data.data.valid_until || 'Lifetime'}</div>
                    </div>`;
            } else {
                resultDiv.innerHTML = `<div class="alert alert-danger mt-2 p-2">${data.message}</div>`;
            }
        })
        .catch(err => {
            resultDiv.innerHTML = '<div class="alert alert-danger mt-2 p-2">Error connecting to server.</div>';
        });
}
</script>
</body>
</html>
