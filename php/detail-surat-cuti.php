<?php
// Koneksi database
include 'database.php';
require_once 'auth_check.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query untuk mengambil data Surat Cuti berdasarkan ID
$query = "SELECT * FROM `surat cuti` WHERE id = $id";
// Eksekusi query
$result = mysqli_query($conn, $query);

// Cek apakah data ditemukan
if (mysqli_num_rows($result) == 0) {
    // Jika data tidak ditemukan, redirect dengan alert
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='surat-cuti.php';</script>";
    exit;
}

// Ambil data dari hasil query
$data = mysqli_fetch_assoc($result);

// Konversi timestamp Mulai Cuti ke format tanggal
$mulai_cuti = $data['Mulai Cuti'] > 0 ? date('d F Y', $data['Mulai Cuti']) : '-';
// Konversi timestamp Sampai Dengan ke format tanggal
$sampai_dengan = $data['Sampai Dengan'] > 0 ? date('d F Y', $data['Sampai Dengan']) : '-';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Meta tag untuk karakter set -->
    <meta charset="UTF-8">
    <!-- Meta tag untuk responsive design -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title halaman -->
    <title>Detail Surat Cuti - DPPKBPM</title>
    <!-- Link CSS untuk dashboard -->
    <link rel="stylesheet" href="../css/dashboard.css">
    <!-- Link CSS untuk kepala dinas -->
    <link rel="stylesheet" href="../css/kepala-dinas.css">
    <!-- Link CSS untuk surat masuk -->
    <link rel="stylesheet" href="../css/surat-masuk.css">
    <!-- Link Font Awesome untuk icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Style untuk detail card */
        .detail-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        /* Style untuk detail header */
        .detail-header {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        /* Style untuk detail header h2 */
        .detail-header h2 {
            color: #1e3a5f;
            font-size: 24px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Style untuk detail row */
        .detail-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f0f2f5;
        }

        /* Style untuk detail row terakhir */
        .detail-row:last-child {
            border-bottom: none;
        }

        /* Style untuk detail label */
        .detail-label {
            font-weight: 600;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Style untuk icon pada detail label */
        .detail-label i {
            color: #3b82f6;
            width: 20px;
        }

        /* Style untuk detail value */
        .detail-value {
            color: #1f2937;
            font-size: 15px;
            line-height: 1.6;
        }

        /* Style untuk action buttons detail */
        .action-buttons-detail {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        /* Style untuk print */
        @media print {
            /* Sembunyikan elemen saat print */
            .sidebar,
            .header,
            .breadcrumb,
            .action-buttons-detail,
            .main-footer {
                display: none !important;
            }

            /* Set margin untuk main content */
            .main-content {
                margin-left: 0 !important;
            }

            /* Hilangkan shadow saat print */
            .detail-card {
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Container utama -->
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <!-- Header sidebar -->
            <div class="sidebar-header">
                <!-- Logo -->
                <div class="logo">
                    <img src="../assets/img/LOGO.png" alt="Logo DPPKBPM" class="logo-img">
                </div>
                <!-- Nama instansi -->
                <h2 class="sidebar-text">DPPKBPM</h2>
                <!-- Subtitle instansi -->
                <p class="subtitle sidebar-text">DIAPRA</p>
                <!-- Username pengguna -->
                <p class="username sidebar-text"><i class="fas fa-user-circle"></i> @Muhammad ibnu Riayath Syah</p>
            </div>

            <!-- Navigasi sidebar -->
            <nav class="sidebar-nav">
                <!-- Menu Dashboard -->
                <a href="dashboard.php" class="nav-item" title="Dashboard">
                    <i class="fas fa-home"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
                <!-- Menu Surat Masuk -->
                <a href="surat-masuk.php" class="nav-item" title="Surat Masuk">
                    <i class="fas fa-inbox"></i>
                    <span class="sidebar-text">Surat Masuk</span>
                </a>
                <!-- Menu Surat Keluar -->
                <a href="surat-keluar.php" class="nav-item" title="Surat Keluar">
                    <i class="fas fa-paper-plane"></i>
                    <span class="sidebar-text">Surat Keluar</span>
                </a>
                <!-- Menu SPJ UMPEG -->
                <a href="spj-umpeg.php" class="nav-item" title="SPJ UMPEG">
                    <i class="fas fa-file-invoice"></i>
                    <span class="sidebar-text">SPJ UMPEG</span>
                </a>
                <!-- Menu Surat Cuti -->
                <a href="surat-cuti.php" class="nav-item active" title="Surat Cuti">
                    <i class="fas fa-calendar-check"></i>
                    <span class="sidebar-text">Surat Cuti</span>
                </a>
                <?php if ($role !== 'user'): ?>
                <!-- Menu Data Pengguna -->
                <a href="data-pengguna.php" class="nav-item" title="Data Pengguna">
                    <i class="fas fa-users"></i>
                    <span class="sidebar-text">Data Pengguna</span>
                </a>
                <!-- Menu Data Kepala Dinas -->
                <a href="data-kepala-dinas.php" class="nav-item" title="Data Kepala Dinas">
                    <i class="fas fa-user-tie"></i>
                    <span class="sidebar-text">Data Kepala Dinas</span>
                </a>
                <?php endif; ?>
            </nav>

            <!-- Footer sidebar -->
            <div class="sidebar-footer sidebar-text">
                <p><i class="fas fa-info-circle"></i> Versi 1.0.0</p>
            </div>

            <!-- Toggle Button untuk sidebar -->
            <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
                <i class="fas fa-chevron-left"></i>
            </button>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <!-- Bagian kiri header -->
                <div class="header-left">
                    <!-- Tombol menu toggle untuk mobile -->
                    <button class="menu-toggle" id="mobileMenuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <!-- Judul halaman -->
                    <h1 class="header-title">Detail Surat Cuti</h1>
                </div>
                <!-- Bagian kanan header -->
                <div class="header-right">
                    <!-- Info pengguna -->
                    <div class="user-info">
                        <span class="user-name"><?= htmlspecialchars($nama) ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <!-- Tombol logout -->
                    <button class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </button>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content">
                <!-- Breadcrumb -->
                <div class="breadcrumb">
                    <!-- Link ke dashboard -->
                    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                    <span class="separator">/</span>
                    <!-- Link ke surat cuti -->
                    <a href="surat-cuti.php">Surat Cuti</a>
                    <span class="separator">/</span>
                    <!-- Halaman saat ini -->
                    <span class="current">Detail Surat Cuti</span>
                </div>

                <!-- Detail Card -->
                <div class="detail-card">
                    <!-- Header detail -->
                    <div class="detail-header">
                        <h2>
                            <i class="fas fa-calendar-check"></i>
                            Informasi Surat Cuti
                        </h2>
                    </div>

                    <!-- Detail row Nama/NIP -->
                    <div class="detail-row">
                        <!-- Label Nama/NIP -->
                        <div class="detail-label">
                            <i class="fas fa-user"></i>
                            Nama/NIP
                        </div>
                        <!-- Value Nama/NIP -->
                        <div class="detail-value">
                            <?php echo htmlspecialchars($data['Nama/NIP']); ?>
                        </div>
                    </div>

                    <!-- Detail row Pangkat/GOL RUANG -->
                    <div class="detail-row">
                        <!-- Label Pangkat/GOL RUANG -->
                        <div class="detail-label">
                            <i class="fas fa-id-badge"></i>
                            Pangkat/GOL RUANG
                        </div>
                        <!-- Value Pangkat/GOL RUANG -->
                        <div class="detail-value">
                            <?php echo htmlspecialchars($data['Pangkat/GOL RUANG']); ?>
                        </div>
                    </div>

                    <!-- Detail row Jabatan -->
                    <div class="detail-row">
                        <!-- Label Jabatan -->
                        <div class="detail-label">
                            <i class="fas fa-briefcase"></i>
                            Jabatan
                        </div>
                        <!-- Value Jabatan -->
                        <div class="detail-value">
                            <?php echo htmlspecialchars($data['Jabatan']); ?>
                        </div>
                    </div>

                    <!-- Detail row Jenis Cuti -->
                    <div class="detail-row">
                        <!-- Label Jenis Cuti -->
                        <div class="detail-label">
                            <i class="fas fa-calendar-alt"></i>
                            Jenis Cuti
                        </div>
                        <!-- Value Jenis Cuti -->
                        <div class="detail-value">
                            <?php echo htmlspecialchars($data['Jenis Cuti']); ?>
                        </div>
                    </div>

                    <!-- Detail row Lamanya -->
                    <div class="detail-row">
                        <!-- Label Lamanya -->
                        <div class="detail-label">
                            <i class="fas fa-clock"></i>
                            Lamanya
                        </div>
                        <!-- Value Lamanya -->
                        <div class="detail-value">
                            <?php echo htmlspecialchars($data['Lamanya']); ?>
                        </div>
                    </div>

                    <!-- Detail row Dilaksanakan DI -->
                    <div class="detail-row">
                        <!-- Label Dilaksanakan DI -->
                        <div class="detail-label">
                            <i class="fas fa-map-marker-alt"></i>
                            Dilaksanakan DI
                        </div>
                        <!-- Value Dilaksanakan DI -->
                        <div class="detail-value">
                            <?php echo htmlspecialchars($data['Dilaksanakan DI']); ?>
                        </div>
                    </div>

                    <!-- Detail row Mulai Cuti -->
                    <div class="detail-row">
                        <!-- Label Mulai Cuti -->
                        <div class="detail-label">
                            <i class="fas fa-calendar-plus"></i>
                            Mulai Cuti
                        </div>
                        <!-- Value Mulai Cuti -->
                        <div class="detail-value">
                            <?php echo $mulai_cuti; ?>
                        </div>
                    </div>

                    <!-- Detail row Sampai Dengan -->
                    <div class="detail-row">
                        <!-- Label Sampai Dengan -->
                        <div class="detail-label">
                            <i class="fas fa-calendar-minus"></i>
                            Sampai Dengan
                        </div>
                        <!-- Value Sampai Dengan -->
                        <div class="detail-value">
                            <?php echo $sampai_dengan; ?>
                        </div>
                    </div>

                    <!-- Detail row Sisa Cuti -->
                    <div class="detail-row">
                        <!-- Label Sisa Cuti -->
                        <div class="detail-label">
                            <i class="fas fa-hourglass-half"></i>
                            Sisa Cuti
                        </div>
                        <!-- Value Sisa Cuti -->
                        <div class="detail-value">
                            <?php echo htmlspecialchars($data['Sisa Cuti']); ?>
                        </div>
                    </div>

                    <!-- Action buttons detail -->
                    <div class="action-buttons-detail">
                        <!-- Tombol kembali -->
                        <a href="surat-cuti.php" class="btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Kembali
                        </a>
                        <!-- Tombol edit -->
                        <a href="edit-surat-cuti.php?id=<?php echo $data['id']; ?>" class="btn-primary">
                            <i class="fas fa-edit"></i>
                            Edit Data
                        </a>
                        <!-- Tombol print -->
                        <button onclick="window.print()" class="btn-print">
                            <i class="fas fa-print"></i>
                            Cetak
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="main-footer">
                <p>&copy; 2025 <strong>DPPKBPM</strong> - Dinas Pengendalian Penduduk dan Keluarga Berencana Pemberdayaan Masyarakat</p>
            </footer>
        </main>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- JavaScript dashboard -->
    <script src="../js/dashboard.js"></script>
</body>

</html>