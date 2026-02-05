<?php
// File untuk memproses CRUD Data Pengguna
include 'database.php';
require_once 'auth_check.php';

// Cek action
if (isset($_POST['action']) || isset($_GET['action'])) {
    $action = isset($_POST['action']) ? $_POST['action'] : $_GET['action'];

    // Admin hanya boleh melihat, tidak boleh CUD
    if ($role === 'admin' && in_array($action, ['add', 'edit', 'delete'])) {
        echo "<!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Processing...</title>
            <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <style>body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }</style>
        </head>
        <body>
            <script>
                Swal.fire({
                    title: 'Akses Ditolak!',
                    text: 'Anda tidak memiliki hak untuk mengubah data pengguna.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    window.location.href = 'data-pengguna.php';
                });
            </script>
        </body>
        </html>";
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
            echo "<!DOCTYPE html>
            <html lang='id'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Processing...</title>
                <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <style>body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }</style>
            </head>
            <body>
                <script>
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Username sudah digunakan! Silakan gunakan username lain.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    }).then((result) => {
                        window.location.href = 'data-pengguna.php';
                    });
                </script>
            </body>
            </html>";
        } else {
            $query = "INSERT INTO user (nama, username, password, role) VALUES ('$nama', '$username', '$password', '$role')";

            if (mysqli_query($conn, $query)) {
                echo "<!DOCTYPE html>
                <html lang='id'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Processing...</title>
                    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
                    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                    <style>body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }</style>
                </head>
                <body>
                    <script>
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Data pengguna berhasil ditambahkan!',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#3085d6'
                        }).then((result) => {
                            window.location.href = 'data-pengguna.php';
                        });
                    </script>
                </body>
                </html>";
            } else {
                echo "<!DOCTYPE html>
                <html lang='id'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Processing...</title>
                    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
                    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                    <style>body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }</style>
                </head>
                <body>
                    <script>
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Error: " . mysqli_error($conn) . "',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        }).then((result) => {
                            window.location.href = 'data-pengguna.php';
                        });
                    </script>
                </body>
                </html>";
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
            echo "<!DOCTYPE html>
            <html lang='id'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Processing...</title>
                <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <style>body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }</style>
            </head>
            <body>
                <script>
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Username sudah digunakan! Silakan gunakan username lain.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#f59e0b'
                    }).then((result) => {
                        window.location.href = 'data-pengguna.php';
                    });
                </script>
            </body>
            </html>";
        } else {
            $query = "UPDATE user SET 
                      nama = '$nama', 
                      username = '$username', 
                      password = '$password',
                      role = '$role'
                      WHERE no = '$no'";

            if (mysqli_query($conn, $query)) {
                echo "<!DOCTYPE html>
                <html lang='id'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Processing...</title>
                    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
                    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                    <style>body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }</style>
                </head>
                <body>
                    <script>
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Data pengguna berhasil diupdate!',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#3085d6'
                        }).then((result) => {
                            window.location.href = 'data-pengguna.php';
                        });
                    </script>
                </body>
                </html>";
            } else {
                echo "<!DOCTYPE html>
                <html lang='id'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Processing...</title>
                    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
                    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                    <style>body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }</style>
                </head>
                <body>
                    <script>
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Error: " . mysqli_error($conn) . "',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        }).then((result) => {
                            window.location.href = 'data-pengguna.php';
                        });
                    </script>
                </body>
                </html>";
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
            echo "<!DOCTYPE html>
            <html lang='id'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Processing...</title>
                <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <style>body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }</style>
            </head>
            <body>
                <script>
                    Swal.fire({
                        title: 'Perhatian!',
                        text: 'Tidak dapat menghapus! Minimal harus ada 1 pengguna.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#f59e0b'
                    }).then((result) => {
                        window.location.href = 'data-pengguna.php';
                    });
                </script>
            </body>
            </html>";
        } else {
            $query = "DELETE FROM user WHERE no = '$no'";

            if (mysqli_query($conn, $query)) {
                echo "<!DOCTYPE html>
                <html lang='id'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Processing...</title>
                    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
                    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                    <style>body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }</style>
                </head>
                <body>
                    <script>
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Data pengguna berhasil dihapus!',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#3085d6'
                        }).then((result) => {
                            window.location.href = 'data-pengguna.php';
                        });
                    </script>
                </body>
                </html>";
            } else {
                echo "<!DOCTYPE html>
                <html lang='id'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Processing...</title>
                    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
                    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                    <style>body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }</style>
                </head>
                <body>
                    <script>
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Error: " . mysqli_error($conn) . "',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        }).then((result) => {
                            window.location.href = 'data-pengguna.php';
                        });
                    </script>
                </body>
                </html>";
            }
        }
    }
} else {
    // Jika tidak ada action, redirect ke halaman data pengguna
    header("Location: data-pengguna.php");
    exit();
}

mysqli_close($conn);
