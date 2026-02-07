<?php
// config/koneksi.php
// Mulai session jika belum dimulai (penting untuk akses $_SESSION)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = ''; 

// Tentukan nama database secara dinamis
$tahun_db = isset($_SESSION['tahun_aktif']) ? $_SESSION['tahun_aktif'] : date('Y');
$DB_NAME = 'db_diapra_' . $tahun_db;

// Validasi Keamanan
if (!preg_match('/^[0-9]{4}$/', $tahun_db)) {
    die("Format tahun database tidak valid.");
}

// Fungsi helper untuk mencoba koneksi
function try_connect($host, $user, $pass, $dbname) {
    try {
        $conn = new mysqli($host, $user, $pass, $dbname);
        if ($conn->connect_error) {
            return false;
        }
        return $conn;
    } catch (Exception $e) {
        // Tangkap mysqli_sql_exception atau Exception lainnya
        return false;
    }
}

// Logika Koneksi dengan Fallback (khusus jika belum login/tidak ada session tahun)
$koneksi = null;
$error_msg = "";

// 1. Coba koneksi ke tahun yang diminta/default
$koneksi = try_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// 2. Jika gagal DAN ini adalah default (user belum pilih tahun), coba fallback ke tahun-tahun sebelumnya
// 2. Jika gagal: 
// - Jika user SUDAH pilih tahun (session set), jangan fallback! Biarkan error agar user tahu DB tidak ada.
// - Jika user BELUM pilih tahun (login page), coba fallback hanya untuk keperluan autentikasi user.
if (!$koneksi && !isset($_SESSION['tahun_aktif'])) {
    // Coba mundur sampai 3 tahun ke belakang hanya untuk mencari DB valid buat login
    for ($i = 0; $i <= 3; $i++) {
        $fallback_year = date('Y') - $i;
        if ($fallback_year == $tahun_db) continue; // Skip jika sama dengan yang sudah dicoba

        $fallback_db = 'db_diapra_' . $fallback_year;
        $koneksi = try_connect($DB_HOST, $DB_USER, $DB_PASS, $fallback_db);
        if ($koneksi) {
            // Kita gunakan DB ini sementara untuk login, TAPI jangan set sebagai tahun_aktif
            // $DB_NAME = $fallback_db; 
            break; 
        }
    }
}

if (!$koneksi) {
    if (isset($_SESSION['user_id'])) {
         die("Database untuk tahun $tahun_db belum tersedia. <a href='pilih_tahun.php'>Pilih tahun lain</a>");
    } else {
        // Pesan error lebih detail jika semua fallback gagal
        die("Gagal terhubung ke database. Pastikan database db_diapra_" . date('Y') . " atau tahun sebelumnya tersedia.");
    }
}

// Global variable support untuk backward compatibility code lama yg pakai $conn
$conn = $koneksi;

// Cek apakah user sudah login tapi belum memilih tahun (dan kita bukan di halaman login/pilih tahun/public)
// Script name check agar tidak redirect loop
$current_script = basename($_SERVER['PHP_SELF']);
$public_pages = ['login.php', 'login_process.php', 'pilih_tahun.php', 'logout.php'];

if (isset($_SESSION['user_id']) && !isset($_SESSION['tahun_aktif']) && !in_array($current_script, $public_pages)) {
    header("Location: pilih_tahun.php");
    exit;
}

$base_url = '/si_kepegawaian'; 

