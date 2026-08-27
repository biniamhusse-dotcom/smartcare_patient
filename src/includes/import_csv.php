<?php
// 1. Remove the time limit for this script (0 = infinite)
set_time_limit(0);
// 2. Increase memory limit for large files
ini_set('memory_limit', '512M');

require_once '../config/db.php';

if (isset($_POST['importSubmit'])) {
    session_start();
    $isAdmin = isset($_SESSION['admin_id']);
    $redirectBase = $isAdmin ? '../admin/import.php' : '../index.php';
    $redirectSuccess = $redirectBase . '?status=success';
    $redirectInvalid = $redirectBase . '?status=invalid_file';

    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

    if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)) {

        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
            
            // Skip the first line (headers)
            fgetcsv($csvFile);

            try {
                // 3. START TRANSACTION (This is the secret to speed)
                $pdo->beginTransaction();

                $sql = "INSERT INTO patients (patient_id, first_name, middle_name, sur_name, sex, dob, district_name, community_name, mobile_number) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);

                while (($line = fgetcsv($csvFile)) !== FALSE) {
                    // Check if the line is empty to avoid errors
                    if(empty($line[0]) && count($line) < 2) continue;

                    $patient_id    = $line[1] ?? '';
                    $first_name    = strtoupper($line[2] ?? '');
                    $middle_name   = strtoupper($line[3] ?? '');
                    $sur_name      = strtoupper($line[4] ?? '');
                    $sex           = $line[5] ?? '';
                    
                    // Date formatting logic
                    $dob_raw       = $line[6] ?? '';
                    $dob           = null;
                    if(!empty($dob_raw)){
                        $dateObj = DateTime::createFromFormat('m/d/Y', $dob_raw);
                        $dob = ($dateObj) ? $dateObj->format('Y-m-d') : null;
                    }

                    $district      = $line[7] ?? '';
                    $community     = $line[8] ?? '';
                    $mobile        = $line[9] ?? '';

                    $stmt->execute([$patient_id, $first_name, $middle_name, $sur_name, $sex, $dob, $district, $community, $mobile]);
                }

                // 4. COMMIT ALL CHANGES AT ONCE
                $pdo->commit();
                fclose($csvFile);
                
                header("Location: $redirectSuccess");
                exit();

            } catch (Exception $e) {
                // If anything goes wrong, undo everything
                $pdo->rollBack();
                die("Error during import: " . $e->getMessage());
            }
        }
    } else {
        header("Location: $redirectInvalid");
    }
}