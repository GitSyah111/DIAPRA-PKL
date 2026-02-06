<?php
session_start();
include 'database.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'check_username') {
    $username = trim($_POST['username']);

    if (empty($username)) {
        $_SESSION['error'] = 'Username wajib diisi.';
        header('Location: lupa-password.php');
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Username ditemukan
        $_SESSION['reset_username'] = $username;
        header('Location: lupa-password.php?step=2');
        exit;
    } else {
        // Username tidak ditemukan
        $_SESSION['error'] = 'Username tidak ditemukan.';
        header('Location: lupa-password.php');
        exit;
    }
} elseif ($action == 'reset_password') {
    if (!isset($_SESSION['reset_username'])) {
        header('Location: lupa-password.php');
        exit;
    }

    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = 'Password wajib diisi.';
        header('Location: lupa-password.php?step=2');
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Konfirmasi password tidak cocok.';
        header('Location: lupa-password.php?step=2');
        exit;
    }

    if (strlen($password) < 3) {
        $_SESSION['error'] = 'Password minimal 3 karakter.';
        header('Location: lupa-password.php?step=2');
        exit;
    }

    $username = $_SESSION['reset_username'];
    $hashed_password = md5($password);

    $stmt = $conn->prepare("UPDATE user SET password = ? WHERE username = ?");
    $stmt->bind_param('ss', $hashed_password, $username);
    
    if ($stmt->execute()) {
        unset($_SESSION['reset_username']);
        $_SESSION['register_success'] = 'Password berhasil direset. Silakan login.';
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['error'] = 'Gagal mereset password. Silakan coba lagi.';
        header('Location: lupa-password.php?step=2');
        exit;
    }
} else {
    header('Location: login.php');
    exit;
}
?>
