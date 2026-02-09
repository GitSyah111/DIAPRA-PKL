<?php
require_once 'auth_check.php';
include 'database.php'; // koneksi.php sudah dimodifikasi untuk handle session tahun

// Ambil tahun aktif dari session untuk label atau query filtering (meski koneksi sudah ke DB tahun tsb)
$tahun_aktif = $_SESSION['tahun_aktif'] ?? date('Y');

// Fungsi helper untuk inisialisasi array bulanan
function initMonthlyArray() {
    return array_fill(1, 12, 0); // Index 1-12 untuk Jan-Des
}

// 1. Data Surat Masuk per Bulan
$stats_masuk = initMonthlyArray();
$q_masuk = "SELECT MONTH(tanggal_terima) as bulan, COUNT(*) as total FROM surat_masuk WHERE YEAR(tanggal_terima) = '$tahun_aktif' GROUP BY MONTH(tanggal_terima)";
$res_masuk = mysqli_query($conn, $q_masuk);
while($row = mysqli_fetch_assoc($res_masuk)) {
    $stats_masuk[$row['bulan']] = (int)$row['total'];
}

// 2. Data Surat Keluar per Bulan
$stats_keluar = initMonthlyArray();
$q_keluar = "SELECT MONTH(tanggal_surat) as bulan, COUNT(*) as total FROM surat_keluar WHERE YEAR(tanggal_surat) = '$tahun_aktif' GROUP BY MONTH(tanggal_surat)";
$res_keluar = mysqli_query($conn, $q_keluar);
while($row = mysqli_fetch_assoc($res_keluar)) {
    $stats_keluar[$row['bulan']] = (int)$row['total'];
}

// 3. Data SPJ UMPEG per Bulan
$stats_spj = initMonthlyArray();
// Cek dulu apakah tabel spj_umpeg ada (untuk jaga-jaga)
$check_spj = mysqli_query($conn, "SHOW TABLES LIKE 'spj_umpeg'");
if(mysqli_num_rows($check_spj) > 0) {
    $q_spj = "SELECT MONTH(tanggal) as bulan, COUNT(*) as total FROM spj_umpeg WHERE YEAR(tanggal) = '$tahun_aktif' GROUP BY MONTH(tanggal)";
    $res_spj = mysqli_query($conn, $q_spj);
    if($res_spj) {
        while($row = mysqli_fetch_assoc($res_spj)) {
            $stats_spj[$row['bulan']] = (int)$row['total'];
        }
    }
}

// 4. Data Surat Cuti per Bulan
// Kolom 'Mulai Cuti' adalah INT (Unix Timestamp), Tabel 'surat cuti' (pakai backtick)
$stats_cuti = initMonthlyArray();
$check_cuti = mysqli_query($conn, "SHOW TABLES LIKE 'surat cuti'");
if(mysqli_num_rows($check_cuti) > 0) {
    // FROM_UNIXTIME mengubah int ke datetime, lalu ambil MONTH
    $q_cuti = "SELECT MONTH(FROM_UNIXTIME(`Mulai Cuti`)) as bulan, COUNT(*) as total FROM `surat cuti` WHERE YEAR(FROM_UNIXTIME(`Mulai Cuti`)) = '$tahun_aktif' GROUP BY MONTH(FROM_UNIXTIME(`Mulai Cuti`))";
    $res_cuti = mysqli_query($conn, $q_cuti);
    if($res_cuti) {
        while($row = mysqli_fetch_assoc($res_cuti)) {
            $stats_cuti[$row['bulan']] = (int)$row['total'];
        }
    }
}

// Prepare data for Chart.js (re-index to 0-11 if needed, but array_values is safer for JSON)
$json_masuk = json_encode(array_values($stats_masuk));
$json_keluar = json_encode(array_values($stats_keluar));
$json_spj = json_encode(array_values($stats_spj));
$json_cuti = json_encode(array_values($stats_cuti));

// --- EXISTING COUNTS LOGIC ---
// Hitung total surat masuk
$query_masuk_total = "SELECT COUNT(*) as total FROM surat_masuk";
$result_masuk_total = mysqli_query($conn, $query_masuk_total);
$total_masuk = mysqli_fetch_assoc($result_masuk_total)['total'];

// Hitung total surat keluar
$query_keluar_total = "SELECT COUNT(*) as total FROM surat_keluar";
$result_keluar_total = mysqli_query($conn, $query_keluar_total);
$total_keluar = mysqli_fetch_assoc($result_keluar_total)['total'];

// Hitung surat belum disposisi
$query_pending = "SELECT COUNT(*) as total FROM surat_masuk WHERE status_disposisi = 'Belum diproses'";
$result_pending = mysqli_query($conn, $query_pending);
$total_pending = mysqli_fetch_assoc($result_pending)['total'];

