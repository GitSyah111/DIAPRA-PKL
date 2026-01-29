<?php
// File untuk memproses CRUD Data Kepala Dinas
include 'database.php';
require_once 'auth_check.php';

// Cek action
if (isset($_POST['action']) || isset($_GET['action'])) {
    $action = isset($_POST['action']) ? $_POST['action'] : $_GET['action'];

    // Admin hanya boleh melihat, tidak boleh CUD
    if ($role === 'admin' && in_array($action, ['add', 'edit', 'delete'])) {
        echo "<script>alert('Anda tidak memiliki hak untuk mengubah data kepala dinas.'); window.location.href = 'data-kepala-dinas.php';</script>";
        exit;
    }

    // TAMBAH DATA
    if ($action == 'add') {
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $pangkat = mysqli_real_escape_string($conn, $_POST['pangkat']);
        $NIP = mysqli_real_escape_string($conn, $_POST['NIP']);

        $query = "INSERT INTO kadis (nama, pangkat, NIP) VALUES ('$nama', '$pangkat', '$NIP')";

        if (mysqli_query($conn, $query)) {
            echo "<script>
                alert('Data berhasil ditambahkan!');
                window.location.href = 'data-kepala-dinas.php';
            </script>";
        } else {
            echo "<script>
                alert('Error: " . mysqli_error($conn) . "');
                window.location.href = 'data-kepala-dinas.php';
            </script>";
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
            echo "<script>
                alert('Data berhasil diupdate!');
                window.location.href = 'data-kepala-dinas.php';
            </script>";
        } else {
            echo "<script>
                alert('Error: " . mysqli_error($conn) . "');
                window.location.href = 'data-kepala-dinas.php';
            </script>";
        }
    }

    // HAPUS DATA
    elseif ($action == 'delete') {
        $no = mysqli_real_escape_string($conn, $_GET['no']);

        $query = "DELETE FROM kadis WHERE no = '$no'";

        if (mysqli_query($conn, $query)) {
            echo "<script>
                alert('Data berhasil dihapus!');
                window.location.href = 'data-kepala-dinas.php';
            </script>";
        } else {
            echo "<script>
                alert('Error: " . mysqli_error($conn) . "');
                window.location.href = 'data-kepala-dinas.php';
            </script>";
        }
    }
} else {
    // Jika tidak ada action, redirect ke halaman data kepala dinas
    header("Location: data-kepala-dinas.php");
    exit();
}

mysqli_close($conn);
