<?php
$host = 'db';
$db   = 'boru_meda_hospital';
$user = 'root'; // default xampp user
$pass = 'boru_meda_hospital';     // default xampp password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>