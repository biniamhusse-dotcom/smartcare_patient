<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php");
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM patients WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php?msg=deleted");
    exit();
} catch (PDOException $e) {
    error_log($e->getMessage());
    header("Location: index.php?msg=error");
    exit();
}
