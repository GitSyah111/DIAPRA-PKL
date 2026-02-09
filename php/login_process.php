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

$stmt = $conn->prepare("SELECT no, nama, username, password, role, nama_bidang FROM user WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['login_error'] = 'Username atau password salah.';
    header('Location: login.php');
    exit;
}

$row = $result->fetch_assoc();
// $row already fetched above
$stored_password = $row['password'];

// 1. Cek apakah password di DB sudah MD5 (dan cocok)
if ($stored_password === md5($password)) {
    // Password cocok (MD5)
} 
// 2. Cek apakah password di DB masih Plain Text (dan cocok)
// Kita asumsikan jika belum MD5, mungkin masih plain text lama
elseif ($stored_password === $password) {
    // Password cocok (Plain Text) -> Migrasi ke MD5
    $new_hash = md5($password);
    $update_stmt = $conn->prepare("UPDATE user SET password = ? WHERE no = ?");
    $update_stmt->bind_param('si', $new_hash, $row['no']);
    $update_stmt->execute();
} 
// 3. Salah semua
else {
    $_SESSION['login_error'] = 'Username atau password salah.';
    header('Location: login.php');
    exit;
}

$_SESSION['user_id'] = $row['no'];
$_SESSION['nama'] = $row['nama'];
$_SESSION['username'] = $row['username'];
$_SESSION['role'] = $row['role'];
$_SESSION['nama_bidang'] = $row['nama_bidang'];
header('Location: pilih_tahun.php');
exit;
