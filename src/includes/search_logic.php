<?php
require_once '../config/db.php';

// Get inputs safely
$fname = trim($_POST['fname'] ?? '');
$mname = trim($_POST['mname'] ?? '');
$sname = trim($_POST['sname'] ?? '');

// Prevent empty search
if ($fname === '' && $mname === '' && $sname === '') {
    echo "<tr><td colspan='9' class='text-center text-warning'>
            Please enter at least one name to search.
          </td></tr>";
    exit;
}

// Helper function for safe output (prevents NULL + XSS)
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

try {
    $sql = "SELECT 
                patient_id,
                first_name,
                middle_name,
                sur_name,
                sex,
                dob,
                district_name,
                community_name,
                mobile_number
            FROM patients 
            WHERE first_name LIKE :fname
              AND middle_name LIKE :mname
              AND sur_name LIKE :sname
            LIMIT 200";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':fname' => "%$fname%",
        ':mname' => "%$mname%",
        ':sname' => "%$sname%"
    ]);

    $found = false;

    // Stream results
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $found = true;

        echo "<tr>
                <td>" . e($row['patient_id']) . "</td>
                <td>" . e($row['first_name']) . "</td>
                <td>" . e($row['middle_name']) . "</td>
                <td>" . e($row['sur_name']) . "</td>
                <td>" . e($row['sex']) . "</td>
                <td>" . e($row['dob']) . "</td>
                <td>" . e($row['district_name']) . "</td>
                <td>" . e($row['community_name']) . "</td>
                <td>" . e($row['mobile_number']) . "</td>
              </tr>";
    }

    if (!$found) {
        echo "<tr><td colspan='9' class='text-center'>
                No matching records found.
              </td></tr>";
    }

} catch (PDOException $e) {
    // Do NOT expose raw DB errors in production
    error_log($e->getMessage());

    echo "<tr><td colspan='9' class='text-danger text-center'>
            System error. Please try again later.
          </td></tr>";
}