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
        // Excel serial date number (int or float)
        if (is_numeric($val) && (float)$val > 30000 && (float)$val < 70000) {
            $unixTime = round(((float)$val - 25569) * 86400);
            return gmdate('Y-m-d', $unixTime);
        }
        $ts = strtotime($val);
        if ($ts !== false && $ts > 0) {
            return date('Y-m-d', $ts);
        }
        return null;
    };

    // Helper to derive CIN metadata
    $deriveCinMeta = function($cin, $companyName) {
        $cin = strtoupper(trim((string)$cin));
        $nameUpper = strtoupper(trim((string)$companyName));

        $actCode = '';
        if (preg_match('/^[A-Z](\d{2})/', $cin, $m)) {
            $actCode = $m[1];
        }

        $class = 'Private';
        if (strpos($cin, 'OPC') !== false || strpos($nameUpper, '(OPC)') !== false) {
            $class = 'One Person Company';
        } elseif (strpos($cin, 'PLC') !== false || strpos($cin, 'FLC') !== false || (str_ends_with($nameUpper, 'LIMITED') && !str_ends_with($nameUpper, 'PRIVATE LIMITED') && !str_ends_with($nameUpper, 'PVT LIMITED') && !str_ends_with($nameUpper, 'PVT LTD') && !str_ends_with($nameUpper, 'PVT. LTD.'))) {
            $class = 'Public';
        } elseif (strpos($cin, 'PTC') !== false || strpos($cin, 'FTC') !== false || strpos($cin, 'NPL') !== false) {
            $class = 'Private';
        }

        $type = 'Non-government company';
        if (strpos($cin, 'FTC') !== false || strpos($cin, 'FLC') !== false) {
            $type = 'subsidiary of company incorporated outside India';
        } elseif (strpos($cin, 'SGC') !== false) {
            $type = 'State Government Company';
        } elseif (strpos($cin, 'GOI') !== false) {
            $type = 'Union Government Company';
        }

        return ['activity_code' => $actCode, 'class' => $class, 'company_type' => $type];
    };

    foreach ($sheetMap as $sheetIdx => $category) {
        $sheetXml = $zip->getFromName("xl/worksheets/sheet{$sheetIdx}.xml");
        if (!$sheetXml) continue;

        $xmlReader = new XMLReader();
        $xmlReader->xml($sheetXml);

        $headerFound = false;
        $colMap = [];
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

                // Check and parse header
                if (!$headerFound) {
                    // Search if this row contains CIN or LLPIN or FCIN
                    $hasCinHeader = false;
                    foreach ($rowCells as $colLetter => $cellVal) {
                        $cUpper = strtoupper(trim($cellVal));
                        if (in_array($cUpper, ['CIN', 'LLPIN', 'FCIN', 'CIN/LLPIN', 'CORPORATE IDENTIFICATION NUMBER'])) {
                            $hasCinHeader = true;
                            break;
                        }
                    }

                    if ($hasCinHeader) {
                        $headerFound = true;
                        // Build dynamic column map from header row
                        foreach ($rowCells as $colLetter => $cellVal) {
                            $cUpper = strtoupper(trim($cellVal));
                            if (in_array($cUpper, ['CIN', 'LLPIN', 'FCIN', 'CIN/LLPIN', 'CORPORATE IDENTIFICATION NUMBER'])) {
                                $colMap['cin'] = $colLetter;
                            } elseif (strpos($cUpper, 'NAME') !== false || strpos($cUpper, 'COMPANY') !== false && strpos($cUpper, 'TYPE') === false && strpos($cUpper, 'CLASS') === false) {
                                if (!isset($colMap['company_name'])) {
                                    $colMap['company_name'] = $colLetter;
                                }
                            } elseif (strpos($cUpper, 'DATE') !== false || strpos($cUpper, 'REGISTRATION') !== false || strpos($cUpper, 'INCORPORATION') !== false) {
                                if (!isset($colMap['registration_date'])) {
                                    $colMap['registration_date'] = $colLetter;
                                }
                            } elseif (strpos($cUpper, 'CLASS') !== false) {
                                $colMap['class'] = $colLetter;
                            } elseif (strpos($cUpper, 'TYPE') !== false || strpos($cUpper, 'STATE') !== false) {
                                $colMap['company_type'] = $colLetter;
                            } elseif (strpos($cUpper, 'ACTIVITY') !== false || strpos($cUpper, 'NIC') !== false || strpos($cUpper, 'ROC') !== false) {
                                $colMap['activity_code'] = $colLetter;
                            }
                        }
                    }
                    continue;
                }

                $cinCol = $colMap['cin'] ?? 'A';
                $cin = isset($rowCells[$cinCol]) ? trim($rowCells[$cinCol]) : '';
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

                // Extract fields based on dynamic colMap or sheet category defaults
                if (isset($colMap['company_name'])) {
                    $companyName = trim($rowCells[$colMap['company_name']] ?? '');
                }
                if (isset($colMap['registration_date'])) {
                    $rawDate = trim($rowCells[$colMap['registration_date']] ?? '');
                }
                if (isset($colMap['class'])) {
                    $class = trim($rowCells[$colMap['class']] ?? '');
                }
                if (isset($colMap['company_type'])) {
                    $companyType = trim($rowCells[$colMap['company_type']] ?? '');
                }
                if (isset($colMap['activity_code'])) {
                    $activityCode = trim($rowCells[$colMap['activity_code']] ?? '');
                }

                // Fallbacks if not dynamically mapped
                if (empty($companyName) && empty($rawDate)) {
                    if ($category === 'INDIAN') {
                        $class = isset($rowCells['B']) ? trim($rowCells['B']) : '';
                        $companyName = isset($rowCells['C']) ? trim($rowCells['C']) : '';
                        $rawDate = isset($rowCells['D']) ? trim($rowCells['D']) : '';
                        $companyType = isset($rowCells['E']) ? trim($rowCells['E']) : '';
                        $activityCode = isset($rowCells['F']) ? trim($rowCells['F']) : '';
                    } elseif ($category === 'LLP') {
                        $companyName = isset($rowCells['B']) ? trim($rowCells['B']) : '';
                        $rawDate = isset($rowCells['C']) ? trim($rowCells['C']) : '';
                        $activityCode = isset($rowCells['D']) ? trim($rowCells['D']) : '';
                        $class = 'LLP';
                        $companyType = 'Limited Liability Partnership';
                    } elseif ($category === 'FOREIGN') {
                        $companyName = isset($rowCells['B']) ? trim($rowCells['B']) : '';
                        $rawDate = isset($rowCells['C']) ? trim($rowCells['C']) : '';
                        $activityCode = isset($rowCells['D']) ? trim($rowCells['D']) : '';
                        $class = 'Foreign';
                        $companyType = 'Foreign Company';
                    }
                }

                // Auto-Correction Safeguard:
                // Check if company_name is actually a date/serial and class has the company name
                $isNameDate = preg_match('/^[0-9]{1,2}[-\/][0-9]{1,2}[-\/][0-9]{2,4}$/', $companyName) ||
                              preg_match('/^[0-9]{4}[-\/][0-9]{1,2}[-\/][0-9]{1,2}$/', $companyName) ||
                              (is_numeric($companyName) && (float)$companyName > 30000 && (float)$companyName < 70000);

                if ($isNameDate && !empty($class) && !in_array(strtoupper($class), ['PRIVATE', 'PUBLIC', 'ONE PERSON COMPANY', 'LLP', 'FOREIGN'])) {
                    $actualCompName = $class;
                    $rawDate = $companyName;
                    $companyName = $actualCompName;
                    $class = '';
                }

                if (empty($companyName) || stripos($companyName, 'Monthly Company') !== false) {
                    continue;
                }

                $regDate = $parseDate($rawDate);

                // Auto-derive class and activity code for Indian CINs if missing or shifted
                if ($category === 'INDIAN') {
                    $derived = $deriveCinMeta($cin, $companyName);
                    if (empty($class) || in_array(strtoupper($class), ['INDIAN', 'COMPANY', 'NON-GOVERNMENT COMPANY', 'PVT', 'LTD'])) {
                        $class = $derived['class'];
                    }
                    if (empty($companyType) || !str_contains(strtolower($companyType), 'company')) {
                        $companyType = $derived['company_type'];
                    }
                    if (empty($activityCode) || !is_numeric($activityCode)) {
                        $activityCode = $derived['activity_code'];
                    }
                } elseif ($category === 'LLP') {
                    $class = 'LLP';
                    $companyType = 'Limited Liability Partnership';
                } elseif ($category === 'FOREIGN') {
                    $class = 'Foreign';
                    $companyType = 'Foreign Company';
                }

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

/**
 * Fast Excel Importer for MCA Strike Off / Struck Off Company & LLP Reports
 * Updates status to 'STRUCK_OFF' for existing CIN/LLPINs and inserts newly discovered struck-off entities.
 */
function import_mca_struck_off_excel($filePath, $con, $forcedReportMonth = null) {
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
                $val = '';
                if (isset($node->t)) {
                    $val = (string)$node->t;
                } elseif (isset($node->r)) {
                    foreach ($node->r as $r) {
                        $val .= (string)$r->t;
                    }
                }
                $sharedStrings[] = $val;
            }
        }
        $xmlReader->close();
    }

    // Extract auto report month from filename if possible (e.g. Company-Report-20260901.xlsx -> 2026-09)
    $autoMonth = $forcedReportMonth;
    if (empty($autoMonth)) {
        $baseName = basename($filePath);
        if (preg_match('/(\d{4})(\d{2})\d{2}/', $baseName, $bm)) {
            $autoMonth = $bm[1] . '-' . $bm[2];
        } else {
            $autoMonth = date('Y-m');
        }
    }

    // Helper to derive CIN metadata
    $deriveCinMeta = function($cin, $companyName) {
        $cin = strtoupper(trim((string)$cin));
        $nameUpper = strtoupper(trim((string)$companyName));

        $actCode = '';
        if (preg_match('/^[A-Z](\d{2})/', $cin, $m)) {
            $actCode = $m[1];
        }

        $class = 'Private';
        if (strpos($cin, 'OPC') !== false || strpos($nameUpper, '(OPC)') !== false) {
            $class = 'One Person Company';
        } elseif (strpos($cin, 'PLC') !== false || strpos($cin, 'FLC') !== false || (str_ends_with($nameUpper, 'LIMITED') && !str_ends_with($nameUpper, 'PRIVATE LIMITED') && !str_ends_with($nameUpper, 'PVT LIMITED') && !str_ends_with($nameUpper, 'PVT LTD') && !str_ends_with($nameUpper, 'PVT. LTD.'))) {
            $class = 'Public';
        } elseif (strpos($cin, 'PTC') !== false || strpos($cin, 'FTC') !== false || strpos($cin, 'NPL') !== false) {
            $class = 'Private';
        }

        $type = 'Non-government company';
        if (strpos($cin, 'FTC') !== false || strpos($cin, 'FLC') !== false) {
            $type = 'subsidiary of company incorporated outside India';
        } elseif (strpos($cin, 'SGC') !== false) {
            $type = 'State Government Company';
        } elseif (strpos($cin, 'GOI') !== false) {
            $type = 'Union Government Company';
        }

        return ['activity_code' => $actCode, 'class' => $class, 'company_type' => $type];
    };

    $stats = [
        'INDIAN' => 0,
        'LLP' => 0,
        'FOREIGN' => 0,
        'total' => 0,
        'inserted' => 0,
        'updated' => 0,
        'errors' => []
    ];

    // Detect sheet count
    $sheetIndex = 1;
    while (true) {
        $sheetXml = $zip->getFromName("xl/worksheets/sheet{$sheetIndex}.xml");
        if (!$sheetXml) {
            break;
        }

        $xmlReader = new XMLReader();
        $xmlReader->xml($sheetXml);

        $headerFound = false;
        $colMap = [];
        $detectedDefaultType = 'INDIAN';
        $batch = [];
        $batchSize = 1000;

        $flushStruckBatch = function(&$batch) use ($con, &$stats) {
            if (empty($batch)) return;

            $valuesSql = [];
            foreach ($batch as $row) {
                $cCategory = mysqli_real_escape_string($con, $row['category']);
                $cCin = mysqli_real_escape_string($con, substr($row['cin'], 0, 50));
                $cName = mysqli_real_escape_string($con, substr($row['company_name'], 0, 255));
                $cClass = mysqli_real_escape_string($con, substr($row['class'], 0, 50));
                $cType = mysqli_real_escape_string($con, substr($row['company_type'], 0, 100));
                $cAct = mysqli_real_escape_string($con, substr($row['activity_code'], 0, 50));
                $cMonth = mysqli_real_escape_string($con, substr($row['report_month'], 0, 20));

                $valuesSql[] = "('$cCategory', '$cCin', '$cName', '$cClass', '$cType', '$cAct', 'STRUCK_OFF', '$cMonth')";
            }

            $sql = "INSERT INTO `companies_incorporated` 
                    (`category`, `cin`, `company_name`, `class`, `company_type`, `activity_code`, `status`, `report_month`) 
                    VALUES " . implode(",\n", $valuesSql) . "
                    ON DUPLICATE KEY UPDATE 
                        `status` = 'STRUCK_OFF',
                        `company_name` = IF(VALUES(`company_name`) != '' AND (`company_name` = '' OR `company_name` IS NULL), VALUES(`company_name`), `company_name`),
                        `category` = IF(VALUES(`category`) != '' AND (`category` IS NULL OR `category` = ''), VALUES(`category`), `category`),
                        `class` = IF(VALUES(`class`) != '' AND (`class` IS NULL OR `class` = ''), VALUES(`class`), `class`),
                        `company_type` = IF(VALUES(`company_type`) != '' AND (`company_type` IS NULL OR `company_type` = ''), VALUES(`company_type`), `company_type`),
                        `activity_code` = IF(VALUES(`activity_code`) != '' AND (`activity_code` IS NULL OR `activity_code` = ''), VALUES(`activity_code`), `activity_code`),
                        `updated_at` = CURRENT_TIMESTAMP";

            if (!mysqli_query($con, $sql)) {
                $stats['errors'][] = "MySQL Error on struck off batch: " . mysqli_error($con);
            } else {
                $affected = mysqli_affected_rows($con);
                $batchCount = count($batch);
                $stats['total'] += $batchCount;
                
                // Track category breakdown
                foreach ($batch as $bRow) {
                    $cCat = $bRow['category'];
                    if (isset($stats[$cCat])) {
                        $stats[$cCat]++;
                    }
                }
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

                // Check title or header
                if (!$headerFound) {
                    // Check if title mentions LLP or Company or date
                    foreach ($rowCells as $cVal) {
                        $uVal = strtoupper(trim($cVal));
                        if (strpos($uVal, 'LLP') !== false) {
                            $detectedDefaultType = 'LLP';
                        }
                        if (preg_match('/DATE:\s*(\d{1,2})[-\/]([A-Za-z]{3})[-\/](\d{4})/i', $uVal, $dm) && empty($forcedReportMonth)) {
                            $ts = strtotime($dm[1] . ' ' . $dm[2] . ' ' . $dm[3]);
                            if ($ts) $autoMonth = date('Y-m', $ts);
                        }
                    }

                    // Check if this row is the header row
                    $hasHeader = false;
                    foreach ($rowCells as $colLetter => $cellVal) {
                        $cUpper = strtoupper(trim($cellVal));
                        if (in_array($cUpper, ['CIN', 'LLPIN', 'FCIN', 'CIN/LLPIN', 'CORPORATE IDENTIFICATION NUMBER', 'S.NO', 'SNO', 'SR.NO', 'SR NO'])) {
                            $hasHeader = true;
                            break;
                        }
                    }

                    if ($hasHeader) {
                        $headerFound = true;
                        foreach ($rowCells as $colLetter => $cellVal) {
                            $cUpper = strtoupper(trim($cellVal));
                            if (in_array($cUpper, ['CIN', 'LLPIN', 'FCIN', 'CIN/LLPIN', 'CORPORATE IDENTIFICATION NUMBER'])) {
                                $colMap['cin'] = $colLetter;
                                if (strpos($cUpper, 'LLP') !== false) {
                                    $detectedDefaultType = 'LLP';
                                }
                            } elseif (strpos($cUpper, 'NAME') !== false || strpos($cUpper, 'COMPANY') !== false || strpos($cUpper, 'LLP') !== false) {
                                if (!isset($colMap['company_name'])) {
                                    $colMap['company_name'] = $colLetter;
                                }
                            }
                        }
                    }
                    continue;
                }

                $cinCol = $colMap['cin'] ?? 'B';
                $nameCol = $colMap['company_name'] ?? 'C';

                $cin = isset($rowCells[$cinCol]) ? trim($rowCells[$cinCol]) : '';
                $companyName = isset($rowCells[$nameCol]) ? trim($rowCells[$nameCol]) : '';

                if (empty($cin) || strlen($cin) > 40 || in_array(strtoupper($cin), ['CIN', 'LLPIN', 'FCIN']) || stripos($cin, 'Report') !== false) {
                    continue;
                }

                $cinUpper = strtoupper($cin);

                // Determine category based on CIN structure
                $category = $detectedDefaultType;
                if (str_starts_with($cinUpper, 'U') || str_starts_with($cinUpper, 'L')) {
                    $category = 'INDIAN';
                } elseif (strpos($cinUpper, '-') !== false || str_starts_with($cinUpper, 'AA') || str_starts_with($cinUpper, 'AB') || str_starts_with($cinUpper, 'AC')) {
                    $category = 'LLP';
                } elseif (str_starts_with($cinUpper, 'F')) {
                    $category = 'FOREIGN';
                }

                $class = '';
                $companyType = '';
                $activityCode = '';

                if ($category === 'INDIAN') {
                    $derived = $deriveCinMeta($cinUpper, $companyName);
                    $class = $derived['class'];
                    $companyType = $derived['company_type'];
                    $activityCode = $derived['activity_code'];
                } elseif ($category === 'LLP') {
                    $class = 'LLP';
                    $companyType = 'Limited Liability Partnership';
                } elseif ($category === 'FOREIGN') {
                    $class = 'Foreign';
                    $companyType = 'Foreign Company';
                }

                $batch[] = [
                    'cin' => $cinUpper,
                    'company_name' => $companyName,
                    'category' => $category,
                    'class' => $class,
                    'company_type' => $companyType,
                    'activity_code' => $activityCode,
                    'report_month' => $autoMonth
                ];

                if (count($batch) >= $batchSize) {
                    $flushStruckBatch($batch);
                }
            }
        }

        $flushStruckBatch($batch);
        $xmlReader->close();
        $sheetIndex++;
    }

    $zip->close();
    $stats['duration'] = round(microtime(true) - $timeStart, 2);
    $stats['status'] = empty($stats['errors']) ? 'success' : 'partial';
    $stats['message'] = "Successfully updated {$stats['total']} struck-off records (Indian: " . number_format($stats['INDIAN']) . ", LLP: " . number_format($stats['LLP']) . ") in {$stats['duration']}s.";

    return $stats;
}
?>

