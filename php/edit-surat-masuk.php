<?php
// Koneksi database
include 'database.php';
require_once 'auth_check.php';

// Cek apakah ada ID
if (!isset($_GET['id'])) {
    header("Location: surat-masuk.php");
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
    <title>Edit Surat Masuk - DPPKBPM</title>
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
                <a href="surat-masuk.php" class="nav-item active" title="Surat Masuk">
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
                    <h1 class="header-title">Edit Surat Masuk</h1>
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
                    <a href="dashboard.php">Dashboard</a>
                    <span class="separator">/</span>
                    <a href="surat-masuk.php">Surat Masuk</a>
                    <span class="separator">/</span>
                    <span class="current">Edit Surat Masuk</span>
                </div>

                <!-- Form Box -->
                <div class="content-box">
                    <div class="box-header">
                        <h2><i class="fas fa-edit"></i> Form Edit Surat Masuk</h2>
                    </div>

                    <div class="form-container">
                        <form id="formSuratMasuk" method="POST" action="proses-surat-masuk.php" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?php echo $surat['id']; ?>">

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nomor_agenda">
                                        <i class="fas fa-hashtag"></i> Nomor Agenda
                                    </label>
                                    <input type="text" id="nomor_agenda" name="nomor_agenda"
                                        value="<?php echo htmlspecialchars($surat['nomor_agenda']); ?>"
                                        readonly
                                        class="readonly-input">
                                    <small class="form-help">
                                        <i class="fas fa-info-circle"></i> Nomor agenda tidak dapat diubah
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_terima">
                                        <i class="fas fa-calendar-check"></i> Tanggal Terima Surat <span class="required">*</span>
                                    </label>
                                    <input type="date" id="tanggal_terima" name="tanggal_terima"
                                        value="<?php echo $surat['tanggal_terima']; ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="alamat_pengirim">
                                    <i class="fas fa-building"></i> Alamat Pengirim Surat <span class="required">*</span>
                                </label>
                                <input type="text" id="alamat_pengirim" name="alamat_pengirim"
                                    value="<?php echo htmlspecialchars($surat['alamat_pengirim']); ?>"
                                    placeholder="Masukkan alamat/instansi pengirim surat" required>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="tanggal_surat">
                                        <i class="fas fa-calendar-alt"></i> Tanggal Surat <span class="required">*</span>
                                    </label>
                                    <input type="date" id="tanggal_surat" name="tanggal_surat"
                                        value="<?php echo $surat['tanggal_surat']; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="nomor_surat">
                                        <i class="fas fa-file-signature"></i> Nomor Surat <span class="required">*</span>
                                    </label>
                                    <input type="text" id="nomor_surat" name="nomor_surat"
                                        value="<?php echo htmlspecialchars($surat['nomor_surat']); ?>"
                                        placeholder="Contoh: 700/3768-SET/ITKO" required>
                                </div>
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
                                    <div class="file-info-display">
                                        <div class="file-info-row">
                                            <div class="file-info-details">
                                                <i class="fas fa-file-pdf"></i>
                                                <div class="file-info-text">
                                                    <span class="file-info-label">File saat ini:</span>
                                                    <span class="file-info-name"><?php echo htmlspecialchars($surat['file_surat']); ?></span>
                                                </div>
                                            </div>
                                            <div class="file-info-actions">
                                                <a href="../uploads/surat_masuk/<?php echo $surat['file_surat']; ?>" target="_blank" class="btn-view-small">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </a>
                                            </div>
                                        </div>
                                        <div class="file-delete-option">
                                            <label class="file-delete-label">
                                                <input type="checkbox" name="delete_file_surat" value="1"> Hapus file ini (Centang untuk menghapus/mengganti tanpa upload baru)
                                            </label>
                                        </div>
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

                            <div class="form-group">
                                <label for="dilihat_oleh">
                                    <i class="fas fa-users"></i> Dapat Dilihat Oleh
                                </label>
                                <select id="dilihat_oleh" name="dilihat_oleh[]" multiple class="select-multiple">
                                    <?php
                                    mysqli_data_seek($result_users, 0);
                                    while ($user = mysqli_fetch_assoc($result_users)) {
                                        $selected = in_array($user['nama'], $dilihat_array) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($user['nama']) . '" ' . $selected . '>'
                                            . htmlspecialchars($user['nama']) . '</option>';
                                    }
                                    ?>
                                </select>
                                <small class="form-help">
                                    <i class="fas fa-info-circle"></i> Pilih pengguna yang dapat melihat surat ini (tekan Ctrl untuk pilih multiple)
                                </small>
                            </div>

                            <div class="form-footer">
                                <a href="surat-masuk.php" class="btn-secondary">
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
        document.getElementById('formSuratMasuk').addEventListener('submit', function(e) {
            const perihal = document.getElementById('perihal').value.trim();
            const alamatPengirim = document.getElementById('alamat_pengirim').value.trim();
            const nomorSurat = document.getElementById('nomor_surat').value.trim();

            if (perihal === '' || alamatPengirim === '' || nomorSurat === '') {
                e.preventDefault();
                alert('Semua field yang bertanda * wajib diisi!');
                return false;
            }

            return true;
        });
    </script>
</body>

</html>