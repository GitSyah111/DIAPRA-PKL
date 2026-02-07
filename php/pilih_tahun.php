<?php
session_start();
require_once 'auth_check.php';

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tahun = $_POST['tahun'];
    // Validasi sederhana: pastikan tahun adalah angka 4 digit
    if (preg_match('/^[0-9]{4}$/', $tahun)) {
        $_SESSION['tahun_aktif'] = $tahun;
        header('Location: dashboard.php');
        exit;
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
    <link rel="stylesheet" href="../css/login.css"> <!-- Menggunakan style login yg sudah ada jika cocok -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Inline style untuk kesederhanaan dan kerapian khusus page ini */
        body {
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }
        .card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .logo-img {
            width: 80px;
            margin-bottom: 1rem;
        }
        h2 {
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        p {
            color: #6b7280;
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }
        select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            background-color: #fff;
            font-size: 1rem;
            color: #1f2937;
            transition: all 0.2s;
        }
        select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .btn-submit {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.1s;
        }
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }
        .logout-link {
            display: inline-block;
            margin-top: 1.5rem;
            color: #6b7280;
            text-decoration: none;
            font-size: 0.875rem;
        }
        .logout-link:hover {
            color: #ef4444;
        }
    </style>
</head>
<body>
    <div class="card">
        <img src="../assets/img/LOGO.png" alt="Logo" class="logo-img">
        <h2>Pilih Tahun Anggaran</h2>
        <p>Silakan pilih tahun database yang ingin diakses</p>

        <?php if (isset($error)): ?>
            <div style="color: #ef4444; margin-bottom: 1rem; background: #fee2e2; padding: 0.5rem; border-radius: 0.375rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="tahun">Tahun</label>
                <select name="tahun" id="tahun" required>
                    <?php foreach ($years as $y): ?>
                        <option value="<?= $y ?>" <?= $y == $current_year ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-submit">
                Lanjut <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <a href="logout.php" class="logout-link">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</body>
</html>
