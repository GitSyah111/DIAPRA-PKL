<?php
session_start();
require_once 'database.php';

// Check if user is logged in
if (empty($_SESSION['user_id'])) {
    $_SESSION['error_message'] = 'Silakan login terlebih dahulu.';
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate required fields
    if (empty($nama) || empty($username)) {
        $_SESSION['error_message'] = 'Nama dan username harus diisi.';
        header('Location: edit-akun.php');
        exit;
    }

    // Check if username is already taken by another user
    $check_query = "SELECT no FROM user WHERE username = ? AND no != ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "si", $username, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $_SESSION['error_message'] = 'Username sudah digunakan oleh pengguna lain.';
        mysqli_stmt_close($stmt);
        header('Location: edit-akun.php');
        exit;
    }
    mysqli_stmt_close($stmt);

    // Check if user wants to change password
    $update_password = false;
    if (!empty($new_password) || !empty($confirm_password)) {
        // Verify current password
        $verify_query = "SELECT password FROM user WHERE no = ?";
        $stmt = mysqli_prepare($conn, $verify_query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (empty($current_password)) {
            $_SESSION['error_message'] = 'Password saat ini harus diisi untuk mengubah password.';
            header('Location: edit-akun.php');
            exit;
        }

        if (!password_verify($current_password, $user['password'])) {
            $_SESSION['error_message'] = 'Password saat ini tidak sesuai.';
            header('Location: edit-akun.php');
            exit;
        }

        if ($new_password !== $confirm_password) {
            $_SESSION['error_message'] = 'Password baru dan konfirmasi password tidak cocok.';
            header('Location: edit-akun.php');
            exit;
        }

        if (strlen($new_password) < 6) {
            $_SESSION['error_message'] = 'Password baru minimal 6 karakter.';
            header('Location: edit-akun.php');
            exit;
        }

        $update_password = true;
    }

    // Update user data
    if ($update_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE user SET nama = ?, username = ?, password = ? WHERE no = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "sssi", $nama, $username, $hashed_password, $user_id);
    } else {
        $update_query = "UPDATE user SET nama = ?, username = ? WHERE no = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "ssi", $nama, $username, $user_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        // Update session data
        $_SESSION['nama'] = $nama;
        $_SESSION['success_message'] = 'Akun berhasil diperbarui.';
        
        if ($update_password) {
            $_SESSION['success_message'] .= ' Password Anda telah diubah.';
        }
    } else {
        $_SESSION['error_message'] = 'Gagal memperbarui akun: ' . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    header('Location: edit-akun.php');
    exit;
} else {
    // If not POST request, redirect to edit page
    header('Location: edit-akun.php');
    exit;
}
?>
