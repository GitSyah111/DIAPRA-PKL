<?php
// Koneksi database
include 'database.php';
require_once 'auth_check.php';

// Fungsi untuk upload file
function uploadFile($file)
{
    $target_dir = "../uploads/surat_keluar/";

    // Buat folder jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = basename($file["name"]);
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Generate nama file unik
    $new_file_name = uniqid() . '_' . time() . '.' . $file_extension;
    $target_file = $target_dir . $new_file_name;

    // Validasi file
    if ($file_extension != "pdf") {
        return array('status' => false, 'message' => 'Hanya file PDF yang diperbolehkan!');
    }

    // Validasi ukuran file (maksimal 10MB)
    if ($file["size"] > 10485760) {
        return array('status' => false, 'message' => 'Ukuran file terlalu besar! Maksimal 10MB');
    }

    // Upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return array('status' => true, 'file_name' => $new_file_name);
    } else {
        return array('status' => false, 'message' => 'Gagal mengupload file!');
    }
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'tambah') {
        // Ambil data dari form
        $nomor_urut = mysqli_real_escape_string($conn, $_POST['nomor_urut']);
        $tanggal_surat = mysqli_real_escape_string($conn, $_POST['tanggal_surat']);
        $nomor_surat = mysqli_real_escape_string($conn, $_POST['nomor_surat']);
        $tujuan_surat = mysqli_real_escape_string($conn, $_POST['tujuan_surat']);
        $perihal = mysqli_real_escape_string($conn, $_POST['perihal']);
        $dibuat_oleh = 'Admin'; // Bisa diganti dengan session username

        $file_surat = '';

        // Upload file jika ada
        if (isset($_FILES['file_surat']) && $_FILES['file_surat']['error'] == 0) {
            $upload = uploadFile($_FILES['file_surat']);
            if ($upload['status']) {
                $file_surat = $upload['file_name'];
            } else {
                echo "<script>
                    alert('" . $upload['message'] . "');
                    window.history.back();
                </script>";
                exit();
            }
        }

        // Insert ke database
        $query = "INSERT INTO surat_keluar (nomor_urut, nomor_surat, tujuan_surat, tanggal_surat, perihal, dibuat_oleh, file_surat) 
                  VALUES ('$nomor_urut', '$nomor_surat', '$tujuan_surat', '$tanggal_surat', '$perihal', '$dibuat_oleh', '$file_surat')";

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
                        text: 'Surat keluar berhasil ditambahkan!',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        window.location.href = 'surat-keluar.php';
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
                        text: 'Gagal menambahkan surat keluar!',
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
    } elseif ($action == 'edit') {
        // Ambil data dari form
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $tanggal_surat = mysqli_real_escape_string($conn, $_POST['tanggal_surat']);
        $nomor_surat = mysqli_real_escape_string($conn, $_POST['nomor_surat']);
        $tujuan_surat = mysqli_real_escape_string($conn, $_POST['tujuan_surat']);
        $perihal = mysqli_real_escape_string($conn, $_POST['perihal']);

        // Ambil data lama
        $query_old = "SELECT file_surat FROM surat_keluar WHERE id = '$id'";
        $result_old = mysqli_query($conn, $query_old);
        $old_data = mysqli_fetch_assoc($result_old);
        $file_surat = $old_data['file_surat'];

        // Upload file baru jika ada
        if (isset($_FILES['file_surat']) && $_FILES['file_surat']['error'] == 0) {
            $upload = uploadFile($_FILES['file_surat']);
            if ($upload['status']) {
                // Hapus file lama jika ada
                if (!empty($old_data['file_surat'])) {
                    $old_file = "../uploads/surat_keluar/" . $old_data['file_surat'];
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
                }
                $file_surat = $upload['file_name'];
            } else {
                echo "<script>
                    alert('" . $upload['message'] . "');
                    window.history.back();
                </script>";
                exit();
            }
        }

        // Update database
        $query = "UPDATE surat_keluar SET 
                  tanggal_surat = '$tanggal_surat',
                  nomor_surat = '$nomor_surat',
                  tujuan_surat = '$tujuan_surat',
                  perihal = '$perihal',
                  file_surat = '$file_surat'
                  WHERE id = '$id'";

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
                        text: 'Surat keluar berhasil diupdate!',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        window.location.href = 'surat-keluar.php';
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
                        text: 'Gagal mengupdate surat keluar!',
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
    } elseif ($action == 'hapus') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);

        // Ambil data file
        $query_file = "SELECT file_surat FROM surat_keluar WHERE id = '$id'";
        $result_file = mysqli_query($conn, $query_file);
        $file_data = mysqli_fetch_assoc($result_file);

        // Hapus file jika ada
        if (!empty($file_data['file_surat'])) {
            $file_path = "../uploads/surat_keluar/" . $file_data['file_surat'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        // Hapus dari database
        $query = "DELETE FROM surat_keluar WHERE id = '$id'";

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
                        text: 'Surat keluar berhasil dihapus!',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        window.location.href = 'surat-keluar.php';
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
                        text: 'Gagal menghapus surat keluar!',
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
} else {
    header("Location: surat-keluar.php");
    exit();
}
