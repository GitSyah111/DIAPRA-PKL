<?php
// File untuk memproses Disposisi Surat
include 'database.php';
require_once 'auth_check.php';

// Cek data submission
if (isset($_POST['action']) && $_POST['action'] == 'disposisi') {
    // Ambil ID Surat Masuk
    $id_surat_masuk = mysqli_real_escape_string($conn, $_POST['id']);
    
    // Ambil data form
    $sifat_surat = mysqli_real_escape_string($conn, $_POST['sifat_surat']);
    $catatan_disposisi = mysqli_real_escape_string($conn, $_POST['catatan_disposisi']);
    $batas_waktu = !empty($_POST['batas_waktu']) ? mysqli_real_escape_string($conn, $_POST['batas_waktu']) : NULL;
    $status_disposisi = 'Sudah didisposisi'; // Set status otomatis


    $dilihat_oleh = '';
    if (isset($_POST['dilihat_oleh']) && is_array($_POST['dilihat_oleh'])) {
        $dilihat_oleh = implode(', ', $_POST['dilihat_oleh']);
    }

    // =========================================================
    // HANDLE UPLOAD FILE DISPOSISI
    // =========================================================
    
    // Ambil data file lama dulu jika perlu
    // =========================================================
    // HANDLE UPLOAD FILE DISPOSISI
    // =========================================================
    
    // Tidak perlu ambil data lama karena ini INSERT baru
    $file_disposisi = '';

    /* 
       NOTE: Jika tabel disposisi belum punya kolom file_disposisi, 
       kode ini mengasumsikan kolom tersebut sudah ditambahkan sesuai rencana.
    */

    // Cek jika ada file baru diupload
    if (isset($_FILES['file_disposisi']) && $_FILES['file_disposisi']['error'] == 0) {
        $allowed_ext = array('pdf');
        $file_name = $_FILES['file_disposisi']['name'];
        $file_tmp = $_FILES['file_disposisi']['tmp_name'];
        $file_size = $_FILES['file_disposisi']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validasi ekstensi
        if (!in_array($file_ext, $allowed_ext)) {
            echo "<script>
                alert('Error: Hanya file PDF yang diperbolehkan untuk Disposisi!');
                window.location.href = 'disposisi-surat.php?id=$id_surat_masuk';
            </script>";
            exit();
        }

        // Validasi ukuran (max 10MB)
        if ($file_size > 10485760) {
            echo "<script>
                alert('Error: Ukuran file disposisi maksimal 10MB!');
                window.location.href = 'disposisi-surat.php?id=$id_surat_masuk';
            </script>";
            exit();
        }

        // Tidak perlu hapus file lama karena ini record baru

        // Generate nama file unik
        $new_file_name = 'disposisi_' . time() . '_' . uniqid() . '.' . $file_ext;
        $upload_path = '../uploads/disposisi/' . $new_file_name;

        // Buat folder jika belum ada
        if (!file_exists('../uploads/disposisi/')) {
            mkdir('../uploads/disposisi/', 0777, true);
        }

        // Upload file
        if (move_uploaded_file($file_tmp, $upload_path)) {
            $file_disposisi = $new_file_name;
        } else {
            echo "<script>
                alert('Error: Gagal mengupload file disposisi!');
                window.location.href = 'disposisi-surat.php?id=$id_surat_masuk';
            </script>";
            exit();
        }
    }

    // Insert Query ke tabel disposisi
    // Kolom: id_surat_masuk, tujuan_bidang, isi_disposisi, sifat, batas_waktu, catatan, status_baca, file_disposisi
    
    // Mapping variables
    $tujuan_bidang = mysqli_real_escape_string($conn, $_POST['tujuan_disposisi']);
    $isi_disposisi = mysqli_real_escape_string($conn, $_POST['instruksi_disposisi']);
    $sifat_db = $sifat_surat;
    $catatan_db = $catatan_disposisi;
    
    // Handle NULL batas_waktu for SQL
    $batas_waktu_sql = $batas_waktu ? "'$batas_waktu'" : "NULL";

    $query = "INSERT INTO disposisi (id_surat_masuk, tujuan_bidang, isi_disposisi, sifat, batas_waktu, catatan, status_baca, file_disposisi)
              VALUES ('$id_surat_masuk', '$tujuan_bidang', '$isi_disposisi', '$sifat_db', $batas_waktu_sql, '$catatan_db', 0, '$file_disposisi')";

    // Note: Kita juga perlu update status_disposisi di tabel surat_masuk agar tahu surat ini sudah didisposisi
    $query_update_status = "UPDATE surat_masuk SET status_disposisi = 'Sudah didisposisi' WHERE id = '$id_surat_masuk'";
    mysqli_query($conn, $query_update_status);

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
                    text: 'Disposisi berhasil disimpan!',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                }).then((result) => {
                    window.location.href = 'surat-masuk.php';
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
                    window.location.href = 'disposisi-surat.php?id=$id_surat_masuk';
                });
            </script>
        </body>
        </html>";
    }

} else {
    header("Location: surat-masuk.php");
    exit();
}
?>
