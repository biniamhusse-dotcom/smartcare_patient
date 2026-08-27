<?php
function getPatientQuery($filters) {
    $sql = " WHERE 1=1";
    $params = [];
    $cols = ['patient_id','first_name','middle_name','surname','sex','district_name','community_name','mobile_phone'];

    foreach ($cols as $col) {
        if (!empty($filters[$col])) {
            $sql .= " AND $col LIKE ?";
            $params[] = "%" . $filters[$col] . "%";
        }
    }
    return [$sql, $params];
}

function getPatients($pdo, $filters, $limit = 100, $offset = 0) {
    list($condition, $params) = getPatientQuery($filters);
    $sql = "SELECT * FROM patients $condition ORDER BY s_no ASC LIMIT $limit OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTotalCount($pdo, $filters) {
    list($condition, $params) = getPatientQuery($filters);
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM patients $condition");
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}