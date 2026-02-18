<?php
// File untuk memproses CRUD Surat Masuk
include 'database.php';
require_once 'auth_check.php';

// Cek action
if (isset($_POST['action']) || isset($_GET['action'])) {
    $action = isset($_POST['action']) ? $_POST['action'] : $_GET['action'];

    // TAMBAH DATA SURAT MASUK
    if ($action == 'add') {
        $nomor_agenda = mysqli_real_escape_string($conn, $_POST['nomor_agenda']);
        $tanggal_terima = mysqli_real_escape_string($conn, $_POST['tanggal_terima']);
        $alamat_pengirim = mysqli_real_escape_string($conn, $_POST['alamat_pengirim']);
        $tanggal_surat = mysqli_real_escape_string($conn, $_POST['tanggal_surat']);
        $nomor_surat = mysqli_real_escape_string($conn, $_POST['nomor_surat']);
        $perihal = mysqli_real_escape_string($conn, $_POST['perihal']);
        $id_user = $_SESSION['user_id'];

        // Dilihat oleh (array to string)
        $dilihat_oleh = '';
        if (isset($_POST['dilihat_oleh']) && is_array($_POST['dilihat_oleh'])) {
            $dilihat_oleh = implode(', ', $_POST['dilihat_oleh']);
        }

        // Upload file surat
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
                    window.location.href = 'tambah-surat-masuk.php';
                </script>";
                exit();
            }

            // Validasi ukuran (max 10MB)
            if ($file_size > 10485760) {
                echo "<script>
                    alert('Error: Ukuran file maksimal 10MB!');
                    window.location.href = 'tambah-surat-masuk.php';
                </script>";
                exit();
            }

            // Generate nama file unik
            $new_file_name = 'surat_' . time() . '_' . uniqid() . '.' . $file_ext;
            $upload_path = '../uploads/surat_masuk/' . $new_file_name;

            // Buat folder jika belum ada
            if (!file_exists('../uploads/surat_masuk/')) {
                mkdir('../uploads/surat_masuk/', 0777, true);
            }

            // Upload file
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $file_surat = $new_file_name;
            } else {
                echo "<script>
                    alert('Error: Gagal mengupload file!');
                    window.location.href = 'tambah-surat-masuk.php';
                </script>";
                exit();
            }
        }

        // Insert ke database
        $query = "INSERT INTO surat_masuk 
                  (nomor_agenda, tanggal_terima, alamat_pengirim, tanggal_surat, nomor_surat, perihal, file_surat, dilihat_oleh, status_disposisi, id_user) 
                  VALUES 
                  ('$nomor_agenda', '$tanggal_terima', '$alamat_pengirim', '$tanggal_surat', '$nomor_surat', '$perihal', '$file_surat', '$dilihat_oleh', 'Belum diproses', '$id_user')";

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
                        text: 'Surat masuk berhasil ditambahkan!',
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
                        window.location.href = 'tambah-surat-masuk.php';
                    });
                </script>
            </body>
            </html>";
        }
    }

    // EDIT DATA SURAT MASUK
    elseif ($action == 'edit') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $nomor_agenda = mysqli_real_escape_string($conn, $_POST['nomor_agenda']);
        $tanggal_terima = mysqli_real_escape_string($conn, $_POST['tanggal_terima']);
        $alamat_pengirim = mysqli_real_escape_string($conn, $_POST['alamat_pengirim']);
        $tanggal_surat = mysqli_real_escape_string($conn, $_POST['tanggal_surat']);
        $nomor_surat = mysqli_real_escape_string($conn, $_POST['nomor_surat']);
        $perihal = mysqli_real_escape_string($conn, $_POST['perihal']);

        // Dilihat oleh
        $dilihat_oleh = '';
        if (isset($_POST['dilihat_oleh']) && is_array($_POST['dilihat_oleh'])) {
            $dilihat_oleh = implode(', ', $_POST['dilihat_oleh']);
        }

        // Ambil data lama untuk file
        $query_old = "SELECT file_surat FROM surat_masuk WHERE id = '$id'";
        $result_old = mysqli_query($conn, $query_old);
        $old_data = mysqli_fetch_assoc($result_old);
        $file_surat = $old_data['file_surat'];

        // Cek jika ada request hapus file
        if (isset($_POST['delete_file_surat']) && $_POST['delete_file_surat'] == '1') {
            if (!empty($file_surat) && file_exists('../uploads/surat_masuk/' . $file_surat)) {
                unlink('../uploads/surat_masuk/' . $file_surat);
            }
            $file_surat = NULL; // Set null di database
        }

        // Cek apakah ada file baru yang diupload
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
                    window.location.href = 'edit-surat-masuk.php?id=$id';
                </script>";
                exit();
            }

            // Validasi ukuran
            if ($file_size > 10485760) {
                echo "<script>
                    alert('Error: Ukuran file maksimal 10MB!');
                    window.location.href = 'edit-surat-masuk.php?id=$id';
                </script>";
                exit();
            }

            // Hapus file lama jika ada (dan bukan null karena sudah dihapus di step delete sebelumnya)
            // Jika user tidak delete tapi replace file, maka file lama harus dihapus agar tidak numpuk
            if (!empty($old_data['file_surat']) && file_exists('../uploads/surat_masuk/' . $old_data['file_surat'])) {
                 // Cek apakah file lama itu sama dengan yang di variable $file_surat sekarang?
                 // Jika user centang delete, $file_surat sudah NULL, jadi aman.
                 // Jika user upload baru, kita harus hapus yang lama.
                unlink('../uploads/surat_masuk/' . $old_data['file_surat']);
            }

            // Upload file baru
            $new_file_name = 'surat_' . time() . '_' . uniqid() . '.' . $file_ext;
            $upload_path = '../uploads/surat_masuk/' . $new_file_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $file_surat = $new_file_name;
            }
        }

        // Update database
        // Handle NULL value properly for file_surat
        $file_update_str = $file_surat ? "'$file_surat'" : "NULL";

        $query = "UPDATE surat_masuk SET 
                  nomor_agenda = '$nomor_agenda',
                  tanggal_terima = '$tanggal_terima',
                  alamat_pengirim = '$alamat_pengirim',
                  tanggal_surat = '$tanggal_surat',
                  nomor_surat = '$nomor_surat',
                  perihal = '$perihal',
                  file_surat = $file_update_str,
                  dilihat_oleh = '$dilihat_oleh'
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
                        text: 'Surat masuk berhasil diupdate!',
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
                        window.location.href = 'edit-surat-masuk.php?id=$id';
                    });
                </script>
            </body>
            </html>";
        }
    }

    // HAPUS DATA SURAT MASUK
    elseif ($action == 'delete') {
        $id = mysqli_real_escape_string($conn, $_GET['id']);

        // Ambil data file untuk dihapus
        $query_file = "SELECT file_surat FROM surat_masuk WHERE id = '$id'";
        $result_file = mysqli_query($conn, $query_file);
        $file_data = mysqli_fetch_assoc($result_file);

        // Hapus file jika ada
        if (!empty($file_data['file_surat']) && file_exists('../uploads/surat_masuk/' . $file_data['file_surat'])) {
            unlink('../uploads/surat_masuk/' . $file_data['file_surat']);
        }

        // --- TAMBAHAN: Hapus data dan file disposisi yang terkait ---
        $query_disposisi = "SELECT file_disposisi FROM disposisi WHERE id_surat_masuk = '$id'";
        $result_disposisi = mysqli_query($conn, $query_disposisi);
        while ($row_disposisi = mysqli_fetch_assoc($result_disposisi)) {
            if (!empty($row_disposisi['file_disposisi']) && file_exists('../uploads/disposisi/' . $row_disposisi['file_disposisi'])) {
                unlink('../uploads/disposisi/' . $row_disposisi['file_disposisi']);
            }
        }
        // Hapus data disposisi dari database
        mysqli_query($conn, "DELETE FROM disposisi WHERE id_surat_masuk = '$id'");
        // --- END TAMBAHAN ---

        // Hapus dari database
        $query = "DELETE FROM surat_masuk WHERE id = '$id'";

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
                        text: 'Surat masuk berhasil dihapus!',
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
                        window.location.href = 'surat-masuk.php';
                    });
                </script>
            </body>
            </html>";
        }
    }
} else {
    // Jika tidak ada action, redirect ke halaman surat masuk
    header("Location: surat-masuk.php");
    exit();
}

mysqli_close($conn);
