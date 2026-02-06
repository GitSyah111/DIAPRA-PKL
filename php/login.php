<?php
session_start();
include_once '../database/koneksi.php';
$error_message = $_SESSION['login_error'] ?? '';
$success_message = $_SESSION['register_success'] ?? '';
unset($_SESSION['login_error'], $_SESSION['register_success']);
?>

<!DOCTYPE html>
<html lang="id">


<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DPPKBPM</title>
    <link rel="stylesheet" href="../css/login.css" />
</head>

<body>
    <div class="login-container">
        <h2>Login</h2>

        <?php if ($error_message): ?>
            <div class="message error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="message success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <form action="login_process.php" method="post" novalidate>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autocomplete="username" />
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password" />
            </div>
            <button type="submit">Login</button>
        </form>

        <div class="register-link">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
            Lupa password? <a href="lupa-password.php">Reset password</a>
        </div>
    </div>
</body>

</html>