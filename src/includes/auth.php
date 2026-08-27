<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /admin/login.php");
        exit();
    }
}

function getAdminUser() {
    return $_SESSION['admin_username'] ?? null;
}

function logout() {
    session_destroy();
    header("Location: /admin/login.php");
    exit();
}
