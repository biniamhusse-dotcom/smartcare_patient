<?php
require_once '../config/db.php';

function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

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

try {
    $conditions = [];
    $params = [];

    if ($p_id !== '') { $conditions[] = "patient_id LIKE ?"; $params[] = "%$p_id%"; }
    if ($fname !== '') { $conditions[] = "first_name LIKE ?"; $params[] = "%$fname%"; }
    if ($mname !== '') { $conditions[] = "middle_name LIKE ?"; $params[] = "%$mname%"; }
    if ($sname !== '') { $conditions[] = "sur_name LIKE ?"; $params[] = "%$sname%"; }
    if ($sex !== '') { $conditions[] = "sex = ?"; $params[] = $sex; }
    if ($dob !== '') { $conditions[] = "dob = ?"; $params[] = $dob; }
    if ($district !== '') { $conditions[] = "district_name LIKE ?"; $params[] = "%$district%"; }
    if ($community !== '') { $conditions[] = "community_name LIKE ?"; $params[] = "%$community%"; }
    if ($mobile !== '') { $conditions[] = "mobile_number LIKE ?"; $params[] = "%$mobile%"; }

    $whereClause = $hasFilter ? 'WHERE ' . implode(' AND ', $conditions) : '';

    // Get filtered stats
    $statsSql = "SELECT 
        COUNT(*) as total,
        SUM(sex = 'M') as male,
        SUM(sex = 'F') as female,
        SUM(dob IS NOT NULL AND dob != '') as with_dob,
        SUM(mobile_number IS NOT NULL AND mobile_number != '') as with_mobile
        FROM patients $whereClause";
    $statsStmt = $pdo->prepare($statsSql);
    $statsStmt->execute($params);
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($stats);

} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['total' => 0, 'male' => 0, 'female' => 0, 'with_dob' => 0, 'with_mobile' => 0]);
}
