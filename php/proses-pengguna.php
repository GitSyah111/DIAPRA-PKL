<?php
// File untuk memproses CRUD Data Pengguna
include 'database.php';
require_once 'auth_check.php';

// Cek action
if (isset($_POST['action']) || isset($_GET['action'])) {
    $action = isset($_POST['action']) ? $_POST['action'] : $_GET['action'];

    // Admin hanya boleh melihat, tidak boleh CUD
    if ($role === 'admin' && in_array($action, ['add', 'edit', 'delete'])) {
        echo "<script>alert('Anda tidak memiliki hak untuk mengubah data pengguna.'); window.location.href = 'data-pengguna.php';</script>";
        exit;
    }

    // TAMBAH DATA
    if ($action == 'add') {
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $role = mysqli_real_escape_string($conn, $_POST['role']);

        // Cek apakah username sudah ada
        $checkQuery = "SELECT * FROM user WHERE username = '$username'";
        $checkResult = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            echo "<script>
                alert('Username sudah digunakan! Silakan gunakan username lain.');
                window.location.href = 'data-pengguna.php';
            </script>";
        } else {
            $query = "INSERT INTO user (nama, username, password, role) VALUES ('$nama', '$username', '$password', '$role')";

            if (mysqli_query($conn, $query)) {
                echo "<script>
                    alert('Data pengguna berhasil ditambahkan!');
                    window.location.href = 'data-pengguna.php';
                </script>";
            } else {
                echo "<script>
                    alert('Error: " . mysqli_error($conn) . "');
                    window.location.href = 'data-pengguna.php';
                </script>";
            }
        }
    }

    // EDIT DATA
    elseif ($action == 'edit') {
        $no = mysqli_real_escape_string($conn, $_POST['no']);
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $role = mysqli_real_escape_string($conn, $_POST['role']);

        // Cek apakah username sudah digunakan oleh user lain
        $checkQuery = "SELECT * FROM user WHERE username = '$username' AND no != '$no'";
        $checkResult = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            echo "<script>
                alert('Username sudah digunakan! Silakan gunakan username lain.');
                window.location.href = 'data-pengguna.php';
            </script>";
        } else {
            $query = "UPDATE user SET 
                      nama = '$nama', 
                      username = '$username', 
                      password = '$password',
                      role = '$role'
                      WHERE no = '$no'";

            if (mysqli_query($conn, $query)) {
                echo "<script>
                    alert('Data pengguna berhasil diupdate!');
                    window.location.href = 'data-pengguna.php';
                </script>";
            } else {
                echo "<script>
                    alert('Error: " . mysqli_error($conn) . "');
                    window.location.href = 'data-pengguna.php';
                </script>";
            }
        }
    }

    // HAPUS DATA
    elseif ($action == 'delete') {
        $no = mysqli_real_escape_string($conn, $_GET['no']);

        // Cek apakah ini adalah user terakhir atau admin terakhir
        $countQuery = "SELECT COUNT(*) as total FROM user";
        $countResult = mysqli_query($conn, $countQuery);
        $countRow = mysqli_fetch_assoc($countResult);

        if ($countRow['total'] <= 1) {
            echo "<script>
                alert('Tidak dapat menghapus! Minimal harus ada 1 pengguna.');
                window.location.href = 'data-pengguna.php';
            </script>";
        } else {
            $query = "DELETE FROM user WHERE no = '$no'";

            if (mysqli_query($conn, $query)) {
                echo "<script>
                    alert('Data pengguna berhasil dihapus!');
                    window.location.href = 'data-pengguna.php';
                </script>";
            } else {
                echo "<script>
                    alert('Error: " . mysqli_error($conn) . "');
                    window.location.href = 'data-pengguna.php';
                </script>";
            }
        }
    }
} else {
    // Jika tidak ada action, redirect ke halaman data pengguna
    header("Location: data-pengguna.php");
    exit();
}

mysqli_close($conn);
