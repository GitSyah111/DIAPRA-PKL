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

        // Upload file Surat Cuti
        $file_surat = '';
        if (isset($_FILES['file_surat']) && $_FILES['file_surat']['error'] == 0) {
            $allowed_ext = array('pdf');
            $file_name = $_FILES['file_surat']['name'];
            $file_tmp = $_FILES['file_surat']['tmp_name'];
            $file_size = $_FILES['file_surat']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // Validasi ekstensi
            if (!in_array($file_ext, $allowed_ext)) {
                echo "<script>
                    alert('Error: Hanya file PDF yang diperbolehkan!');
                    window.location.href = 'tambah-surat-cuti.php';
                </script>";
                exit();
            }

            // Validasi ukuran (max 10MB)
            if ($file_size > 10485760) {
                echo "<script>
                    alert('Error: Ukuran file maksimal 10MB!');
                    window.location.href = 'tambah-surat-cuti.php';
                </script>";
                exit();
            }

            // Generate nama file unik
            $new_file_name = 'surat_cuti_' . time() . '_' . uniqid() . '.' . $file_ext;
            $upload_path = '../uploads/surat_cuti/' . $new_file_name;

            // Buat folder jika belum ada
            if (!file_exists('../uploads/surat_cuti/')) {
                mkdir('../uploads/surat_cuti/', 0777, true);
            }

            // Upload file
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $file_surat = $new_file_name;
            } else {
                echo "<script>
                    alert('Error: Gagal mengupload file!');
                    window.location.href = 'tambah-surat-cuti.php';
                </script>";
                exit();
            }
        }

        // Query insert ke database dengan kolom menggunakan backticks
        $query = "INSERT INTO `surat cuti` 
                  (`Nama/NIP`, `Pangkat/GOL RUANG`, `Jabatan`, `Jenis Cuti`, `Lamanya`, `Dilaksanakan DI`, `Mulai Cuti`, `Sampai Dengan`, `Sisa Cuti`, `file_surat`) 
                  VALUES 
                  ('$nama_nip', '$pangkat_gol', '$jabatan', '$jenis_cuti', '$lamanya', '$dilaksanakan_di', '$mulai_cuti', '$sampai_dengan', '$sisa_cuti', '$file_surat')";

        // Eksekusi query
        try {
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
                throw new Exception(mysqli_error($conn));
            }
        } catch (Exception $e) {
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
                        text: 'Error: " . addslashes($e->getMessage()) . "',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    }).then((result) => {
                        window.history.back();
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

        // Ambil data file lama
        $query_old = "SELECT file_surat FROM `surat cuti` WHERE id = '$id'";
        $result_old = mysqli_query($conn, $query_old);
        $old_data = mysqli_fetch_assoc($result_old);
        $file_surat = $old_data['file_surat'];

        // Cek jika ada request hapus file
        if (isset($_POST['delete_file_surat']) && $_POST['delete_file_surat'] == '1') {
            if (!empty($file_surat) && file_exists('../uploads/surat_cuti/' . $file_surat)) {
                unlink('../uploads/surat_cuti/' . $file_surat);
            }
            $file_surat = NULL; // Set null di database
        }

        // Cek apakah ada file baru
        if (isset($_FILES['file_surat']) && $_FILES['file_surat']['error'] == 0) {
            $allowed_ext = array('pdf');
            $file_name = $_FILES['file_surat']['name'];
            $file_tmp = $_FILES['file_surat']['tmp_name'];
            $file_size = $_FILES['file_surat']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // Validasi ekstensi
            if (!in_array($file_ext, $allowed_ext)) {
                echo "<script>
                    alert('Error: Hanya file PDF yang diperbolehkan!');
                    window.location.href = 'edit-surat-cuti.php?id=$id';
                </script>";
                exit();
            }

            // Validasi ukuran
            if ($file_size > 10485760) {
                echo "<script>
                    alert('Error: Ukuran file maksimal 10MB!');
                    window.location.href = 'edit-surat-cuti.php?id=$id';
                </script>";
                exit();
            }

            // Hapus file lama jika ada (dan belum dihapus)
            if (!empty($old_data['file_surat']) && file_exists('../uploads/surat_cuti/' . $old_data['file_surat'])) {
                unlink('../uploads/surat_cuti/' . $old_data['file_surat']);
            }

            // Upload file baru
            $new_file_name = 'surat_cuti_' . time() . '_' . uniqid() . '.' . $file_ext;
            $upload_path = '../uploads/surat_cuti/' . $new_file_name;

            // Buat folder jika belum ada
            if (!file_exists('../uploads/surat_cuti/')) {
                mkdir('../uploads/surat_cuti/', 0777, true);
            }

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $file_surat = $new_file_name;
            }
        }

        // Handle NULL value properly for sql
        $file_update_str = $file_surat ? "'$file_surat'" : "NULL";

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
                  `Sisa Cuti` = '$sisa_cuti',
                  `file_surat` = $file_update_str
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

        // Ambil data file untuk dihapus
        $query_file = "SELECT file_surat FROM `surat cuti` WHERE id = '$id'";
        $result_file = mysqli_query($conn, $query_file);
        $file_data = mysqli_fetch_assoc($result_file);

        // Hapus file jika ada
        if (!empty($file_data['file_surat']) && file_exists('../uploads/surat_cuti/' . $file_data['file_surat'])) {
            unlink('../uploads/surat_cuti/' . $file_data['file_surat']);
        }

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