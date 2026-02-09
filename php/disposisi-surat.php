<?php
// Koneksi database
include 'database.php';
require_once 'auth_check.php';

// Cek apakah ada ID
if (!isset($_GET['id'])) {
    header("Location: surat-masuk.php");
    exit();
}

// Security Check: Hanya Admin/Super Admin yang boleh akses halaman ini
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super_admin') {
    echo "<script>alert('Akses ditolak! Halaman ini hanya untuk Admin.'); window.location.href = 'surat-masuk.php';</script>";
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data surat masuk
$query = "SELECT * FROM surat_masuk WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>
        alert('Data surat tidak ditemukan!');
        window.location.href = 'surat-masuk.php';
    </script>";
    exit();
}

$surat = mysqli_fetch_assoc($result);

// Ambil data user untuk dropdown "Dapat dilihat oleh"
$query_users = "SELECT nama FROM user ORDER BY nama ASC";
$result_users = mysqli_query($conn, $query_users);

// Ambil data user BIDANG untuk dropdown "Tujuan Disposisi"
$query_bidang = "SELECT nama_bidang FROM user WHERE role = 'bidang' AND nama_bidang IS NOT NULL AND nama_bidang != '' ORDER BY nama_bidang ASC";
$result_bidang = mysqli_query($conn, $query_bidang);

// Array dilihat oleh yang sudah dipilih
$dilihat_array = array();
if (!empty($surat['dilihat_oleh'])) {
    $dilihat_array = explode(', ', $surat['dilihat_oleh']);
}


