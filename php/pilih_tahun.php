<?php
session_start();
require_once 'auth_check.php';

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tahun = $_POST['tahun'];
    // Validasi sederhana: pastikan tahun adalah angka 4 digit
    if (preg_match('/^[0-9]{4}$/', $tahun)) {
        // Cek apakah database tersedia
        $test_db_name = 'db_diapra_' . $tahun;
        // Gunakan credentials dari koneksi.php (hardcoded sini atau include, better manual check to avoid session issues)
        // Kita assume root/empty password sesuai koneksi.php
        $test_conn = @new mysqli('localhost', 'root', '', $test_db_name);
        
        if ($test_conn->connect_error) {
            $error = "Database untuk tahun $tahun belum tersedia.";
        } else {
            $test_conn->close();
            $_SESSION['tahun_aktif'] = $tahun;
            header('Location: dashboard.php');
            exit;
        }
    } else {
        $error = "Tahun tidak valid.";
    }
}

// Generate opsi tahun (misal: 2024 s/d 2027)
$current_year = date('Y');
$years = range(2024, 2027);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Tahun - DIAPRA</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="auth-header">
            <img src="../assets/img/LOGO.png" alt="Logo DIAPRA" class="logo-img">
            <div class="app-title">Digitalisasi Administrasi Persuratan<br>(DIAPRA)</div>
            <div class="auth-subtitle">Pilih Tahun Anggaran</div>
        </div>

        <?php if (isset($error)): ?>
            <div class="message error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label for="tahun">Tahun</label>
                <select name="tahun" id="tahun" required style="width: 100%; padding: 12px 16px; font-size: 14px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-family: inherit;">
                    <?php foreach ($years as $y): ?>
                        <option value="<?= $y ?>" <?= $y == $current_year ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit">
                Lanjut <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <div class="auth-footer">
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="copyright">
        &copy; <?= date('Y') ?> DIAPRA DPPKBPM. All rights reserved.
    </div>
</body>
</html>
