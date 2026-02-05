<?php
// File untuk memproses CRUD Surat Cuti
include 'database.php';
require_once 'auth_check.php';

// Cek action dari POST atau GET
if (isset($_POST['action']) || isset($_GET['action'])) {
    // Ambil action dari POST atau GET
    $action = isset($_POST['action']) ? $_POST['action'] : $_GET['action'];

    // TAMBAH DATA SURAT CUTI
    if ($action == 'add') {
        // Escape string untuk keamanan input Nama/NIP
        $nama_nip = mysqli_real_escape_string($conn, $_POST['nama_nip']);
        // Escape string untuk keamanan input Pangkat/GOL RUANG
        $pangkat_gol = mysqli_real_escape_string($conn, $_POST['pangkat_gol']);
        // Escape string untuk keamanan input Jabatan
        $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
        // Escape string untuk keamanan input Jenis Cuti
        $jenis_cuti = mysqli_real_escape_string($conn, $_POST['jenis_cuti']);
        // Escape string untuk keamanan input Lamanya
        $lamanya = mysqli_real_escape_string($conn, $_POST['lamanya']);
        // Escape string untuk keamanan input Dilaksanakan DI
        $dilaksanakan_di = mysqli_real_escape_string($conn, $_POST['dilaksanakan_di']);
        // Ambil tanggal mulai cuti dari POST
        $mulai_cuti_date = $_POST['mulai_cuti'];
        // Konversi tanggal mulai cuti ke timestamp Unix
        $mulai_cuti = strtotime($mulai_cuti_date);
        // Ambil tanggal sampai dengan dari POST
        $sampai_dengan_date = $_POST['sampai_dengan'];
        // Konversi tanggal sampai dengan ke timestamp Unix
        $sampai_dengan = strtotime($sampai_dengan_date);
        // Escape string untuk keamanan input Sisa Cuti
        $sisa_cuti = mysqli_real_escape_string($conn, $_POST['sisa_cuti']);

        // Query insert ke database dengan kolom menggunakan backticks
        $query = "INSERT INTO `surat cuti` 
                  (`Nama/NIP`, `Pangkat/GOL RUANG`, `Jabatan`, `Jenis Cuti`, `Lamanya`, `Dilaksanakan DI`, `Mulai Cuti`, `Sampai Dengan`, `Sisa Cuti`) 
                  VALUES 
                  ('$nama_nip', '$pangkat_gol', '$jabatan', '$jenis_cuti', '$lamanya', '$dilaksanakan_di', '$mulai_cuti', '$sampai_dengan', '$sisa_cuti')";

        // Eksekusi query
        if (mysqli_query($conn, $query)) {
            // Jika berhasil, tampilkan alert dan redirect
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
                        text: 'Data Surat Cuti berhasil ditambahkan!',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        window.location.href = 'surat-cuti.php';
                    });
                </script>
            </body>
            </html>";
        } else {
            // Jika gagal, tampilkan error dan redirect
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
                        window.location.href = 'tambah-surat-cuti.php';
                    });
                </script>
            </body>
            </html>";
        }
    }

    // EDIT DATA SURAT CUTI
    elseif ($action == 'edit') {
        // Ambil ID dari POST
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        // Escape string untuk keamanan input Nama/NIP
        $nama_nip = mysqli_real_escape_string($conn, $_POST['nama_nip']);
        // Escape string untuk keamanan input Pangkat/GOL RUANG
        $pangkat_gol = mysqli_real_escape_string($conn, $_POST['pangkat_gol']);
        // Escape string untuk keamanan input Jabatan
        $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
        // Escape string untuk keamanan input Jenis Cuti
        $jenis_cuti = mysqli_real_escape_string($conn, $_POST['jenis_cuti']);
        // Escape string untuk keamanan input Lamanya
        $lamanya = mysqli_real_escape_string($conn, $_POST['lamanya']);
        // Escape string untuk keamanan input Dilaksanakan DI
        $dilaksanakan_di = mysqli_real_escape_string($conn, $_POST['dilaksanakan_di']);
        // Ambil tanggal mulai cuti dari POST
        $mulai_cuti_date = $_POST['mulai_cuti'];
        // Konversi tanggal mulai cuti ke timestamp Unix
        $mulai_cuti = strtotime($mulai_cuti_date);
        // Ambil tanggal sampai dengan dari POST
        $sampai_dengan_date = $_POST['sampai_dengan'];
        // Konversi tanggal sampai dengan ke timestamp Unix
        $sampai_dengan = strtotime($sampai_dengan_date);
        // Escape string untuk keamanan input Sisa Cuti
        $sisa_cuti = mysqli_real_escape_string($conn, $_POST['sisa_cuti']);

        // Query update ke database dengan kolom menggunakan backticks
        $query = "UPDATE `surat cuti` SET 
                  `Nama/NIP` = '$nama_nip',
                  `Pangkat/GOL RUANG` = '$pangkat_gol',
                  `Jabatan` = '$jabatan',
                  `Jenis Cuti` = '$jenis_cuti',
                  `Lamanya` = '$lamanya',
                  `Dilaksanakan DI` = '$dilaksanakan_di',
                  `Mulai Cuti` = '$mulai_cuti',
                  `Sampai Dengan` = '$sampai_dengan',
                  `Sisa Cuti` = '$sisa_cuti'
                  WHERE id = '$id'";

        // Eksekusi query
        if (mysqli_query($conn, $query)) {
            // Jika berhasil, tampilkan alert dan redirect
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
                        text: 'Data Surat Cuti berhasil diupdate!',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        window.location.href = 'surat-cuti.php';
                    });
                </script>
            </body>
            </html>";
        } else {
            // Jika gagal, tampilkan error dan redirect
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
                        window.location.href = 'edit-surat-cuti.php?id=$id';
                    });
                </script>
            </body>
            </html>";
        }
    }

    // HAPUS DATA SURAT CUTI
    elseif ($action == 'delete') {
        // Ambil ID dari GET
        $id = mysqli_real_escape_string($conn, $_GET['id']);

        // Query delete dari database
        $query = "DELETE FROM `surat cuti` WHERE id = '$id'";

        // Eksekusi query
        if (mysqli_query($conn, $query)) {
            // Jika berhasil, tampilkan alert dan redirect
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
                        text: 'Data Surat Cuti berhasil dihapus!',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        window.location.href = 'surat-cuti.php';
                    });
                </script>
            </body>
            </html>";
        } else {
            // Jika gagal, tampilkan error dan redirect
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
                        window.location.href = 'surat-cuti.php';
                    });
                </script>
            </body>
            </html>";
        }
    }
} else {
    // Jika tidak ada action, redirect ke halaman Surat Cuti
    header("Location: surat-cuti.php");
    exit();
}

// Tutup koneksi database
mysqli_close($conn);
?>