?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unggah Disposisi - DPPKBPM</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/kepala-dinas.css">
    <link rel="stylesheet" href="../css/surat-masuk.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ... (CSS Modal Anda tetap sama) ... */
        .preview-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .preview-modal-content {
            background: white;
            border-radius: 12px;
            width: 95%;
            max-width: 1200px;
            height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .preview-modal-header {
            padding: 20px 25px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 12px 12px 0 0;
        }

        .preview-modal-header h3 {
            margin: 0;
            color: #1e3a5f;
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .preview-modal-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #6b7280;
            cursor: pointer;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s;
        }

        .preview-modal-close:hover {
            background: #f3f4f6;
            color: #1e3a5f;
            transform: rotate(90deg);
        }

        .preview-modal-body {
            flex: 1;
            padding: 0;
            overflow: hidden;
            position: relative;
            background: #f9fafb;
        }

        .preview-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 10;
        }

        .preview-loading i {
            font-size: 48px;
            color: #3b82f6;
            margin-bottom: 15px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .preview-loading p {
            color: #6b7280;
            font-size: 16px;
        }

        .preview-modal-body iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .preview-modal-footer {
            padding: 15px 25px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: #f9fafb;
            border-radius: 0 0 12px 12px;
        }

        .preview-modal-footer .btn-secondary,
        .preview-modal-footer .btn-primary {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            border: none;
            text-decoration: none;
        }

        .preview-modal-footer .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .preview-modal-footer .btn-secondary:hover {
            background: #4b5563;
            transform: translateY(-2px);
        }

        .preview-modal-footer .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .preview-modal-footer .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .preview-modal-content {
                width: 98%;
                height: 95vh;
            }

            .preview-modal-header {
                padding: 15px;
            }

            .preview-modal-header h3 {
                font-size: 16px;
            }

            .preview-modal-footer {
                flex-direction: column;
            }

            .preview-modal-footer .btn-secondary,
            .preview-modal-footer .btn-primary {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="hover-trigger"></div>
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
                <a href="surat-masuk.php" class="nav-item active" title="Surat Masuk">
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

        <main class="main-content">
            <header class="header">
                <div class="header-left">
                    <button class="menu-toggle" id="mobileMenuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <button class="header-menu-btn" id="headerMenuBtn">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="header-title">Unggah Disposisi <?php echo htmlspecialchars($surat['nomor_surat']); ?></h1>
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
                        <a href="logout.php" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </header>

            <div class="content">
                <div class="breadcrumb">
                    <a href="dashboard.php">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="surat-masuk.php">Surat Masuk</a>
                    <span class="separator">/</span>
                    <a href="disposisi-surat.php?id=<?php echo $surat['id']; ?>">Disposisi</a>
                    <span class="separator">/</span>
                    <span class="current">Unggah Disposisi</span>
                </div>

                <div class="content-box info-surat-box">
                    <div class="box-header">
                        <h2><i class="fas fa-info-circle"></i> Informasi Surat</h2>
                    </div>
                    <div class="info-content">
                        <div class="info-row">
                            <div class="info-item">
                                <label><i class="fas fa-hashtag"></i> Nomor Agenda:</label>
                                <span><?php echo htmlspecialchars($surat['nomor_agenda']); ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-calendar-check"></i> Tanggal Terima:</label>
                                <span><?php echo date('d F Y', strtotime($surat['tanggal_terima'])); ?></span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-item">
                                <label><i class="fas fa-building"></i> Pengirim:</label>
                                <span><?php echo htmlspecialchars($surat['alamat_pengirim']); ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-calendar-alt"></i> Tanggal Surat:</label>
                                <span><?php echo date('d F Y', strtotime($surat['tanggal_surat'])); ?></span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-item full-width">
                                <label><i class="fas fa-align-left"></i> Perihal:</label>
                                <span><?php echo htmlspecialchars($surat['perihal']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content-box">
                    <div class="box-header">
                        <h2><i class="fas fa-share-square"></i> Form Disposisi</h2>
                        <div class="header-actions">
                            <button type="button" class="btn-print" onclick="openPreviewModal()">
                                <i class="fas fa-print"></i> Cetak Lembar Disposisi
                            </button>
                        </div>
                    </div>
                    <?php
// Ambil data disposisi terakhir jika ada
$query_disp = "SELECT * FROM disposisi WHERE id_surat_masuk = '$id' ORDER BY id DESC LIMIT 1";
$result_disp = mysqli_query($conn, $query_disp);
$disposisi_data = mysqli_fetch_assoc($result_disp);

// Siapkan variabel default
$sifat_value = $disposisi_data['sifat'] ?? '';
$tujuan_value = $disposisi_data['tujuan_bidang'] ?? '';
$batas_waktu_value = $disposisi_data['batas_waktu'] ?? '';
$isi_disposisi_value = $disposisi_data['isi_disposisi'] ?? '';
$catatan_value = $disposisi_data['catatan'] ?? '';
$file_disposisi_value = $disposisi_data['file_disposisi'] ?? '';
$id_disposisi = $disposisi_data['id'] ?? '';
?>

                    <div class="form-container">
                        <form id="formDisposisi" method="POST" action="proses-disposisi.php" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="disposisi">
                            <input type="hidden" name="id" value="<?php echo $surat['id']; ?>">
                            <!-- Tambahkan ID Disposisi untuk mode Edit -->
                            <input type="hidden" name="id_disposisi" value="<?php echo $id_disposisi; ?>">

                            <div class="form-group">
                                <label for="nomor_surat">
                                    <i class="fas fa-file-signature"></i> Nomor Surat
                                </label>
                                <input type="text" id="nomor_surat" name="nomor_surat"
                                    value="<?php echo htmlspecialchars($surat['nomor_surat']); ?>"
                                    readonly
                                    class="readonly-input">
                            </div>

                            <!-- Sifat Surat (Dropdown update) -->
                            <div class="form-group">
                                <label for="sifat_surat">Sifat Surat <span class="required">*</span></label>
                                <select id="sifat_surat" name="sifat_surat" class="form-control" required>
                                    <option value="">-- Pilih Sifat Surat --</option>
                                    <option value="Biasa" <?php echo ($sifat_value == 'Biasa' || $surat['sifat_surat'] == 'Biasa') ? 'selected' : ''; ?>>Biasa</option>
                                    <option value="Penting" <?php echo ($sifat_value == 'Penting' || $surat['sifat_surat'] == 'Penting') ? 'selected' : ''; ?>>Penting</option>
                                    <option value="Segera" <?php echo ($sifat_value == 'Segera' || $surat['sifat_surat'] == 'Segera') ? 'selected' : ''; ?>>Segera</option>
                                    <option value="Rahasia" <?php echo ($sifat_value == 'Rahasia' || $surat['sifat_surat'] == 'Rahasia') ? 'selected' : ''; ?>>Rahasia</option>
                                </select>
                            </div>

                            <!-- Batas Waktu (New Field) -->
                            <div class="form-group">
                                <label for="batas_waktu">Batas Waktu</label>
                                <input type="date" id="batas_waktu" name="batas_waktu" class="form-control" value="<?php echo $batas_waktu_value; ?>">
                            </div>

                            <!-- Tujuan Disposisi (New Interface: Dropdown Bidang) -->
                            <div class="form-group">
                                <label for="tujuan_disposisi">Tujuan Disposisi (Bidang) <span class="required">*</span></label>
                                <select id="tujuan_disposisi" name="tujuan_disposisi" class="form-control" required>
                                    <option value="">-- Pilih Bidang Tujuan --</option>
                                    <?php 
                                    mysqli_data_seek($result_bidang, 0); // Reset pointer
                                    while ($bidang = mysqli_fetch_assoc($result_bidang)): 
                                    ?>
                                        <option value="<?php echo htmlspecialchars($bidang['nama_bidang']); ?>" <?php echo ($tujuan_value == $bidang['nama_bidang']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($bidang['nama_bidang']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <!-- Instruksi/Isi Disposisi (Textarea) -->
                            <div class="form-group">
                                <label for="instruksi_disposisi">Isi Instruksi / Disposisi <span class="required">*</span></label>
                                <textarea id="instruksi_disposisi" name="instruksi_disposisi" class="form-control" rows="4" required placeholder="Tuliskan instruksi disposisi di sini..."><?php echo htmlspecialchars($isi_disposisi_value); ?></textarea>
                            </div>

                            <!-- Catatan Disposisi (Textarea) -->
                            <div class="form-group">
                                <label for="catatan_disposisi">Catatan Disposisi</label>
                                <textarea id="catatan_disposisi" name="catatan_disposisi" class="form-control" rows="3" placeholder="Tambahkan catatan jika ada..."><?php echo htmlspecialchars($catatan_value); ?></textarea>
                            </div>

                            <!-- File Disposisi -->
                            <div class="form-group">
                                <label for="file_disposisi">
                                    File Disposisi (PDF Only) <?php echo empty($file_disposisi_value) ? '<span class="required">*</span>' : ''; ?>
                                    <small style="display:block; color:#666; font-weight:normal;">Upload lembar disposisi yang sudah discan</small>
                                </label>
                                
                                <?php if (!empty($file_disposisi_value)): ?>
                                    <div class="file-info-display">
                                        <div class="file-info-row">
                                            <div class="file-info-details">
                                                <i class="fas fa-file-pdf"></i>
                                                <div class="file-info-text">
                                                    <span class="file-info-label">File saat ini:</span>
                                                    <span class="file-info-name"><?php echo htmlspecialchars($file_disposisi_value); ?></span>
                                                </div>
                                            </div>
                                            <div class="file-info-actions">
                                                <a href="../uploads/disposisi/<?php echo $file_disposisi_value; ?>" target="_blank" class="btn-view-small">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="file-upload-wrapper">
                                    <input type="file" id="file_disposisi" name="file_disposisi" class="file-upload-input" accept=".pdf" <?php echo empty($file_disposisi_value) ? 'required' : ''; ?> onchange="updateFileName(this)">
                                    <label for="file_disposisi" class="file-upload-label">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span><?php echo !empty($file_disposisi_value) ? 'Ganti file PDF...' : 'Pilih file PDF...'; ?></span>
                                    </label>
                                    <div class="file-name" id="file-name-display">Belum ada file dipilih</div>
                                </div>
                            </div>
                            <div class="form-footer">
                                <a href="surat-masuk.php" class="btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-save"></i> Simpan Disposisi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>



            </div>

            <footer class="main-footer">
                <p>&copy; 2025 <strong>DPPKBPM</strong> - Dinas Pengendalian Penduduk dan Keluarga Berencana Pemberdayaan Masyarakat</p>
            </footer>
        </main>
    </div>

    <script src="../js/dashboard.js"></script>
    <script>
        // ... (Form validation tetap sama) ...
        document.getElementById('formDisposisi').addEventListener('submit', function(e) {
            const tujuanChecked = document.querySelectorAll('input[name="tujuan_disposisi[]"]:checked').length.length;

            if (tujuanChecked === 0) {
                e.preventDefault();
                alert('Pilih minimal 1 tujuan disposisi!');
                return false;
            }

            return true;
        });

        // =============================================
        // PERBAIKAN: NAMA FILE DIPANGGIL DARI FILE BARU
        // =============================================
        function openPreviewModal() {
            const modal = document.createElement('div');
            modal.id = 'previewModal';
            modal.className = 'preview-modal';

            // =========================================================================
            // INI ADALAH PERUBAHAN UTAMA
            // =========================================================================
            const pdfUrl = "cetak-disposisi-final.php?id=<?php echo $surat['id']; ?>";
            const downloadUrl = "cetak-disposisi-final.php?id=<?php echo $surat['id']; ?>";
            // =========================================================================

            modal.innerHTML = `
                <div class="preview-modal-content">
                    <div class="preview-modal-header">
                        <h3><i class="fas fa-file-pdf"></i> Preview Lembar Disposisi</h3>
                        <button class="preview-modal-close" onclick="closePreviewModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="preview-modal-body">
                        <div class="preview-loading">
                            <i class="fas fa-spinner"></i>
                            <p>Memuat preview...</p>
                        </div>
                        <iframe id="pdfPreview" src="${pdfUrl}" frameborder="0"></iframe>
                    </div>
                    <div class="preview-modal-footer">
                        <button class="btn-secondary" onclick="closePreviewModal()">
                            <i class="fas fa-times"></i> Tutup
                        </button>
                        <button class="btn-primary" onclick="printPreview()">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <a href="${downloadUrl}" download class="btn-primary">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);

            // Hide loading when iframe loaded
            const iframe = document.getElementById('pdfPreview');
            iframe.onload = function() {
                document.querySelector('.preview-loading').style.display = 'none';
            };
        }

        function closePreviewModal() {
            const modal = document.getElementById('previewModal');
            if (modal) {
                modal.remove();
            }
        }

        function printPreview() {
            const iframe = document.getElementById('pdfPreview');
            if (iframe) {
                iframe.contentWindow.print();
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'previewModal') {
                closePreviewModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePreviewModal();
            }
        });
    </script>
</body>

</html>