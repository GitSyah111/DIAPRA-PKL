<?php
// File hapus SPJ UMPEG - redirect ke proses-spj-umpeg.php
// Koneksi database
include 'database.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Redirect ke proses dengan action delete
    header("Location: proses-spj-umpeg.php?action=delete&id=$id");
    exit();
} else {
    echo "<script>alert('ID tidak valid!'); window.location.href='spj-umpeg.php';</script>";
}
