<?php
session_start();
$step = isset($_GET['step']) ? $_GET['step'] : 1;
$error_message = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lupa Password - DPPKBPM</title>
    <link rel="stylesheet" href="../css/login.css" />
    <style>
        .back-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        .back-link a {
            color: #666;
            text-decoration: none;
        }
        .back-link a:hover {
            color: #333;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Lupa Password</h2>

        <?php if ($error_message): ?>
            <div class="message error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
            <!-- STEP 1: Masukkan Username -->
            <p style="text-align: center; margin-bottom: 20px; color: #666;">
                Masukkan username Anda untuk mereset password.
            </p>
            <form action="proses-reset.php?action=check_username" method="post">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autocomplete="off" placeholder="Masukkan username anda" />
                </div>
                <button type="submit">Cari Akun</button>
            </form>
        <?php elseif ($step == 2): ?>
            <!-- STEP 2: Masukkan Password Baru -->
            <?php if (!isset($_SESSION['reset_username'])): ?>
                <?php header('Location: lupa-password.php'); exit; ?>
            <?php endif; ?>
            
            <p style="text-align: center; margin-bottom: 20px; color: #666;">
                Reset password untuk akun: <strong><?= htmlspecialchars($_SESSION['reset_username']) ?></strong>
            </p>
            <form action="proses-reset.php?action=reset_password" method="post">
                <div class="input-group">
                    <label for="password">Password Baru</label>
                    <input type="password" id="password" name="password" required placeholder="Minimal 3 karakter" />
                </div>
                <div class="input-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Ulangi password baru" />
                </div>
                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>

        <div class="back-link">
            <a href="login.php">Kembali ke Login</a>
        </div>
    </div>
</body>

</html>
