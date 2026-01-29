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
    <title>Edit SPJ UMPEG - DPPKBPM</title>
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
                <p class="subtitle sidebar-text">DIAPRA</p>
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
                <a href="surat-keluar.php" class="nav-item" title="Surat Keluar">
                    <i class="fas fa-paper-plane"></i>
                    <span class="sidebar-text">Surat Keluar</span>
                </a>
                <a href="spj-umpeg.php" class="nav-item active" title="SPJ UMPEG">
                    <i class="fas fa-file-invoice"></i>
                    <span class="sidebar-text">SPJ UMPEG</span>
                </a>
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
                    <h1 class="header-title">Edit SPJ UMPEG</h1>
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
                    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                    <span class="separator">/</span>
                    <a href="spj-umpeg.php">SPJ UMPEG</a>
                    <span class="separator">/</span>
                    <span class="current">Edit SPJ UMPEG</span>
                </div>

                <!-- Form Container -->
                <div class="content-box">
                    <div class="box-header">
                        <h2><i class="fas fa-edit"></i> Form Edit SPJ UMPEG</h2>
                    </div>

                    <form method="POST" action="proses-spj-umpeg.php" enctype="multipart/form-data" class="form-container" id="formSPJ">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

                        <div class="form-row">
                            <div class="form-group">
                                <label for="nomor_urut">
                                    <i class="fas fa-sort-numeric-up"></i>
                                    Nomor Urut
                                </label>
                                <input type="text" id="nomor_urut" name="nomor_urut"
                                    value="<?php echo htmlspecialchars($data['nomor_urut']); ?>"
                                    readonly class="readonly-input">
                                <small class="form-help">
                                    <i class="fas fa-info-circle"></i> Nomor urut tidak dapat diubah
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="tanggal">
                                    <i class="fas fa-calendar-alt"></i>
                                    Tanggal
                                    <span class="required">*</span>
                                </label>
                                <input type="date" id="tanggal" name="tanggal" required
                                    value="<?php echo $data['tanggal']; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nomor_spj">
                                <i class="fas fa-file-alt"></i>
                                Nomor SPJ
                                <span class="required">*</span>
                            </label>
                            <input type="text" id="nomor_spj" name="nomor_spj" required
                                value="<?php echo htmlspecialchars($data['nomor_spj']); ?>">
                            <small class="form-help">
                                <i class="fas fa-info-circle"></i>
                                Format: SPJ/[Nomor]/UMPEG/[Tahun]
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="nama_kegiatan">
                                <i class="fas fa-tasks"></i>
                                Nama Kegiatan
                                <span class="required">*</span>
                            </label>
                            <textarea id="nama_kegiatan" name="nama_kegiatan" required
                                rows="4"><?php echo htmlspecialchars($data['nama_kegiatan']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="dibuat_oleh">
                                <i class="fas fa-user-edit"></i>
                                Dibuat Oleh
                                <span class="required">*</span>
                            </label>
                            <input type="text" id="dibuat_oleh" name="dibuat_oleh" required
                                value="<?php echo htmlspecialchars($data['dibuat_oleh']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="file_spj">
                                <i class="fas fa-file-pdf"></i> File SPJ (PDF)
                            </label>

                            <?php if (!empty($data['file_spj'])): ?>
                                <div class="current-file">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>File saat ini: <strong><?php echo htmlspecialchars($data['file_spj']); ?></strong></span>
                                    <a href="../uploads/spj_umpeg/<?php echo $data['file_spj']; ?>"
                                        target="_blank" class="btn-view-file">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="file-upload-wrapper">
                                <input type="file" id="file_spj" name="file_spj"
                                    accept=".pdf" class="file-input">
                                <label for="file_spj" class="file-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span id="file-name">
                                        <?php echo !empty($data['file_spj']) ? 'Ganti file PDF (Maksimal 10MB)' : 'Pilih file PDF (Maksimal 10MB)'; ?>
                                    </span>
                                </label>
                            </div>
                            <small class="form-help">
                                <i class="fas fa-info-circle"></i> Format: PDF, Maksimal ukuran: 10MB. Kosongkan jika tidak ingin mengubah file.
                            </small>
                        </div>

                        <div class="form-footer">
                            <a href="spj-umpeg.php" class="btn-secondary">
                                <i class="fas fa-times"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save"></i>
                                Update Data
                            </button>
                        </div>
                    </form>
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
    <script>
        // Preview nama file yang dipilih
        document.getElementById('file_spj').addEventListener('change', function(e) {
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
        document.getElementById('formSPJ').addEventListener('submit', function(e) {
            const nomorSpj = document.getElementById('nomor_spj').value.trim();
            const namaKegiatan = document.getElementById('nama_kegiatan').value.trim();
            const dibuatOleh = document.getElementById('dibuat_oleh').value.trim();

            if (nomorSpj === '' || namaKegiatan === '' || dibuatOleh === '') {
                e.preventDefault();
                alert('Semua field yang bertanda * wajib diisi!');
                return false;
            }

            return true;
        });
    </script>
</body>

</html>