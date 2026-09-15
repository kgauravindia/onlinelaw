<?php
require_once ('includes/function.php');

// --- API Logic ---
if (isset($_REQUEST['api_key']) && isset($_REQUEST['action'])) {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    $api_key_input = trim($_REQUEST['api_key']);
    $action = $_REQUEST['action'];
    $response = ['status' => 'success', 'data' => []];
    
    // Validate API Key against Database
    $safe_key = addslashes($api_key_input);
    $key_res = direct_sql("SELECT * FROM api_keys WHERE api_key = '$safe_key' AND is_active = 1 LIMIT 1");
    
    if ($key_res['count'] == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or inactive API Key']);
        exit;
    }

    $key_data = $key_res['data'][0];
    
    // Check Validity (if set)
    if ($key_data['valid_until'] && strtotime($key_data['valid_until']) < time()) {
        echo json_encode(['status' => 'error', 'message' => 'API Key has expired']);
        exit;
    }

    // Increment Usage Count
    direct_sql("UPDATE api_keys SET usage_count = usage_count + 1 WHERE id = " . $key_data['id'], 'set');

    switch ($action) {
        case 'bank-ifsc':
            $ifsc = isset($_REQUEST['ifsc']) ? strtoupper(trim($_REQUEST['ifsc'])) : '';
            if (!empty($ifsc)) {
                $res = direct_sql("SELECT * FROM bank_ifsc WHERE ifsc = '$ifsc' LIMIT 1");
                if ($res['count'] > 0) {
                    $response['data'] = $res['data'][0];
                } else {
                    $response = ['status' => 'error', 'message' => 'No record found for IFSC: ' . $ifsc];
                }
            } else {
                $response = ['status' => 'error', 'message' => 'IFSC parameter is required'];
            }
            break;

        case 'pincode':
            $pincode = isset($_REQUEST['pincode']) ? trim($_REQUEST['pincode']) : '';
            if (!empty($pincode)) {
                $res = direct_sql("SELECT * FROM pincode WHERE pincode = '$pincode' LIMIT 100");
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
                $res = direct_sql("SELECT * FROM companies_incorporated WHERE cin = '$safe_q' OR cin LIKE '%$safe_q%' OR company_name LIKE '%$safe_q%' LIMIT 50");
                if ($res['count'] > 0) {
                    // Enrich with NIC info
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
            $response = ['status' => 'error', 'message' => 'Invalid action'];
            break;
    }

    echo json_encode($response);
    exit;
}

// --- Internal API Stats Handler ---
if(isset($_REQUEST['check_usage'])) {
    header('Content-Type: application/json');
    $key = trim($_REQUEST['check_usage']);
    $safe_key = addslashes($key);
    $res = direct_sql("SELECT website_name, usage_count, is_active, valid_until FROM api_keys WHERE api_key = '$safe_key' LIMIT 1");
    if($res['count'] > 0) {
        echo json_encode(['status' => 'success', 'data' => $res['data'][0]]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'API Key not found or invalid.']);
    }
    exit;
}

// --- Documentation Data ---
$stats = direct_sql("SELECT COUNT(*) as total_keys, SUM(usage_count) as total_calls FROM api_keys WHERE is_active = 1");
$total_keys = $stats['data'][0]['total_keys'] ?? 0;
$total_calls = $stats['data'][0]['total_calls'] ?? 0;

$page_title = "Developer API Documentation | Bank IFSC & Pincode JSON API India - Online Law";
$meta_desc = "Integrate real-time Bank IFSC, PIN Code, and GST HSN/SAC data into your apps with the Online Law Developer API. Fast, reliable RESTful JSON endpoints for Indian datasets.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tag -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?php echo $meta_desc; ?>">
<meta name="keywords" content="Developer API India, Bank IFSC API JSON, Pincode API for website, GST HSN API service, Indian Financial Data API, Free REST API India, Passport Office API">
<meta name="author" content="OfferPlant">
<title><?php echo $page_title; ?></title>

<?php require_once ('includes/header.php'); ?> 

<!-- Custom Documentation Styles -->
<style>
    :root {
        --doc-bg: #f8fafc;
        --sidebar-bg: #ffffff;
        --code-bg: #1e293b;
        --code-accent: #38bdf8;
    }
    
    .api-container {
        display: flex;
        flex-wrap: wrap;
        margin-top: 40px;
        gap: 30px;
    }
    
    .api-sidebar {
        flex: 0 0 280px;
        position: sticky;
        top: 20px;
        height: min-content;
        background: var(--sidebar-bg);
        border-radius: var(--radius-lg);
        padding: 25px;
        box-shadow: var(--shadow-md);
        border: 1px solid #e2e8f0;
    }
    
    .api-content {
        flex: 1;
        min-width: 300px;
        background: #ffffff;
        border-radius: var(--radius-lg);
        padding: 40px;
        box-shadow: var(--shadow-sm);
        border: 1px solid #e2e8f0;
    }
    
    .sidebar-link {
        display: block;
        padding: 10px 15px;
        color: #64748b;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
        margin-bottom: 5px;
    }
    
    .sidebar-link:hover, .sidebar-link.active {
        background: #f1f5f9;
        color: var(--primary-color);
        text-decoration: none;
    }
    
    .api-section {
        margin-bottom: 60px;
        scroll-margin-top: 40px;
    }
    
    .api-section h2 {
        font-family: var(--font-heading);
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f5f9;
        font-size: 28px;
    }
    
    .method-badge {
        display: inline-block;
        padding: 4px 10px;
        background: var(--primary-color);
        color: white;
        border-radius: 4px;
        font-weight: bold;
        font-size: 12px;
        margin-right: 10px;
    }
    
    .endpoint-url {
        background: #f1f5f9;
        padding: 12px 20px;
        border-radius: 8px;
        font-family: 'Courier New', Courier, monospace;
        font-weight: 600;
        color: #0f172a;
        word-break: break-all;
        margin: 15px 0;
        border: 1px solid #e2e8f0;
    }
    
    .code-block {
        background: var(--code-bg);
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
        position: relative;
        overflow: hidden;
    }
    
    .code-block pre {
        margin: 0;
        color: #f8fafc;
        font-size: 14px;
        line-height: 1.6;
    }
    
    .code-header {
        position: absolute;
        top: 0;
        right: 0;
        padding: 8px 15px;
        background: rgba(255,255,255,0.1);
        color: rgba(255,255,255,0.6);
        font-size: 11px;
        text-transform: uppercase;
        border-bottom-left-radius: 8px;
    }
    
    .param-table {
        width: 100%;
        margin: 20px 0;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .param-table th {
        background: #f8fafc;
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .param-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #f1f5f9;
        color: #64748b;
        font-size: 14px;
    }
    
    .param-name {
        font-family: monospace;
        color: var(--primary-color);
        font-weight: 700;
    }
    
    .param-required {
        color: #ef4444;
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    @media (max-width: 991px) {
        .api-sidebar {
            flex: 1 1 100%;
            position: relative;
            top: 0;
        }
    }
</style>

<!-- Banner Section Start -->
<section class="bg-img-3 overlay p-0 full-row" style="padding: 20px 0 !important;">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="banner-text text-center">
					<h2 class="text-white banner-tittle position-relative mb-1" style="font-family: var(--font-heading) !important; font-size: 32px; margin: 0;">Developer API</h2>
					<ul class="list-inline mt-2 mb-0" style="color: #cbd5e1; font-family: var(--font-body); font-size: 15px;">
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item" style="color: #fff; font-weight: 500;">API Documentation</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Banner Section End --> 

<div class="full-row bg-body py-80" style="padding: 60px 0; background-color: #f8fafc;">
    <div class="container">
        <div class="api-container">
            <!-- Sidebar Navigation -->
            <aside class="api-sidebar">
                <h5 class="mb-4" style="font-weight: 800; color: var(--text-main);">Documentation</h5>
                <a href="#introduction" class="sidebar-link active">Introduction</a>
                <a href="#authentication" class="sidebar-link">Authentication</a>
                <a href="#bank-ifsc" class="sidebar-link">Bank IFSC API</a>
                <a href="#pincode" class="sidebar-link">Pincode API</a>
                <a href="#hsn-sac" class="sidebar-link">HSN/SAC API</a>
                <a href="#passport" class="sidebar-link">Passport API</a>
                <a href="#integration" class="sidebar-link">Integration (WP/JS)</a>
                <a href="#errors" class="sidebar-link">Error Codes</a>
                
                <div class="mt-5 p-3 rounded" style="background: rgba(var(--primary-rgb), 0.05); border: 1px dashed rgba(var(--primary-rgb), 0.2);">
                    <p class="small mb-0 text-muted">Need a custom API? Contact us at <a href="mailto:<?php echo $inst_email; ?>" class="text-primary"><?php echo $inst_email; ?></a></p>
                </div>
            </aside>
            
            <!-- Main Content -->
            <main class="api-content">
                <!-- Introduction -->
                <section id="introduction" class="api-section">
                    <h2>Introduction</h2>
                    <p class="lead" style="color: #64748b; font-size: 18px; line-height: 1.6;">
                        Welcome to the <strong>Online Law REST API</strong>. Our API provides developers with programmatic access to comprehensive datasets including Bank IFSC codes, Indian Post Office Pincodes, and GST HSN/SAC classifications.
                    </p>
                    
                    <!-- Live Stats Dashboard -->
                    <div class="row mt-4 mb-5">
                        <div class="col-md-6 mb-3">
                            <div class="p-4 text-center" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: 16px; border: 1px solid #bfdbfe;">
                                <div style="font-size: 32px; font-weight: 800; color: #1e40af;"><?php echo number_format($total_calls); ?>+</div>
                                <div style="font-size: 14px; font-weight: 600; color: #60a5fa; text-transform: uppercase; letter-spacing: 1px;">Requests Served</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-4 text-center" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 16px; border: 1px solid #bbf7d0;">
                                <div style="font-size: 32px; font-weight: 800; color: #166534;"><?php echo number_format($total_keys); ?>+</div>
                                <div style="font-size: 14px; font-weight: 600; color: #4ade80; text-transform: uppercase; letter-spacing: 1px;">Active Developers</div>
                            </div>
                        </div>
                    </div>

                    <p style="color: #64748b; font-size: 15px; line-height: 1.7;">
                        All API responses are returned in standard JSON format. The API is designed to be easy to integrate into your existing applications, websites, or business workflows.
                    </p>
                    
                    <div class="alert alert-info mt-4" style="border-radius: 12px; border: none; background: #f8fafc; border: 1px solid #e2e8f0; color: #475569;">
                        <i class="fas fa-link mr-2 text-primary"></i> Base Endpoint: <code style="background: #fff; padding: 2px 8px; border-radius: 4px;"><?php echo $base_url; ?>api.php</code>
                    </div>
                </section>
                
                <!-- Authentication -->
                <section id="authentication" class="api-section">
                    <h2>Authentication</h2>
                    <p style="color: #64748b; font-size: 15px; mb-4">To access the API, you must include your API Key in every request as a query parameter. You can track your usage in real-time below.</p>
                    
                    <!-- Usage Checker -->
                    <div class="p-4 mb-4 rounded-lg" style="background: #ffffff; border: 2px solid var(--primary-color); border-style: dashed; border-radius: 16px;">
                        <h6 class="mb-3" style="font-weight: 700;">Check Your API Usage</h6>
                        <div class="row">
                            <div class="col-md-9 mb-2">
                                <input type="text" id="usage_key" class="form-control" style="height: 45px; border-radius: 8px;" placeholder="Enter your API Key here...">
                            </div>
                            <div class="col-md-3">
                                <button onclick="checkUsage()" class="btn btn-primary w-100" style="height: 45px; border-radius: 8px;">Check</button>
                            </div>
                        </div>
                        <div id="usage_result" class="mt-3 small" style="display: none;"></div>
                    </div>

                    <table class="param-table">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Type</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="param-name">api_key</span> <span class="param-required">*</span></td>
                                <td>String</td>
                                <td>Your unique API access key.</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="p-4 rounded-lg" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <span class="text-muted small d-block mb-1">DEFAULT PUBLIC KEY:</span>
                        <code style="font-size: 16px; font-weight: 700; color: #64748b;"><?php echo $api_key; ?></code>
                    </div>
                </section>
                
                <!-- Bank IFSC API -->
                <section id="bank-ifsc" class="api-section">
                    <h2>Bank IFSC API</h2>
                    <p style="color: #64748b;">Retrieve detailed bank branch information using an 11-digit IFSC code.</p>
                    
                    <div class="endpoint-url">
                        <span class="method-badge">GET</span> <?php echo $base_url; ?>api.php?api_key=...&action=bank-ifsc&ifsc=SBIN0000001
                    </div>
                    
                    <h5>Request Parameters</h5>
                    <table class="param-table">
                        <thead>
                            <tr>
                                <th>Param</th>
                                <th>Required</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="param-name">action</span></td>
                                <td>Yes</td>
                                <td>Must be <code>bank-ifsc</code></td>
                            </tr>
                            <tr>
                                <td><span class="param-name">ifsc</span></td>
                                <td>Yes</td>
                                <td>11-Digit Alphanumeric IFSC Code</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <h5>Sample Response</h5>
                    <div class="code-block">
                        <div class="code-header">JSON</div>
                        <pre>
{
  "status": "success",
  "data": {
    "bank": "STATE BANK OF INDIA",
    "ifsc": "SBIN0000001",
    "branch": "ADONI",
    "address": "P.B.NO.20, CHHABI COMPLEX, STATION ROAD, ADONI 518301",
    "city1": "ADONI",
    "city2": "KURNOOL",
    "state": "ANDHRA PRADESH",
    "phone": "220133"
  }
}
                        </pre>
                    </div>
                </section>
                
                <!-- Pincode API -->
                <section id="pincode" class="api-section">
                    <h2>Pincode API</h2>
                    <p style="color: #64748b;">Search for post office details and localities associated with a specific 6-digit Pincode.</p>
                    
                    <div class="endpoint-url">
                        <span class="method-badge">GET</span> <?php echo $base_url; ?>api.php?api_key=...&action=pincode&pincode=841301
                    </div>
                    
                    <h5>Request Parameters</h5>
                    <table class="param-table">
                        <thead>
                            <tr>
                                <th>Param</th>
                                <th>Required</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="param-name">action</span></td>
                                <td>Yes</td>
                                <td>Must be <code>pincode</code></td>
                            </tr>
                            <tr>
                                <td><span class="param-name">pincode</span></td>
                                <td>Yes</td>
                                <td>6-Digit Indian Pincode</td>
                            </tr>
                        </tbody>
                    </table>
                </section>
                
                <!-- HSN API -->
                <section id="hsn-sac" class="api-section">
                    <h2>GST HSN/SAC API</h2>
                    <p style="color: #64748b;">Search for GST Goods (HSN) and Services (SAC) classifications and rates.</p>
                    
                    <div class="endpoint-url">
                        <span class="method-badge">GET</span> <?php echo $base_url; ?>api.php?api_key=...&action=hsn-sac&q=1001
                    </div>
                    
                    <h5>Request Parameters</h5>
                    <table class="param-table">
                        <thead>
                            <tr>
                                <th>Param</th>
                                <th>Required</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="param-name">action</span></td>
                                <td>Yes</td>
                                <td>Must be <code>hsn-sac</code></td>
                            </tr>
                            <tr>
                                <td><span class="param-name">q</span></td>
                                <td>Yes</td>
                                <td>Code or Keyword (e.g. Rice, 1001, Cleaning)</td>
                            </tr>
                        </tbody>
                    </table>
                </section>

                <!-- Passport API -->
                <section id="passport" class="api-section">
                    <h2>Passport Office API</h2>
                    <p style="color: #64748b;">Locate Passport Seva Kendras (PSK), Post Office Passport Seva Kendras (POPSK), and Regional Passport Offices (RPO).</p>
                    
                    <div class="endpoint-url">
                        <span class="method-badge">GET</span> <?php echo $base_url; ?>api.php?api_key=...&action=passport&q=Patna
                    </div>
                    
                    <h5>Request Parameters</h5>
                    <table class="param-table">
                        <thead>
                            <tr>
                                <th>Param</th>
                                <th>Required</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="param-name">action</span></td>
                                <td>Yes</td>
                                <td>Must be <code>passport</code></td>
                            </tr>
                            <tr>
                                <td><span class="param-name">q</span></td>
                                <td>Yes</td>
                                <td>Office Name, RPO, or State (e.g. Patna, Bihar, PSK)</td>
                            </tr>
                        </tbody>
                    </table>

                    <h5>Sample Response</h5>
                    <div class="code-block">
                        <div class="code-header">JSON</div>
                        <pre>
{
  "status": "success",
  "data": [
    {
      "name": "PSK PATNA",
      "rpo": "PATNA",
      "state": "Bihar",
      "address": "Gala Diamond Building, Exhibition Road, Patna",
      "phone": "0612-1234567"
    }
  ]
}
                        </pre>
                    </div>
                </section>
                
                <!-- Integration Examples -->
                <section id="integration" class="api-section">
                    <h2>Integration Examples</h2>
                    <p style="color: #64748b;">Easily integrate our data into your website or WordPress blog using the snippets below.</p>
                    
                    <ul class="nav nav-tabs mb-4" id="apiTabs" role="tablist" style="border-bottom: 2px solid #f1f5f9;">
                        <li class="nav-item">
                            <a class="nav-link active" id="js-tab" data-toggle="tab" href="#js-code" role="tab" style="border: none; font-weight: 600; color: #64748b; padding: 10px 20px;">Vanilla JavaScript (AJAX)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="php-tab" data-toggle="tab" href="#php-code" role="tab" style="border: none; font-weight: 600; color: #64748b; padding: 10px 20px;">PHP (cURL)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="wp-tab" data-toggle="tab" href="#wp-code" role="tab" style="border: none; font-weight: 600; color: #64748b; padding: 10px 20px;">WordPress</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- JS Tab -->
                        <div class="tab-pane fade show active" id="js-code" role="tabpanel">
                            <div class="code-block">
                                <div class="code-header">JavaScript (Fetch)</div>
                                <pre>
const apiKey = 'YOUR_API_KEY';
const ifsc = 'SBIN0000001';

fetch(`<?php echo $base_url; ?>api.php?api_key=${apiKey}&action=pincode&pincode=${pin}`)
  .then(response => response.json())
  .then(data => {
    if(data.status === 'success') {
      console.log('Office Name:', data.data[0].office_name);
      console.log('District:', data.data[0].district_name);
    } else {
      console.error('Error:', data.message);
    }
  });
                                </pre>
                            </div>
                        </div>

                        <!-- PHP Tab -->
                        <div class="tab-pane fade" id="php-code" role="tabpanel">
                            <div class="code-block">
                                <div class="code-header">PHP (cURL)</div>
                                <pre>
&lt;?php
$apiKey = 'YOUR_API_KEY';
$url = "<?php echo $base_url; ?>api.php?api_key=$apiKey&action=pincode&pincode=800001";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if ($data['status'] === 'success') {
    echo "Office: " . $data['data'][0]['office_name'];
}
?&gt;
                                </pre>
                            </div>
                        </div>

                        <!-- WordPress Tab -->
                        <div class="tab-pane fade" id="wp-code" role="tabpanel">
                            <div class="code-block">
                                <div class="code-header">WordPress (wp_remote_get)</div>
                                <pre>
// Add this to your functions.php or a custom plugin
function get_legal_data_from_olaw($pincode) {
    $api_key = 'YOUR_API_KEY';
    $url = "<?php echo $base_url; ?>api.php?api_key=$api_key&action=pincode&pincode=$pincode";
    
    $response = wp_remote_get($url);
    if (is_wp_error($response)) return false;
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    return ($data['status'] === 'success') ? $data['data'] : false;
}
                                </pre>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Errors -->
                <section id="errors" class="api-section">
                    <h2>Error Codes</h2>
                    <p style="color: #64748b;">Our API uses standard response patterns to indicate errors.</p>
                    
                    <div class="code-block">
                        <div class="code-header">Error Example</div>
                        <pre>
{
  "status": "error",
  "message": "Invalid API Key"
}
                        </pre>
                    </div>
                    
                    <table class="param-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Message Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>Invalid API Key</code></td>
                                <td>The provided API key is incorrect or missing.</td>
                            </tr>
                            <tr>
                                <td><code>No record found</code></td>
                                <td>No data matches your query parameters.</td>
                            </tr>
                            <tr>
                                <td><code>Invalid action</code></td>
                                <td>The action parameter doesn't match a supported endpoint.</td>
                            </tr>
                        </tbody>
                    </table>
                </section>
            </main>
        </div>
    </div>
</div>

<?php require_once ('includes/footer.php'); ?> 

<!-- Active Link Highlight -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const links = document.querySelectorAll('.sidebar-link');
        const sections = document.querySelectorAll('.api-section');
        
        function changeActiveLink() {
            let index = sections.length;
            while(--index && window.scrollY + 100 < sections[index].offsetTop) {}
            
            links.forEach((link) => link.classList.remove('active'));
            links[index].classList.add('active');
        }

        changeActiveLink();
        window.addEventListener('scroll', changeActiveLink);
    });

    function checkUsage() {
        const key = document.getElementById('usage_key').value;
        const resultDiv = document.getElementById('usage_result');
        if(!key) return alert('Please enter an API Key');
        
        resultDiv.style.display = 'block';
        resultDiv.innerHTML = '<span class="text-muted">Checking database...</span>';
        
        fetch(`?check_usage=${encodeURIComponent(key)}`)
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    const statusClass = data.data.is_active == 1 ? 'text-success' : 'text-danger';
                    const statusText = data.data.is_active == 1 ? 'Active' : 'Inactive';
                    resultDiv.innerHTML = `
                        <div class="alert mt-2" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="row">
                                <div class="col-6"><strong>Website:</strong><br>${data.data.website_name}</div>
                                <div class="col-6"><strong>Status:</strong><br><span class="${statusClass}">${statusText}</span></div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6"><strong>Total Calls:</strong><br><span class="text-primary font-weight-bold" style="font-size: 18px;">${data.data.usage_count}</span></div>
                                <div class="col-6"><strong>Valid Until:</strong><br>${data.data.valid_until || 'No expiry'}</div>
                            </div>
                        </div>`;
                } else {
                    resultDiv.innerHTML = `<div class="alert alert-danger mt-2">${data.message}</div>`;
                }
            })
            .catch(err => {
                resultDiv.innerHTML = '<div class="alert alert-danger mt-2">Error connecting to server.</div>';
            });
    }
</script>

</body>
</html>
