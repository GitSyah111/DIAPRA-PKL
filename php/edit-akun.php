<?php
require_once 'auth_check.php';
include 'database.php';

// Get current user data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM user WHERE no = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user_data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Akun - DPPKBPM</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/kepala-dinas.css">
    <link rel="stylesheet" href="../css/surat-masuk.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="hover-trigger"></div>
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="../assets/img/LOGO.png" alt="Logo DPPKBPM" class="logo-img">
                </div>
                <h2 class="sidebar-text">DIAPRA DPPKBPM</h2>
            </div>

            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item" title="Dashboard">
                    <i class="fas fa-home"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
                <a href="surat-masuk.php" class="nav-item" title="Surat Masuk">
                    <i class="fas fa-inbox"></i>
                    <span class="sidebar-text">Surat Masuk</span>
                </a>
                <a href="surat-keluar.php" class="nav-item" title="Surat Keluar">
                    <i class="fas fa-paper-plane"></i>
                    <span class="sidebar-text">Surat Keluar</span>
                </a>
                <?php if ($role !== 'user'): ?>
                <a href="spj-umpeg.php" class="nav-item" title="SPJ UMPEG">
                    <i class="fas fa-file-invoice"></i>
                    <span class="sidebar-text">SPJ UMPEG</span>
                </a>
                <?php endif; ?>
                <a href="surat-cuti.php" class="nav-item" title="Surat Cuti">
                    <i class="fas fa-calendar-check"></i>
                    <span class="sidebar-text">Surat Cuti</span>
                </a>
                <?php if ($role !== 'user'): ?>
                <a href="data-pengguna.php" class="nav-item" title="Data Pengguna">
                    <i class="fas fa-users"></i>
                    <span class="sidebar-text">Data Pengguna</span>
                </a>
                <a href="data-kepala-dinas.php" class="nav-item" title="Data Kepala Dinas">
                    <i class="fas fa-user-tie"></i>
                    <span class="sidebar-text">Data Kepala Dinas</span>
                </a>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer sidebar-text">
                <p><i class="fas fa-info-circle"></i> Versi 1.0.0</p>
            </div>

        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <button class="menu-toggle" id="mobileMenuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <button class="header-menu-btn" id="headerMenuBtn">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="header-title">Edit Akun</h1>
                </div>
                <div class="header-right">
                    <div class="user-info" id="userInfoToggle">
                        <span class="user-name"><?= htmlspecialchars($nama) ?></span>
                        <span class="user-role"><?= ucfirst(htmlspecialchars($role)) ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="edit-akun.php" class="active">
                            <i class="fas fa-user-edit"></i> Edit Akun
                        </a>
                    </div>
                    <button class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </button>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content">
                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_message) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_message) ?>
                    </div>
                <?php endif; ?>

                <!-- Edit Account Form -->
                <div class="content-box">
                    <div class="box-header">
                        <h2><i class="fas fa-user-edit"></i> Edit Informasi Akun</h2>
                    </div>

                    <div class="form-container" style="padding: 30px;">
                        <form action="proses-edit-akun.php" method="POST" id="editAccountForm">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nama">Nama Lengkap <span class="required">*</span></label>
                                    <input type="text" id="nama" name="nama" class="form-input" value="<?= htmlspecialchars($user_data['nama']) ?>" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="username">Username <span class="required">*</span></label>
                                    <input type="text" id="username" name="username" class="form-input" value="<?= htmlspecialchars($user_data['username']) ?>" required>
                                    <small style="color: #6b7280;">Username digunakan untuk login</small>
                                </div>
                            </div>

                            <hr style="margin: 30px 0; border: none; border-top: 1px solid #e5e7eb;">
                            
                            <h3 style="margin-bottom: 20px; color: #1e3a5f;"><i class="fas fa-key"></i> Ubah Password (Opsional)</h3>
                            <p style="margin-bottom: 20px; color: #6b7280; font-size: 14px;">Kosongkan jika tidak ingin mengubah password</p>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="current_password">Password Saat Ini</label>
                                    <input type="password" id="current_password" name="current_password" class="form-input" autocomplete="current-password">
                                    <small style="color: #6b7280;">Wajib diisi jika ingin mengubah password</small>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="new_password">Password Baru</label>
                                    <input type="password" id="new_password" name="new_password" class="form-input" minlength="6" autocomplete="new-password">
                                    <small style="color: #6b7280;">Minimal 6 karakter</small>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="confirm_password">Konfirmasi Password Baru</label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-input" autocomplete="new-password">
                                </div>
                            </div>

                            <div class="form-footer">
                                <a href="dashboard.php" class="btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="main-footer">
                <p>&copy; 2025 <strong>DPPKBPM</strong> - Dinas Pengendalian Penduduk dan Keluarga Berencana Pemberdayaan Masyarakat</p>
            </footer>
        </main>
    </div>

    <script src="../js/dashboard.js"></script>
    <script>
        // Form validation
        document.getElementById('editAccountForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const currentPassword = document.getElementById('current_password').value;

            // If trying to change password
            if (newPassword || confirmPassword) {
                if (!currentPassword) {
                    e.preventDefault();
                    alert('Masukkan password saat ini untuk mengubah password');
                    return false;
                }

                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('Password baru dan konfirmasi password tidak cocok');
                    return false;
                }

                if (newPassword.length < 6) {
                    e.preventDefault();
                    alert('Password baru minimal 6 karakter');
                    return false;
                }
            }
        });
    </script>
</body>

</html>
