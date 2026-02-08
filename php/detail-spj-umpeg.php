<?php
// Koneksi database
include 'database.php';
require_once 'auth_check.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data SPJ UMPEG berdasarkan ID
$query = "SELECT * FROM spj_umpeg WHERE id = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='spj-umpeg.php';</script>";
    exit;
}

$data = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail SPJ UMPEG - DPPKBPM</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/kepala-dinas.css">
    <link rel="stylesheet" href="../css/surat-masuk.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .detail-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .detail-header {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .detail-header h2 {
            color: #1e3a5f;
            font-size: 24px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .detail-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f0f2f5;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-label i {
            color: #3b82f6;
            width: 20px;
        }

        .detail-value {
            color: #1f2937;
            font-size: 15px;
            line-height: 1.6;
        }

        .file-preview {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            margin-top: 10px;
        }

        .file-preview i {
            color: #3b82f6;
            font-size: 24px;
        }

        .file-info {
            flex: 1;
        }

        .file-info strong {
            display: block;
            color: #1e3a5f;
            margin-bottom: 4px;
        }

        .file-info small {
            color: #6b7280;
        }

        .btn-view-pdf {
            padding: 8px 16px;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-view-pdf:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
        }

        .action-buttons-detail {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        @media print {

            .sidebar,
            .header,
            .breadcrumb,
            .action-buttons-detail,
            .main-footer,
            .file-preview {
                display: none !important;
            }

            .main-content {
                margin-left: 0 !important;
            }

            .detail-card {
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
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
                <a href="spj-umpeg.php" class="nav-item active" title="SPJ UMPEG">
                    <i class="fas fa-file-invoice"></i>
                    <span class="sidebar-text">SPJ UMPEG</span>
                </a>
                <?php endif; ?>
                <a href="surat-cuti.php" class="nav-item" title="Surat Cuti">
                    <i class="fas fa-calendar-check"></i>
                    <span class="sidebar-text">Surat Cuti</span>
                </a>
                <a href="data-pengguna.php" class="nav-item" title="Data Pengguna">
                    <i class="fas fa-users"></i>
                    <span class="sidebar-text">Data Pengguna</span>
                </a>
                <a href="data-kepala-dinas.php" class="nav-item" title="Data Kepala Dinas">
                    <i class="fas fa-user-tie"></i>
                    <span class="sidebar-text">Data Kepala Dinas</span>
                </a>
            </nav>

            <div class="sidebar-footer sidebar-text">
                <p><i class="fas fa-info-circle"></i> Versi 1.0.0</p>
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
                    <h1 class="header-title"><button class="header-menu-btn" id="headerMenuBtn">
                        <i class="fas fa-bars"></i>
                    </button>
                    Detail SPJ UMPEG</h1>
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
                <!-- Breadcrumb -->
                <div class="breadcrumb">
                    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                    <span class="separator">/</span>
                    <a href="spj-umpeg.php">SPJ UMPEG</a>
                    <span class="separator">/</span>
                    <span class="current">Detail SPJ UMPEG</span>
                </div>

                <!-- Detail Card -->
                <div class="detail-card">
                    <div class="detail-header">
                        <h2>
                            <i class="fas fa-file-invoice"></i>
                            Informasi SPJ UMPEG
                        </h2>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-sort-numeric-up"></i>
                            Nomor Urut
                        </div>
                        <div class="detail-value">
                            <?php echo htmlspecialchars($data['nomor_urut']); ?>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-file-alt"></i>
                            Nomor SPJ
                        </div>
                        <div class="detail-value">
                            <?php echo htmlspecialchars($data['nomor_spj']); ?>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-calendar-alt"></i>
                            Tanggal
                        </div>
                        <div class="detail-value">
                            <?php echo date('d F Y', strtotime($data['tanggal'])); ?>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-tasks"></i>
                            Nama Kegiatan
                        </div>
                        <div class="detail-value">
                            <?php echo nl2br(htmlspecialchars($data['nama_kegiatan'])); ?>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-user-edit"></i>
                            Dibuat Oleh
                        </div>
                        <div class="detail-value">
                            <?php echo htmlspecialchars($data['dibuat_oleh']); ?>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-file-pdf"></i>
                            File SPJ
                        </div>
                        <div class="detail-value">
                            <?php if (!empty($data['file_spj'])): ?>
                                <div class="file-preview">
                                    <i class="fas fa-file-pdf"></i>
                                    <div class="file-info">
                                        <strong><?php echo htmlspecialchars($data['file_spj']); ?></strong>
                                        <small>Format: PDF</small>
                                    </div>
                                    <a href="../uploads/spj_umpeg/<?php echo $data['file_spj']; ?>"
                                        target="_blank" class="btn-view-pdf">
                                        <i class="fas fa-eye"></i> Lihat PDF
                                    </a>
                                </div>
                            <?php else: ?>
                                <span style="color: #9ca3af; font-style: italic;">Tidak ada file terlampir</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="action-buttons-detail">
                        <a href="spj-umpeg.php" class="btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Kembali
                        </a>
                        <a href="edit-spj-umpeg.php?id=<?php echo $data['id']; ?>" class="btn-primary">
                            <i class="fas fa-edit"></i>
                            Edit Data
                        </a>
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

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="../js/dashboard.js"></script>
</body>

</html>