<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
    $_SESSION['login_error'] = 'Silakan login terlebih dahulu.';
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role'];
$nama = $_SESSION['nama'] ?? '';
