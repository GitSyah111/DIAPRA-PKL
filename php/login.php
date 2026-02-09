<?php
session_start();
include_once '../database/koneksi.php';
$error_message = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - DIAPRA</title>
    <link rel="stylesheet" href="../css/login.css" />
</head>

<body>
    <div class="login-container">
        <div class="auth-header">
            <img src="../assets/img/LOGO.png" alt="Logo DIAPRA" class="logo-img">
            <div class="app-title">Digitalisasi Administrasi Persuratan<br>(DIAPRA)</div>
            <div class="auth-subtitle">Silahkan Login</div>
        </div>

        <?php if ($error_message): ?>
            <div class="message error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form action="login_process.php" method="post" novalidate>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autocomplete="username" placeholder="Masukkan username" />
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="Masukkan password" />
            </div>
            <button type="submit">Login</button>
        </form>

        <div class="auth-footer">
            <a href="lupa-password.php">Lupa Password?</a>
        </div>
    </div>

    <div class="copyright">
        &copy; <?= date('Y') ?> DIAPRA DPPKBPM. All rights reserved.
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            if (this.checkValidity()) {
                btn.innerHTML = 'Loading...';
                btn.disabled = true;
                btn.style.opacity = '0.7';
                btn.style.cursor = 'not-allowed';
            }
        });
    </script>
</body>

</html>