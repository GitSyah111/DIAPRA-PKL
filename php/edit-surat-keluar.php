<?php
// Koneksi database
include 'database.php';
require_once 'auth_check.php';

// Cek apakah ada ID
if (!isset($_GET['id'])) {
    header("Location: surat-keluar.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data surat keluar
$query = "SELECT * FROM surat_keluar WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>
        alert('Data surat tidak ditemukan!');
        window.location.href = 'surat-keluar.php';
    </script>";
    exit();
}

$surat = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Surat Keluar - DPPKBPM</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/kepala-dinas.css">
    <link rel="stylesheet" href="../css/surat-masuk.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="../assets/img/LOGO.png" alt="Logo DPPKBPM" class="logo-img">
                </div>
                <h2 class="sidebar-text">DPPKBPM</h2>
                <p class="subtitle sidebar-text">Sistem Manajemen Surat</p>
                <p class="username sidebar-text"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($nama) ?></p>
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
                <a href="surat-keluar.php" class="nav-item active" title="Surat Keluar">
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
            </div>

            <!-- Toggle Button -->
            <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
                <i class="fas fa-chevron-left"></i>
            </button>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <button class="menu-toggle" id="mobileMenuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="header-title">Edit Surat Keluar</h1>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span class="user-name"><?= htmlspecialchars($nama) ?></span>
                        <i class="fas fa-chevron-down"></i>
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
                    <a href="dashboard.php">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="surat-keluar.php">Surat Keluar</a>
                    <span class="separator">/</span>
                    <span class="current">Edit Surat Keluar</span>
                </div>

                <!-- Form Box -->
                <div class="content-box">
                    <div class="box-header">
                        <h2><i class="fas fa-edit"></i> Form Edit Surat Keluar</h2>
                    </div>

                    <div class="form-container">
                        <form id="formSuratKeluar" method="POST" action="proses-surat-keluar.php" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?php echo $surat['id']; ?>">

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nomor_urut">
                                        <i class="fas fa-hashtag"></i> Nomor
                                    </label>
                                    <input type="text" id="nomor_urut" name="nomor_urut"
                                        value="<?php echo htmlspecialchars($surat['nomor_urut']); ?>"
                                        readonly
                                        class="readonly-input">
                                    <small class="form-help">
                                        <i class="fas fa-info-circle"></i> Nomor tidak dapat diubah
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_surat">
                                        <i class="fas fa-calendar-alt"></i> Tanggal Surat <span class="required">*</span>
                                    </label>
                                    <input type="date" id="tanggal_surat" name="tanggal_surat"
                                        value="<?php echo $surat['tanggal_surat']; ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="nomor_surat">
                                    <i class="fas fa-file-signature"></i> Nomor Surat <span class="required">*</span>
                                </label>
                                <input type="text" id="nomor_surat" name="nomor_surat"
                                    value="<?php echo htmlspecialchars($surat['nomor_surat']); ?>"
                                    placeholder="Contoh: 800.1.11.2/1823/DPPKBPM/2025" required>
                            </div>

                            <div class="form-group">
                                <label for="tujuan_surat">
                                    <i class="fas fa-building"></i> Tujuan Surat <span class="required">*</span>
                                </label>
                                <input type="text" id="tujuan_surat" name="tujuan_surat"
                                    value="<?php echo htmlspecialchars($surat['tujuan_surat']); ?>"
                                    placeholder="Masukkan tujuan/penerima surat" required>
                            </div>

                            <div class="form-group">
                                <label for="perihal">
                                    <i class="fas fa-align-left"></i> Perihal <span class="required">*</span>
                                </label>
                                <textarea id="perihal" name="perihal" rows="4"
                                    placeholder="Masukkan perihal/isi surat" required><?php echo htmlspecialchars($surat['perihal']); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="file_surat">
                                    <i class="fas fa-file-pdf"></i> File Surat (PDF)
                                </label>

                                <?php if (!empty($surat['file_surat'])): ?>
                                    <div class="current-file">
                                        <i class="fas fa-file-pdf"></i>
                                        <span>File saat ini: <strong><?php echo htmlspecialchars($surat['file_surat']); ?></strong></span>
                                        <a href="../uploads/surat_keluar/<?php echo $surat['file_surat']; ?>"
                                            target="_blank" class="btn-view-file">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="file-upload-wrapper">
                                    <input type="file" id="file_surat" name="file_surat"
                                        accept=".pdf" class="file-input">
                                    <label for="file_surat" class="file-label">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span id="file-name">
                                            <?php echo !empty($surat['file_surat']) ? 'Ganti file PDF (Maksimal 10MB)' : 'Pilih file PDF (Maksimal 10MB)'; ?>
                                        </span>
                                    </label>
                                </div>
                                <small class="form-help">
                                    <i class="fas fa-info-circle"></i> Format: PDF, Maksimal ukuran: 10MB. Kosongkan jika tidak ingin mengubah file.
                                </small>
                            </div>

                            <div class="form-footer">
                                <a href="surat-keluar.php" class="btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-save"></i> Update
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
        // Preview nama file yang dipilih
        document.getElementById('file_surat').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Ganti file PDF (Maksimal 10MB)';
            document.getElementById('file-name').textContent = fileName;

            // Validasi ukuran file
            if (e.target.files[0] && e.target.files[0].size > 10485760) {
                alert('Ukuran file terlalu besar! Maksimal 10MB');
                e.target.value = '';
                document.getElementById('file-name').textContent = 'Ganti file PDF (Maksimal 10MB)';
            }
        });

        // Form validation
        document.getElementById('formSuratKeluar').addEventListener('submit', function(e) {
            const perihal = document.getElementById('perihal').value.trim();
            const tujuanSurat = document.getElementById('tujuan_surat').value.trim();
            const nomorSurat = document.getElementById('nomor_surat').value.trim();

            if (perihal === '' || tujuanSurat === '' || nomorSurat === '') {
                e.preventDefault();
                alert('Semua field yang bertanda * wajib diisi!');
                return false;
            }

            return true;
        });
    </script>
</body>

</html>