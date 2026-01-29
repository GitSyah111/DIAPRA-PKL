<?php
session_start();
include 'database.php';

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = 'Username dan password wajib diisi.';
    header('Location: login.php');
    exit;
}

$stmt = $conn->prepare("SELECT no, nama, username, password, role FROM user WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['login_error'] = 'Username atau password salah.';
    header('Location: login.php');
    exit;
}

$row = $result->fetch_assoc();
// Password disimpan plain text di DB
if ($row['password'] !== $password) {
    $_SESSION['login_error'] = 'Username atau password salah.';
    header('Location: login.php');
    exit;
}

$_SESSION['user_id'] = $row['no'];
$_SESSION['nama'] = $row['nama'];
$_SESSION['username'] = $row['username'];
$_SESSION['role'] = $row['role'];
header('Location: dashboard.php');
exit;
