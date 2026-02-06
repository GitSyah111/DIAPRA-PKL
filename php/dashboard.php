<?php
require_once 'auth_check.php';
include 'database.php';

// Hitung total surat masuk
$query_masuk = "SELECT COUNT(*) as total FROM surat_masuk";
$result_masuk = mysqli_query($conn, $query_masuk);
$total_masuk = mysqli_fetch_assoc($result_masuk)['total'];

// Hitung total surat keluar
$query_keluar = "SELECT COUNT(*) as total FROM surat_keluar";
$result_keluar = mysqli_query($conn, $query_keluar);
$total_keluar = mysqli_fetch_assoc($result_keluar)['total'];

// Hitung surat belum disposisi
$query_pending = "SELECT COUNT(*) as total FROM surat_masuk WHERE status_disposisi = 'Belum diproses'";
$result_pending = mysqli_query($conn, $query_pending);
$total_pending = mysqli_fetch_assoc($result_pending)['total'];

// Hitung total pengguna
$query_users = "SELECT COUNT(*) as total FROM user";
$result_users = mysqli_query($conn, $query_users);
$total_users = mysqli_fetch_assoc($result_users)['total'];

// Ambil 3 aktivitas terbaru (gabungan surat masuk dan keluar)
$aktivitas = [];

// Surat masuk terbaru
$query_aktivitas_masuk = "SELECT 'masuk' as tipe, nomor_surat, perihal, created_at FROM surat_masuk ORDER BY created_at DESC LIMIT 2";
$result_aktivitas_masuk = mysqli_query($conn, $query_aktivitas_masuk);
while ($row = mysqli_fetch_assoc($result_aktivitas_masuk)) {
    $aktivitas[] = $row;
}

// Surat keluar terbaru
$query_aktivitas_keluar = "SELECT 'keluar' as tipe, nomor_surat, perihal, created_at FROM surat_keluar ORDER BY created_at DESC LIMIT 2";
$result_aktivitas_keluar = mysqli_query($conn, $query_aktivitas_keluar);
while ($row = mysqli_fetch_assoc($result_aktivitas_keluar)) {
    $aktivitas[] = $row;
}

// Sort berdasarkan waktu terbaru dan ambil 3 teratas
usort($aktivitas, function ($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});
$aktivitas = array_slice($aktivitas, 0, 3);

// Fungsi untuk menghitung waktu relatif
function time_elapsed_string($datetime)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->d == 0) {
        if ($diff->h == 0) {
            if ($diff->i == 0) {
                return 'Baru saja';
            }
            return $diff->i . ' menit yang lalu';
        }
        return $diff->h . ' jam yang lalu';
    } elseif ($diff->d == 1) {
        return '1 hari yang lalu';
    } elseif ($diff->d < 7) {
        return $diff->d . ' hari yang lalu';
    } elseif ($diff->d < 30) {
        return floor($diff->d / 7) . ' minggu yang lalu';
    } else {
        return date('d/m/Y', strtotime($datetime));
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DPPKBPM - Sistem Manajemen Surat</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="../assets/img/LOGO.png" alt="Logo Pemko" class="logo-img">
                </div>
                <h2 class="sidebar-text">DIAPRA DPPKBPM</h2>
            </div>

            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active" title="Dashboard">
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
                    <h1 class="header-title">Dashboard</h1>
                </div>
                <div class="header-right">
                    <div class="user-info" id="userInfoToggle">
                        <span class="user-name"><?= htmlspecialchars($nama) ?></span>
                        <span class="user-role"><?= ucfirst(htmlspecialchars($role)) ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="edit-akun.php">
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
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <a href="surat-masuk.php" class="stat-card blue" style="text-decoration: none; color: inherit;">
                        <div class="stat-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Surat Masuk</h3>
                            <p class="stat-number"><?php echo $total_masuk; ?></p>
                            <span class="stat-label">Total keseluruhan</span>
                        </div>
                    </a>

                    <a href="surat-keluar.php" class="stat-card green" style="text-decoration: none; color: inherit;">
                        <div class="stat-icon">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Surat Keluar</h3>
                            <p class="stat-number"><?php echo $total_keluar; ?></p>
                            <span class="stat-label">Total keseluruhan</span>
                        </div>
                    </a>

                    <a href="surat-belum-disposisi.php" class="stat-card orange" style="text-decoration: none; color: inherit;">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Belum Disposisi</h3>
                            <p class="stat-number"><?php echo $total_pending; ?></p>
                            <span class="stat-label">Menunggu disposisi</span>
                        </div>
                    </a>

                    <?php if ($role !== 'user'): ?>
                    <a href="data-pengguna.php" class="stat-card purple" style="text-decoration: none; color: inherit;">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Pengguna</h3>
                            <p class="stat-number"><?php echo $total_users; ?></p>
                            <span class="stat-label">Pengguna aktif</span>
                        </div>
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Recent Activity -->
                <div class="content-box">
                    <div class="box-header">
                        <h2><i class="fas fa-history"></i> Aktivitas Terbaru</h2>
                        <a href="surat-masuk.php" class="view-all">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="activity-list">
                        <?php if (count($aktivitas) > 0): ?>
                            <?php foreach ($aktivitas as $item): ?>
                                <div class="activity-item">
                                    <div class="activity-icon <?php echo $item['tipe'] == 'masuk' ? 'incoming' : 'outgoing'; ?>">
                                        <i class="fas fa-<?php echo $item['tipe'] == 'masuk' ? 'envelope' : 'paper-plane'; ?>"></i>
                                    </div>
                                    <div class="activity-content">
                                        <h4>Surat <?php echo ucfirst($item['tipe']); ?> <?php echo $item['tipe'] == 'masuk' ? 'Baru' : 'Terkirim'; ?></h4>
                                        <p>Surat perihal "<?php echo htmlspecialchars(substr($item['perihal'], 0, 60)); ?><?php echo strlen($item['perihal']) > 60 ? '...' : ''; ?>" - No: <?php echo htmlspecialchars($item['nomor_surat']); ?></p>
                                        <span class="activity-time">
                                            <i class="fas fa-clock"></i> <?php echo time_elapsed_string($item['created_at']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="activity-item">
                                <div class="activity-content" style="text-align: center; width: 100%;">
                                    <p style="color: #9ca3af;">Belum ada aktivitas</p>
                                </div>
                            </div>
                        <?php endif; ?>
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
</body>

</html>