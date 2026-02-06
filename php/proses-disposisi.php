<?php
// File untuk memproses Disposisi Surat
include 'database.php';
require_once 'auth_check.php';

// Cek data submission
if (isset($_POST['action']) && $_POST['action'] == 'disposisi') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    
    // Ambil data form
    $sifat_surat = mysqli_real_escape_string($conn, $_POST['sifat_surat']);
    $catatan_disposisi = mysqli_real_escape_string($conn, $_POST['catatan_disposisi']);
    $status_disposisi = 'Sudah didisposisi'; // Set status otomatis

    // Array to string untuk checkbox/multiselect
    $tujuan_disposisi = '';
    if (isset($_POST['tujuan_disposisi']) && is_array($_POST['tujuan_disposisi'])) {
        $tujuan_disposisi = implode(', ', $_POST['tujuan_disposisi']);
    }

    $instruksi_disposisi = '';
    if (isset($_POST['instruksi_disposisi']) && is_array($_POST['instruksi_disposisi'])) {
        $instruksi_disposisi = implode(', ', $_POST['instruksi_disposisi']);
    }

    $dilihat_oleh = '';
    if (isset($_POST['dilihat_oleh']) && is_array($_POST['dilihat_oleh'])) {
        $dilihat_oleh = implode(', ', $_POST['dilihat_oleh']);
    }

    // =========================================================
    // HANDLE UPLOAD FILE DISPOSISI
    // =========================================================
    
    // Ambil data file lama dulu jika perlu
    $query_old = "SELECT file_disposisi FROM surat_masuk WHERE id = '$id'";
    $result_old = mysqli_query($conn, $query_old);
    $old_data = mysqli_fetch_assoc($result_old);
    $file_disposisi = $old_data['file_disposisi']; // Default ke file lama

    // Cek jika ada request hapus file
    if (isset($_POST['delete_file_disposisi']) && $_POST['delete_file_disposisi'] == '1') {
        if (!empty($file_disposisi) && file_exists('../uploads/disposisi/' . $file_disposisi)) {
            unlink('../uploads/disposisi/' . $file_disposisi);
        }
        $file_disposisi = NULL; // Set null di database
    }

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
                window.location.href = 'disposisi-surat.php?id=$id';
            </script>";
            exit();
        }

        // Validasi ukuran (max 10MB)
        if ($file_size > 10485760) {
            echo "<script>
                alert('Error: Ukuran file disposisi maksimal 10MB!');
                window.location.href = 'disposisi-surat.php?id=$id';
            </script>";
            exit();
        }

        // Hapus file lama jika ada (dan bukan null karena sudah dihapus di step delete sebelumnya)
        // Kita cek lagi $old_data['file_disposisi'] langsung untuk memastikan file fiisik lama terhapus saat replace
        if (!empty($old_data['file_disposisi']) && file_exists('../uploads/disposisi/' . $old_data['file_disposisi'])) {
            // Cek apakah file lama itu sama dengan yang di variable $file_disposisi sekarang?
            // Jika user centang delete, $file_disposisi sudah NULL, jadi aman.
            // Jika user TIDAK centang delete tapi upload baru, kita harus hapus yang lama.
            unlink('../uploads/disposisi/' . $old_data['file_disposisi']);
        }

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
                window.location.href = 'disposisi-surat.php?id=$id';
            </script>";
            exit();
        }
    }

    // Update Query
    // Kita gunakan NULL jika $file_disposisi kosong/null
    $file_update_str = $file_disposisi ? "'$file_disposisi'" : "NULL";

    $query = "UPDATE surat_masuk SET 
              sifat_surat = '$sifat_surat',
              tujuan_disposisi = '$tujuan_disposisi',
              instruksi_disposisi = '$instruksi_disposisi',
              catatan_disposisi = '$catatan_disposisi',
              status_disposisi = '$status_disposisi',
              dilihat_oleh = '$dilihat_oleh',
              file_disposisi = $file_update_str
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
                    window.location.href = 'disposisi-surat.php?id=$id';
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
