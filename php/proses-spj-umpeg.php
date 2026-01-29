<?php
// File untuk memproses CRUD SPJ UMPEG
include 'database.php';
require_once 'auth_check.php';

// Cek action
if (isset($_POST['action']) || isset($_GET['action'])) {
    $action = isset($_POST['action']) ? $_POST['action'] : $_GET['action'];

    // TAMBAH DATA SPJ UMPEG
    if ($action == 'add') {
        $nomor_urut = mysqli_real_escape_string($conn, $_POST['nomor_urut']);
        $nomor_spj = mysqli_real_escape_string($conn, $_POST['nomor_spj']);
        $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
        $nama_kegiatan = mysqli_real_escape_string($conn, $_POST['nama_kegiatan']);
        $dibuat_oleh = mysqli_real_escape_string($conn, $_POST['dibuat_oleh']);

        // Upload file SPJ
        $file_spj = '';
        if (isset($_FILES['file_spj']) && $_FILES['file_spj']['error'] == 0) {
            $allowed_ext = array('pdf');
            $file_name = $_FILES['file_spj']['name'];
            $file_tmp = $_FILES['file_spj']['tmp_name'];
            $file_size = $_FILES['file_spj']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // Validasi ekstensi
            if (!in_array($file_ext, $allowed_ext)) {
                echo "<script>
                    alert('Error: Hanya file PDF yang diperbolehkan!');
                    window.location.href = 'tambah-spj-umpeg.php';
                </script>";
                exit();
            }

            // Validasi ukuran (max 10MB)
            if ($file_size > 10485760) {
                echo "<script>
                    alert('Error: Ukuran file maksimal 10MB!');
                    window.location.href = 'tambah-spj-umpeg.php';
                </script>";
                exit();
            }

            // Generate nama file unik
            $new_file_name = 'spj_' . time() . '_' . uniqid() . '.' . $file_ext;
            $upload_path = '../uploads/spj_umpeg/' . $new_file_name;

            // Buat folder jika belum ada
            if (!file_exists('../uploads/spj_umpeg/')) {
                mkdir('../uploads/spj_umpeg/', 0777, true);
            }

            // Upload file
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $file_spj = $new_file_name;
            } else {
                echo "<script>
                    alert('Error: Gagal mengupload file!');
                    window.location.href = 'tambah-spj-umpeg.php';
                </script>";
                exit();
            }
        }

        // Insert ke database
        $query = "INSERT INTO spj_umpeg 
                  (nomor_urut, nomor_spj, tanggal, nama_kegiatan, dibuat_oleh, file_spj) 
                  VALUES 
                  ('$nomor_urut', '$nomor_spj', '$tanggal', '$nama_kegiatan', '$dibuat_oleh', '$file_spj')";

        if (mysqli_query($conn, $query)) {
            echo "<script>
                alert('Data SPJ UMPEG berhasil ditambahkan!');
                window.location.href = 'spj-umpeg.php';
            </script>";
        } else {
            echo "<script>
                alert('Error: " . mysqli_error($conn) . "');
                window.location.href = 'tambah-spj-umpeg.php';
            </script>";
        }
    }

    // EDIT DATA SPJ UMPEG
    elseif ($action == 'edit') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $nomor_urut = mysqli_real_escape_string($conn, $_POST['nomor_urut']);
        $nomor_spj = mysqli_real_escape_string($conn, $_POST['nomor_spj']);
        $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
        $nama_kegiatan = mysqli_real_escape_string($conn, $_POST['nama_kegiatan']);
        $dibuat_oleh = mysqli_real_escape_string($conn, $_POST['dibuat_oleh']);

        // Ambil data lama untuk file
        $query_old = "SELECT file_spj FROM spj_umpeg WHERE id = '$id'";
        $result_old = mysqli_query($conn, $query_old);
        $old_data = mysqli_fetch_assoc($result_old);
        $file_spj = $old_data['file_spj'];

        // Cek apakah ada file baru
        if (isset($_FILES['file_spj']) && $_FILES['file_spj']['error'] == 0) {
            $allowed_ext = array('pdf');
            $file_name = $_FILES['file_spj']['name'];
            $file_tmp = $_FILES['file_spj']['tmp_name'];
            $file_size = $_FILES['file_spj']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // Validasi ekstensi
            if (!in_array($file_ext, $allowed_ext)) {
                echo "<script>
                    alert('Error: Hanya file PDF yang diperbolehkan!');
                    window.location.href = 'edit-spj-umpeg.php?id=$id';
                </script>";
                exit();
            }

            // Validasi ukuran
            if ($file_size > 10485760) {
                echo "<script>
                    alert('Error: Ukuran file maksimal 10MB!');
                    window.location.href = 'edit-spj-umpeg.php?id=$id';
                </script>";
                exit();
            }

            // Hapus file lama
            if (!empty($old_data['file_spj']) && file_exists('../uploads/spj_umpeg/' . $old_data['file_spj'])) {
                unlink('../uploads/spj_umpeg/' . $old_data['file_spj']);
            }

            // Upload file baru
            $new_file_name = 'spj_' . time() . '_' . uniqid() . '.' . $file_ext;
            $upload_path = '../uploads/spj_umpeg/' . $new_file_name;

            // Buat folder jika belum ada
            if (!file_exists('../uploads/spj_umpeg/')) {
                mkdir('../uploads/spj_umpeg/', 0777, true);
            }

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $file_spj = $new_file_name;
            }
        }

        // Update database
        $query = "UPDATE spj_umpeg SET 
                  nomor_urut = '$nomor_urut',
                  nomor_spj = '$nomor_spj',
                  tanggal = '$tanggal',
                  nama_kegiatan = '$nama_kegiatan',
                  dibuat_oleh = '$dibuat_oleh',
                  file_spj = '$file_spj'
                  WHERE id = '$id'";

        if (mysqli_query($conn, $query)) {
            echo "<script>
                alert('Data SPJ UMPEG berhasil diupdate!');
                window.location.href = 'spj-umpeg.php';
            </script>";
        } else {
            echo "<script>
                alert('Error: " . mysqli_error($conn) . "');
                window.location.href = 'edit-spj-umpeg.php?id=$id';
            </script>";
        }
    }

    // HAPUS DATA SPJ UMPEG
    elseif ($action == 'delete') {
        $id = mysqli_real_escape_string($conn, $_GET['id']);

        // Ambil data file untuk dihapus
        $query_file = "SELECT file_spj FROM spj_umpeg WHERE id = '$id'";
        $result_file = mysqli_query($conn, $query_file);
        $file_data = mysqli_fetch_assoc($result_file);

        // Hapus file jika ada
        if (!empty($file_data['file_spj']) && file_exists('../uploads/spj_umpeg/' . $file_data['file_spj'])) {
            unlink('../uploads/spj_umpeg/' . $file_data['file_spj']);
        }

        // Hapus dari database
        $query = "DELETE FROM spj_umpeg WHERE id = '$id'";

        if (mysqli_query($conn, $query)) {
            echo "<script>
                alert('Data SPJ UMPEG berhasil dihapus!');
                window.location.href = 'spj-umpeg.php';
            </script>";
        } else {
            echo "<script>
                alert('Error: " . mysqli_error($conn) . "');
                window.location.href = 'spj-umpeg.php';
            </script>";
        }
    }
} else {
    // Jika tidak ada action, redirect ke halaman SPJ UMPEG
    header("Location: spj-umpeg.php");
    exit();
}

mysqli_close($conn);
