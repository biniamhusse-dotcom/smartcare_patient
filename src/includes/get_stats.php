<?php
header('Content-Type: application/json');
require_once '../config/db.php';

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

    $statsSql = "SELECT 
        COUNT(*) as total,
        IFNULL(SUM(sex = 'M'), 0) as male,
        IFNULL(SUM(sex = 'F'), 0) as female,
        IFNULL(SUM(dob IS NOT NULL AND dob != ''), 0) as with_dob,
        IFNULL(SUM(mobile_number IS NOT NULL AND mobile_number != ''), 0) as with_mobile
        FROM patients $whereClause";
    $statsStmt = $pdo->prepare($statsSql);
    $statsStmt->execute($params);
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'total' => (int)$stats['total'],
        'male' => (int)$stats['male'],
        'female' => (int)$stats['female'],
        'with_dob' => (int)$stats['with_dob'],
        'with_mobile' => (int)$stats['with_mobile']
    ]);

} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['total' => 0, 'male' => 0, 'female' => 0, 'with_dob' => 0, 'with_mobile' => 0]);
}
