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
    <title>DPPKBPM</title>
    <link rel="stylesheet" href="../css/login.css" />
</head>

<body>
    <div class="login-container">
        <h2>Login</h2>

        <?php if ($error_message): ?>
            <div class="message error"><?= htmlspecialchars($error_message) ?></div>
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
            Lupa password? <a href="lupa-password.php">Reset password</a>
        </div>
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