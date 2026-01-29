<?php
// Koneksi database
include 'database.php';
require_once 'auth_check.php';

// Generate nomor urut otomatis
$query_last = "SELECT nomor_urut FROM spj_umpeg ORDER BY id DESC LIMIT 1";
$result_last = mysqli_query($conn, $query_last);
if (mysqli_num_rows($result_last) > 0) {
    $last = mysqli_fetch_assoc($result_last);
    $last_number = intval($last['nomor_urut']);
    $new_number = $last_number + 1;
} else {
    $new_number = 1;
}

// Generate Nomor SPJ otomatis
$tahun_sekarang = date('Y');
$query_last_spj = "SELECT nomor_spj FROM spj_umpeg WHERE nomor_spj LIKE '%/$tahun_sekarang' ORDER BY id DESC LIMIT 1";
$result_last_spj = mysqli_query($conn, $query_last_spj);

if (mysqli_num_rows($result_last_spj) > 0) {
    $last_spj = mysqli_fetch_assoc($result_last_spj);
    $last_nomor_spj = $last_spj['nomor_spj'];

    // Parse nomor urut dari format SPJ/001/UMPEG/2025
    preg_match('/SPJ\/(\d+)\/UMPEG\/(\d{4})/', $last_nomor_spj, $matches);

    if (isset($matches[1])) {
        $last_spj_number = intval($matches[1]);
        $new_spj_number = $last_spj_number + 1;
    } else {
        $new_spj_number = 1;
    }
} else {
    $new_spj_number = 1;
}

// Format nomor SPJ dengan 3 digit
$nomor_spj_formatted = sprintf("SPJ/%03d/UMPEG/%s", $new_spj_number, $tahun_sekarang);

// Ambil data user untuk dropdown
$query_users = "SELECT no, nama FROM user ORDER BY nama ASC";
$result_users = mysqli_query($conn, $query_users);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah SPJ UMPEG - DPPKBPM</title>
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
                <a href="spj-umpeg.php" class="nav-item" title="SPJ UMPEG">
                    <i class="fas fa-file-invoice"></i>
                    <span class="sidebar-text">SPJ UMPEG</span>
                </a>
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
                    <h1 class="header-title">Tambah SPJ UMPEG</h1>
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
                    <span class="current">Tambah SPJ UMPEG</span>
                </div>

                <!-- Form Container -->
                <div class="content-box">
                    <div class="box-header">
                        <h2><i class="fas fa-plus-circle"></i> Form Tambah SPJ UMPEG</h2>
                    </div>

                    <form method="POST" action="proses-spj-umpeg.php" enctype="multipart/form-data" class="form-container" id="formSPJ">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="nomor_urut" value="<?php echo $new_number; ?>">

                        <div class="form-row">
                            <div class="form-group">
                                <label for="nomor_spj">
                                    <i class="fas fa-file-alt"></i>
                                    Nomor SPJ
                                </label>
                                <input type="text" id="nomor_spj" name="nomor_spj"
                                    value="<?php echo $nomor_spj_formatted; ?>"
                                    readonly class="readonly-input">
                                <small class="form-help">
                                    <i class="fas fa-info-circle"></i>
                                    Nomor SPJ dibuat otomatis
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="tanggal">
                                    <i class="fas fa-calendar-alt"></i>
                                    Tanggal
                                    <span class="required">*</span>
                                </label>
                                <input type="date" id="tanggal" name="tanggal" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nama_kegiatan">
                                <i class="fas fa-tasks"></i>
                                Nama Kegiatan
                                <span class="required">*</span>
                            </label>
                            <textarea id="nama_kegiatan" name="nama_kegiatan" required
                                placeholder="Masukkan nama kegiatan" rows="4"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="dibuat_oleh">
                                <i class="fas fa-user-edit"></i>
                                Dibuat Oleh
                                <span class="required">*</span>
                            </label>
                            <select id="dibuat_oleh" name="dibuat_oleh" required>
                                <option value="">-- Pilih User --</option>
                                <?php
                                if (mysqli_num_rows($result_users) > 0) {
                                    while ($user = mysqli_fetch_assoc($result_users)) {
                                        echo '<option value="' . htmlspecialchars($user['nama']) . '">' . htmlspecialchars($user['nama']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            <small class="form-help">
                                <i class="fas fa-info-circle"></i> Pilih akun yang membuat SPJ ini
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="file_spj">
                                <i class="fas fa-file-pdf"></i> File SPJ (PDF)
                            </label>
                            <div class="file-upload-wrapper">
                                <input type="file" id="file_spj" name="file_spj"
                                    accept=".pdf" class="file-input">
                                <label for="file_spj" class="file-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span id="file-name">Pilih file PDF (Maksimal 10MB)</span>
                                </label>
                            </div>
                            <small class="form-help">
                                <i class="fas fa-info-circle"></i> Format: PDF, Maksimal ukuran: 10MB (Opsional)
                            </small>
                        </div>

                        <div class="form-footer">
                            <a href="spj-umpeg.php" class="btn-secondary">
                                <i class="fas fa-times"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save"></i>
                                Simpan Data
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
        // Set tanggal hari ini sebagai default
        document.getElementById('tanggal').valueAsDate = new Date();

        // Preview nama file yang dipilih
        document.getElementById('file_spj').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file PDF (Maksimal 10MB)';
            document.getElementById('file-name').textContent = fileName;

            // Validasi ukuran file
            if (e.target.files[0] && e.target.files[0].size > 10485760) {
                alert('Ukuran file terlalu besar! Maksimal 10MB');
                e.target.value = '';
                document.getElementById('file-name').textContent = 'Pilih file PDF (Maksimal 10MB)';
            }
        });

        // Form validation
        document.getElementById('formSPJ').addEventListener('submit', function(e) {
            const namaKegiatan = document.getElementById('nama_kegiatan').value.trim();
            const dibuatOleh = document.getElementById('dibuat_oleh').value;

            if (namaKegiatan === '' || dibuatOleh === '') {
                e.preventDefault();
                alert('Semua field yang bertanda * wajib diisi!');
                return false;
            }

            return true;
        });
    </script>
</body>

</html>