// Hitung total pengguna
$query_users = "SELECT COUNT(*) as total FROM user";
$result_users = mysqli_query($conn, $query_users);
$total_users = mysqli_fetch_assoc($result_users)['total'];

// Hitung total SPJ UMPEG
$total_spj = 0;
$check_spj_table = mysqli_query($conn, "SHOW TABLES LIKE 'spj_umpeg'");
if (mysqli_num_rows($check_spj_table) > 0) {
    $query_spj = "SELECT COUNT(*) as total FROM spj_umpeg";
    $result_spj = mysqli_query($conn, $query_spj);
    if ($result_spj) {
        $total_spj = mysqli_fetch_assoc($result_spj)['total'];
    }
}

// Hitung total Surat Cuti
$total_cuti = 0;
$check_cuti_table = mysqli_query($conn, "SHOW TABLES LIKE 'surat cuti'");
if (mysqli_num_rows($check_cuti_table) > 0) {
    $query_cuti = "SELECT COUNT(*) as total FROM `surat cuti`";
    $result_cuti = mysqli_query($conn, $query_cuti);
    if ($result_cuti) {
        $total_cuti = mysqli_fetch_assoc($result_cuti)['total'];
    }
}

// Hitung Disposisi Masuk (Khusus Role Bidang)
$total_disposisi_masuk = 0;
if ($role == 'bidang') {
    $nama_bidang = $_SESSION['nama_bidang'] ?? '';
    $query_disp_bidang = "SELECT COUNT(*) as total FROM disposisi WHERE tujuan_bidang = '$nama_bidang'";
    $result_disp_bidang = mysqli_query($conn, $query_disp_bidang);
    if ($result_disp_bidang) {
        $total_disposisi_masuk = mysqli_fetch_assoc($result_disp_bidang)['total'];
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
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
    <div class="container">
        <div class="hover-trigger"></div>
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

                <?php if ($role == 'bidang'): ?>
                <a href="disposisi-masuk.php" class="nav-item" title="Disposisi Masuk">
                    <i class="fas fa-inbox"></i>
                    <span class="sidebar-text">Disposisi Masuk</span>
                </a>
                <?php endif; ?>

                <?php if ($role !== 'user' && $role !== 'bidang'): ?>
                <a href="spj-umpeg.php" class="nav-item" title="SPJ UMPEG">
                    <i class="fas fa-file-invoice"></i>
                    <span class="sidebar-text">SPJ UMPEG</span>
                </a>
                <?php endif; ?>
                <a href="surat-cuti.php" class="nav-item" title="Surat Cuti">
                    <i class="fas fa-calendar-check"></i>
                    <span class="sidebar-text">Surat Cuti</span>
                </a>
                <?php if ($role !== 'user' && $role !== 'bidang'): ?>
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
                <!-- Indikator Tahun Aktif -->
                <div style="margin-top: 10px; font-size: 0.8rem; color: #a1a1aa;">
                    Data Tahun: <strong><?= htmlspecialchars($tahun_aktif) ?></strong>
                    <br>
                    <a href="pilih_tahun.php" style="color: #60a5fa; text-decoration: none;">(Ganti Tahun)</a>
                </div>
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
                    <h1 class="header-title">Dashboard (<?= htmlspecialchars($tahun_aktif) ?>)</h1>
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
                        <button class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </div>
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

                    <?php if ($role == 'bidang'): ?>
                        <a href="disposisi-masuk.php" class="stat-card orange" style="text-decoration: none; color: inherit;">
                            <div class="stat-icon">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Disposisi Masuk</h3>
                                <p class="stat-number"><?php echo $total_disposisi_masuk; ?></p>
                                <span class="stat-label">Total Disposisi Diterima</span>
                            </div>
                        </a>
                    <?php endif; ?>

                    <!-- New Card: SPJ UMPEG -->
                    <?php if ($role !== 'user' && $role !== 'bidang'): ?>
                    <a href="spj-umpeg.php" class="stat-card indigo" style="text-decoration: none; color: inherit;">
                        <div class="stat-icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div class="stat-info">
                            <h3>SPJ UMPEG</h3>
                            <p class="stat-number"><?php echo $total_spj; ?></p>
                            <span class="stat-label">Total Dokumen</span>
                        </div>
                    </a>
                    <?php endif; ?>

                    <!-- New Card: Surat Cuti -->
                    <a href="surat-cuti.php" class="stat-card teal" style="text-decoration: none; color: inherit;">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Surat Cuti</h3>
                            <p class="stat-number"><?php echo $total_cuti; ?></p>
                            <span class="stat-label">Total Pengajuan</span>
                        </div>
                    </a>

                    <?php if ($role !== 'user' && $role !== 'bidang'): ?>
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

                <!-- CHART SECTION -->
                <div class="content-box" style="margin-bottom: 2rem;">
                    <div class="box-header">
                        <h2><i class="fas fa-chart-line"></i> Statistik Surat Tahun <?= htmlspecialchars($tahun_aktif) ?></h2>
                    </div>
                    <div style="padding: 1.5rem; height: 400px;">
                        <canvas id="suratChart"></canvas>
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
    <style>
        /* Ensure only one menu button shows at a time */
        .menu-toggle {
            display: block;
        }
        
        .header-menu-btn {
            display: none;
        }
        
        @media (min-width: 769px) {
            .menu-toggle {
                display: none;
            }
            
            .header-menu-btn {
                display: block;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('suratChart').getContext('2d');
            
            // Create Gradients for Area Fill (Neon Style)
            // Neon Indigo for Surat Masuk (#6366f1)
            const gradientMasuk = ctx.createLinearGradient(0, 0, 0, 400);
            gradientMasuk.addColorStop(0, 'rgba(99, 102, 241, 0.4)'); 
            gradientMasuk.addColorStop(1, 'rgba(99, 102, 241, 0.0)'); 

            // Neon Rose for Surat Keluar (#f43f5e)
            const gradientKeluar = ctx.createLinearGradient(0, 0, 0, 400);
            gradientKeluar.addColorStop(0, 'rgba(244, 63, 94, 0.4)'); 
            gradientKeluar.addColorStop(1, 'rgba(244, 63, 94, 0.0)'); 
            
            // Data dari PHP
            const dataMasuk = <?= $json_masuk ?>;
            const dataKeluar = <?= $json_keluar ?>;

            // Global Defaults for High-End Look
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#94a3b8';
            
            const suratChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                    datasets: [
                        {
                            label: 'Surat Masuk',
                            data: dataMasuk,
                            borderColor: '#6366f1', // Neon Indigo
                            backgroundColor: gradientMasuk,
                            borderWidth: 5,
                            pointRadius: 0, // Hidden points
                            pointHoverRadius: 8,
                            pointHoverBackgroundColor: '#6366f1',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverBorderWidth: 3,
                            tension: 0.4,
                            cubicInterpolationMode: 'monotone',
                            fill: true,
                            // Shadow Effects via Dataset Properties (if supported/plugin) or just placeholder for style intent
                            shadowColor: 'rgba(99, 102, 241, 0.6)',
                            shadowBlur: 15,
                            shadowOffsetX: 0,
                            shadowOffsetY: 5
                        },
                        {
                            label: 'Surat Keluar',
                            data: dataKeluar,
                            borderColor: '#f43f5e', // Neon Rose
                            backgroundColor: gradientKeluar,
                            borderWidth: 5,
                            pointRadius: 0, // Hidden points
                            pointHoverRadius: 8,
                            pointHoverBackgroundColor: '#f43f5e',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverBorderWidth: 3,
                            tension: 0.4,
                            cubicInterpolationMode: 'monotone',
                            fill: true,
                            shadowColor: 'rgba(244, 63, 94, 0.6)',
                            shadowBlur: 15,
                            shadowOffsetX: 0,
                            shadowOffsetY: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 12,
                                padding: 25,
                                font: {
                                    size: 13,
                                    weight: 600
                                },
                                color: '#cbd5e1' 
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(15, 23, 42, 0.95)',
                            titleColor: '#f8fafc',
                            bodyColor: '#e2e8f0',
                            borderColor: '#334155',
                            borderWidth: 1,
                            padding: 14,
                            boxPadding: 8,
                            cornerRadius: 8,
                            titleFont: {
                                size: 14,
                                weight: 'bold',
                                family: "'Inter', sans-serif"
                            },
                            bodyFont: {
                                size: 13, 
                                family: "'Inter', sans-serif"
                            },
                            displayColors: true,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            // Tambahkan padding atas agar dot tertinggi tidak terpotong
                            suggestedMax: Math.max(...dataMasuk, ...dataKeluar) + 1, 
                            grid: {
                                color: 'rgba(241, 245, 249, 0.05)', 
                                borderDash: [5, 5],
                                drawBorder: false
                            },
                            ticks: {
                                stepSize: 1,
                                padding: 15,
                                color: '#94a3b8',
                                font: {
                                    size: 11
                                }
                            },
                            border: {
                                display: false
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                padding: 10,
                                color: '#94a3b8',
                                font: {
                                    size: 11
                                }
                            },
                            border: {
                                display: false
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    },
                    elements: {
                        point: {
                            hitRadius: 20
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>