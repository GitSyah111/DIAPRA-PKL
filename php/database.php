<?php
// database.php

// Konfigurasi database
$host = "localhost"; // Host database Anda, biasanya localhost
$username = "root"; // Username database Anda
$password = ""; // Password database Anda (kosong jika tidak ada)
$database = "si_surat"; // Nama database yang Anda buat sebelumnya

// Buat koneksi ke database
$conn = new mysqli($host, $username, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Set karakter set ke utf8mb4 untuk mendukung berbagai karakter
$conn->set_charset("utf8mb4");

// Anda bisa menambahkan pesan sukses koneksi untuk debugging (opsional)
//echo "Koneksi database berhasil!";
