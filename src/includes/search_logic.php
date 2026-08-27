<?php
require_once '../config/db.php';

function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$p_id      = trim($_POST['p_id'] ?? '');
$fname     = trim($_POST['fname'] ?? '');
$mname     = trim($_POST['mname'] ?? '');
$sname     = trim($_POST['sname'] ?? '');
$sex       = trim($_POST['sex'] ?? '');
$dob       = trim($_POST['dob'] ?? '');
$district  = trim($_POST['district'] ?? '');
$community = trim($_POST['community'] ?? '');
$mobile    = trim($_POST['mobile'] ?? '');

$hasFilter = ($p_id !== '' || $fname !== '' || $mname !== '' || $sname !== '' || 
              $sex !== '' || $dob !== '' || $district !== '' || $community !== '' || $mobile !== '');

if (!$hasFilter) {
    echo "<tr><td colspan='9' class='text-center text-muted py-4'>
            <em>Type in any field to search records.</em>
          </td></tr>";
    exit;
}

try {
    $conditions = [];
    $params = [];

    if ($p_id !== '') {
        $conditions[] = "patient_id LIKE ?";
        $params[] = "%$p_id%";
    }
    if ($fname !== '') {
        $conditions[] = "first_name LIKE ?";
        $params[] = "%$fname%";
    }
    if ($mname !== '') {
        $conditions[] = "middle_name LIKE ?";
        $params[] = "%$mname%";
    }
    if ($sname !== '') {
        $conditions[] = "sur_name LIKE ?";
        $params[] = "%$sname%";
    }
    if ($sex !== '') {
        $conditions[] = "sex = ?";
        $params[] = $sex;
    }
    if ($dob !== '') {
        $conditions[] = "dob = ?";
        $params[] = $dob;
    }
    if ($district !== '') {
        $conditions[] = "district_name LIKE ?";
        $params[] = "%$district%";
    }
    if ($community !== '') {
        $conditions[] = "community_name LIKE ?";
        $params[] = "%$community%";
    }
    if ($mobile !== '') {
        $conditions[] = "mobile_number LIKE ?";
        $params[] = "%$mobile%";
    }

    $whereClause = implode(' OR ', $conditions);

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
            WHERE $whereClause
            LIMIT 200";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $found = false;

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
        echo "<tr><td colspan='9' class='text-center py-3'>
                No matching records found.
              </td></tr>";
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    echo "<tr><td colspan='9' class='text-danger text-center'>
            System error. Please try again later.
          </td></tr>";
}
