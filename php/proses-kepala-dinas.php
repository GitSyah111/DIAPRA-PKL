<?php
// File untuk memproses CRUD Data Kepala Dinas
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
                    text: 'Anda tidak memiliki hak untuk mengubah data kepala dinas.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    window.location.href = 'data-kepala-dinas.php';
                });
            </script>
        </body>
        </html>";
        exit;
    }

    // TAMBAH DATA
    if ($action == 'add') {
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $pangkat = mysqli_real_escape_string($conn, $_POST['pangkat']);
        $NIP = mysqli_real_escape_string($conn, $_POST['NIP']);

        $query = "INSERT INTO kadis (nama, pangkat, NIP) VALUES ('$nama', '$pangkat', '$NIP')";

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
                        text: 'Data berhasil ditambahkan!',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        window.location.href = 'data-kepala-dinas.php';
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
                        window.location.href = 'data-kepala-dinas.php';
                    });
                </script>
            </body>
            </html>";
        }
    }

    // EDIT DATA
    elseif ($action == 'edit') {
        $no = mysqli_real_escape_string($conn, $_POST['no']);
        $nama = mysqli_real_escape_string($conn, $_POST['nama']); 
        $pangkat = mysqli_real_escape_string($conn, $_POST['pangkat']);
        $NIP = mysqli_real_escape_string($conn, $_POST['NIP']);

        $query = "UPDATE kadis SET 
                  nama = '$nama', 
                  pangkat = '$pangkat', 
                  NIP = '$NIP' 
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
                        text: 'Data berhasil diupdate!',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        window.location.href = 'data-kepala-dinas.php';
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
                        window.location.href = 'data-kepala-dinas.php';
                    });
                </script>
            </body>
            </html>";
        }
    }

    // HAPUS DATA
    elseif ($action == 'delete') {
        $no = mysqli_real_escape_string($conn, $_GET['no']);

        $query = "DELETE FROM kadis WHERE no = '$no'";

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
                        text: 'Data berhasil dihapus!',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        window.location.href = 'data-kepala-dinas.php';
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
                        window.location.href = 'data-kepala-dinas.php';
                    });
                </script>
            </body>
            </html>";
        }
    }
} else {
    // Jika tidak ada action, redirect ke halaman data kepala dinas
    header("Location: data-kepala-dinas.php");
    exit();
}

mysqli_close($conn);
