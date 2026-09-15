<?php
/**
 * Fast Excel Importer for MCA Company Incorporation Reports
 * Handles Indian Companies, LLP Companies, and Foreign Companies sheets
 */

function import_mca_incorporation_excel($filePath, $con, $forcedReportMonth = null) {
    if (!file_exists($filePath)) {
        return ['status' => 'error', 'message' => 'File does not exist: ' . $filePath];
    }

    $timeStart = microtime(true);
    $zip = new ZipArchive();
    if ($zip->open($filePath) !== TRUE) {
        return ['status' => 'error', 'message' => 'Unable to open Excel file. Please ensure it is a valid .xlsx file.'];
    }

    // 1. Read Shared Strings
    $sharedStrings = [];
    if ($zip->locateName('xl/sharedStrings.xml') !== false) {
        $xmlReader = new XMLReader();
        $xmlReader->xml($zip->getFromName('xl/sharedStrings.xml'));
        while ($xmlReader->read()) {
            if ($xmlReader->nodeType == XMLReader::ELEMENT && $xmlReader->name == 'si') {
                $node = new SimpleXMLElement($xmlReader->readOuterXML());
                if (isset($node->t)) {
                    $sharedStrings[] = (string)$node->t;
                } elseif (isset($node->r)) {
                    $t = '';
                    foreach ($node->r as $r) {
                        $t .= (string)$r->t;
                    }
                    $sharedStrings[] = $t;
                } else {
                    $sharedStrings[] = '';
                }
            }
        }
        $xmlReader->close();
    }

    // Map sheet index or sheet names
    $sheetMap = [
        1 => 'INDIAN',
        2 => 'LLP',
        3 => 'FOREIGN'
    ];

    $stats = [
        'INDIAN' => 0,
        'LLP' => 0,
        'FOREIGN' => 0,
        'total' => 0,
        'errors' => []
    ];

    // Helper for date normalization
    $parseDate = function($val) {
        $val = trim((string)$val);
        if (empty($val)) return null;
        
        // Check DD-MM-YYYY or DD/MM/YYYY
        if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/', $val, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }
        // Check YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) {
            return $val;
        }
        // Excel serial date number
        if (is_numeric($val) && (int)$val > 30000 && (int)$val < 60000) {
            $unixTime = ((int)$val - 25569) * 86400;
            return date('Y-m-d', $unixTime);
        }
        $ts = strtotime($val);
        if ($ts !== false && $ts > 0) {
            return date('Y-m-d', $ts);
        }
        return null;
    };

    foreach ($sheetMap as $sheetIdx => $category) {
        $sheetXml = $zip->getFromName("xl/worksheets/sheet{$sheetIdx}.xml");
        if (!$sheetXml) continue;

        $xmlReader = new XMLReader();
        $xmlReader->xml($sheetXml);

        $headerFound = false;
        $batch = [];
        $batchSize = 1000;

        $flushBatch = function(&$batch, $category) use ($con, &$stats) {
            if (empty($batch)) return;
            $valuesSql = [];
            foreach ($batch as $row) {
                $cCategory = mysqli_real_escape_string($con, $category);
                $cCin = mysqli_real_escape_string($con, substr($row['cin'], 0, 50));
                $cName = mysqli_real_escape_string($con, substr($row['company_name'], 0, 255));
                $cClass = mysqli_real_escape_string($con, substr($row['class'], 0, 50));
                $cType = mysqli_real_escape_string($con, substr($row['company_type'], 0, 100));
                $cAct = mysqli_real_escape_string($con, substr($row['activity_code'], 0, 50));
                $cDate = $row['registration_date'] ? "'" . mysqli_real_escape_string($con, $row['registration_date']) . "'" : "NULL";
                $cMonth = mysqli_real_escape_string($con, substr($row['report_month'], 0, 20));

                $valuesSql[] = "('$cCategory', '$cCin', '$cName', '$cClass', '$cType', '$cAct', $cDate, '$cMonth')";
            }

            $sql = "INSERT INTO `companies_incorporated` 
                    (`category`, `cin`, `company_name`, `class`, `company_type`, `activity_code`, `registration_date`, `report_month`) 
                    VALUES " . implode(",\n", $valuesSql) . "
                    ON DUPLICATE KEY UPDATE 
                        `category` = VALUES(`category`),
                        `company_name` = VALUES(`company_name`),
                        `class` = VALUES(`class`),
                        `company_type` = VALUES(`company_type`),
                        `activity_code` = VALUES(`activity_code`),
                        `registration_date` = VALUES(`registration_date`),
                        `report_month` = VALUES(`report_month`),
                        `updated_at` = CURRENT_TIMESTAMP";

            if (!mysqli_query($con, $sql)) {
                $stats['errors'][] = "MySQL Error on sheet $category: " . mysqli_error($con);
            } else {
                $count = count($batch);
                $stats[$category] += $count;
                $stats['total'] += $count;
            }
            $batch = [];
        };

        while ($xmlReader->read()) {
            if ($xmlReader->nodeType == XMLReader::ELEMENT && $xmlReader->name == 'row') {
                $rowXml = $xmlReader->readOuterXML();
                $rowNode = simplexml_load_string($rowXml);

                $rowCells = [];
                foreach ($rowNode->c as $c) {
                    $col = preg_replace('/[0-9]/', '', (string)$c['r']);
                    $val = (string)$c->v;
                    $type = (string)$c['t'];
                    if ($type === 's' && isset($sharedStrings[(int)$val])) {
                        $val = trim($sharedStrings[(int)$val]);
                    }
                    $rowCells[$col] = $val;
                }

                // Check header
                if (!$headerFound) {
                    if (isset($rowCells['A']) && in_array(strtoupper(trim($rowCells['A'])), ['CIN', 'LLPIN', 'FCIN'])) {
                        $headerFound = true;
                    }
                    continue;
                }

                $cin = isset($rowCells['A']) ? trim($rowCells['A']) : '';
                if (empty($cin) || strlen($cin) > 40 || stripos($cin, 'Monthly Company') !== false || stripos($cin, 'incorporation') !== false || stripos($cin, 'report') !== false) {
                    continue;
                }

                if (in_array(strtoupper($cin), ['CIN', 'LLPIN', 'FCIN'])) {
                    continue;
                }

                $companyName = '';
                $class = '';
                $companyType = '';
                $activityCode = '';
                $rawDate = '';

                if ($category === 'INDIAN') {
                    // Col A: CIN, B: Class, C: Company Name, D: Date Of Registration, E: Company Type, F: Activity Code
                    $class = isset($rowCells['B']) ? trim($rowCells['B']) : '';
                    $companyName = isset($rowCells['C']) ? trim($rowCells['C']) : '';
                    $rawDate = isset($rowCells['D']) ? trim($rowCells['D']) : '';
                    $companyType = isset($rowCells['E']) ? trim($rowCells['E']) : '';
                    $activityCode = isset($rowCells['F']) ? trim($rowCells['F']) : '';
                } elseif ($category === 'LLP') {
                    // Col A: LLPIN, B: LLP Name, C: Date of registration, D: Activity
                    $companyName = isset($rowCells['B']) ? trim($rowCells['B']) : '';
                    $rawDate = isset($rowCells['C']) ? trim($rowCells['C']) : '';
                    $activityCode = isset($rowCells['D']) ? trim($rowCells['D']) : '';
                    $class = 'LLP';
                    $companyType = 'Limited Liability Partnership';
                } elseif ($category === 'FOREIGN') {
                    // Col A: FCIN, B: Company Name, C: Date of Registration, D: Activity
                    $companyName = isset($rowCells['B']) ? trim($rowCells['B']) : '';
                    $rawDate = isset($rowCells['C']) ? trim($rowCells['C']) : '';
                    $activityCode = isset($rowCells['D']) ? trim($rowCells['D']) : '';
                    $class = 'Foreign';
                    $companyType = 'Foreign Company';
                }

                if (empty($companyName) || stripos($companyName, 'Monthly Company') !== false) {
                    continue;
                }

                $regDate = $parseDate($rawDate);
                $reportMonth = $forcedReportMonth;
                if (empty($reportMonth)) {
                    if ($regDate) {
                        $reportMonth = date('Y-m', strtotime($regDate));
                    } else {
                        $reportMonth = date('Y-m');
                    }
                }

                $batch[] = [
                    'cin' => $cin,
                    'company_name' => $companyName,
                    'class' => $class,
                    'company_type' => $companyType,
                    'activity_code' => $activityCode,
                    'registration_date' => $regDate,
                    'report_month' => $reportMonth
                ];

                if (count($batch) >= $batchSize) {
                    $flushBatch($batch, $category);
                }
            }
        }

        // Flush remaining
        $flushBatch($batch, $category);
        $xmlReader->close();
    }

    $zip->close();
    $stats['duration'] = round(microtime(true) - $timeStart, 2);
    $stats['status'] = empty($stats['errors']) ? 'success' : 'partial';
    $stats['message'] = "Successfully processed {$stats['total']} companies (Indian: " . number_format($stats['INDIAN']) . ", LLP: " . number_format($stats['LLP']) . ", Foreign: " . number_format($stats['FOREIGN']) . ") in {$stats['duration']}s.";

    return $stats;
}
