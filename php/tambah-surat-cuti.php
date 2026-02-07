<?php
// Koneksi database
include 'database.php';
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Meta tag untuk karakter set -->
    <meta charset="UTF-8">
    <!-- Meta tag untuk responsive design -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title halaman -->
    <title>Tambah Surat Cuti - DPPKBPM</title>
    <!-- Link CSS untuk dashboard -->
    <link rel="stylesheet" href="../css/dashboard.css">
    <!-- Link CSS untuk kepala dinas -->
    <link rel="stylesheet" href="../css/kepala-dinas.css">
    <!-- Link CSS untuk surat masuk -->
    <link rel="stylesheet" href="../css/surat-masuk.css">
    <!-- Link Font Awesome untuk icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Container utama -->
    <div class="container">
        <div class="hover-trigger"></div>
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <!-- Header sidebar -->
            <div class="sidebar-header">
                <!-- Logo -->
                <div class="logo">
                    <img src="../assets/img/LOGO.png" alt="Logo DPPKBPM" class="logo-img">
                </div>
                <!-- Nama instansi -->
                <h2 class="sidebar-text">DIAPRA DPPKBPM</h2>
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
                <?php if ($role !== 'user'): ?>
                <a href="spj-umpeg.php" class="nav-item" title="SPJ UMPEG">
                    <i class="fas fa-file-invoice"></i>
                    <span class="sidebar-text">SPJ UMPEG</span>
                </a>
                <?php endif; ?>
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
                    <button class="header-menu-btn" id="headerMenuBtn">
                        <i class="fas fa-bars"></i>
                    </button>
                    <!-- Judul halaman -->
                    <h1 class="header-title">Tambah Surat Cuti</h1>
                </div>
                <!-- Bagian kanan header -->
                <div class="header-right">
                    <!-- Info pengguna -->
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
                    <span class="current">Tambah Surat Cuti</span>
                </div>

                <!-- Form Container -->
                <div class="content-box">
                    <!-- Header box -->
                    <div class="box-header">
                        <h2><i class="fas fa-plus-circle"></i> Form Tambah Surat Cuti</h2>
                    </div>

                    <!-- Form tambah surat cuti -->
                    <form method="POST" action="proses-surat-cuti.php" class="form-container" id="formSuratCuti" enctype="multipart/form-data">
                        <!-- Hidden input untuk action -->
                        <input type="hidden" name="action" value="add">

                        <!-- Baris form pertama -->
                        <div class="form-row">
                            <!-- Form group Nama/NIP -->
                            <div class="form-group">
                                <label for="nama_nip">
                                    <i class="fas fa-user"></i>
                                    Nama/NIP
                                    <span class="required">*</span>
                                </label>
                                <!-- Input Nama/NIP -->
                                <input type="text" id="nama_nip" name="nama_nip" required
                                    placeholder="Masukkan Nama/NIP">
                            </div>

                            <!-- Form group Pangkat/GOL RUANG -->
                            <div class="form-group">
                                <label for="pangkat_gol">
                                    <i class="fas fa-id-badge"></i>
                                    Pangkat/GOL RUANG
                                    <span class="required">*</span>
                                </label>
                                <!-- Input Pangkat/GOL RUANG -->
                                <input type="text" id="pangkat_gol" name="pangkat_gol" required
                                    placeholder="Masukkan Pangkat/GOL RUANG">
                            </div>
                        </div>

                        <!-- Baris form kedua -->
                        <div class="form-row">
                            <!-- Form group Jabatan -->
                            <div class="form-group">
                                <label for="jabatan">
                                    <i class="fas fa-briefcase"></i>
                                    Jabatan
                                    <span class="required">*</span>
                                </label>
                                <!-- Input Jabatan -->
                                <input type="text" id="jabatan" name="jabatan" required
                                    placeholder="Masukkan Jabatan">
                            </div>

                            <!-- Form group Jenis Cuti -->
                            <div class="form-group">
                                <label for="jenis_cuti">
                                    <i class="fas fa-calendar-alt"></i>
                                    Jenis Cuti
                                    <span class="required">*</span>
                                </label>
                                <!-- Select Jenis Cuti -->
                                <select id="jenis_cuti" name="jenis_cuti" required>
                                    <option value="">-- Pilih Jenis Cuti --</option>
                                    <option value="Cuti Tahunan">Cuti Tahunan</option>
                                    <option value="Cuti Sakit">Cuti Sakit</option>
                                    <option value="Cuti Melahirkan">Cuti Melahirkan</option>
                                    <option value="Cuti Alasan Penting">Cuti Alasan Penting</option>
                                    <option value="Cuti Besar">Cuti Besar</option>
                                    <option value="Cuti Diluar Tanggungan Negara">Cuti Diluar Tanggungan Negara</option>
                                </select>
                            </div>
                        </div>

                        <!-- Baris form ketiga -->
                        <div class="form-row">
                            <!-- Form group Lamanya -->
                            <div class="form-group">
                                <label for="lamanya">
                                    <i class="fas fa-clock"></i>
                                    Lamanya
                                    <span class="required">*</span>
                                </label>
                                <!-- Input Lamanya -->
                                <input type="text" id="lamanya" name="lamanya" required
                                    placeholder="Contoh: 5 hari">
                                <small class="form-help">
                                    <i class="fas fa-info-circle"></i> Masukkan lama cuti (contoh: 5 hari)
                                </small>
                            </div>

                            <!-- Form group Dilaksanakan DI -->
                            <div class="form-group">
                                <label for="dilaksanakan_di">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Dilaksanakan DI
                                    <span class="required">*</span>
                                </label>
                                <!-- Input Dilaksanakan DI -->
                                <input type="text" id="dilaksanakan_di" name="dilaksanakan_di" required
                                    placeholder="Masukkan tempat dilaksanakannya cuti">
                            </div>
                        </div>

                        <!-- Baris form keempat -->
                        <div class="form-row">
                            <!-- Form group Mulai Cuti -->
                            <div class="form-group">
                                <label for="mulai_cuti">
                                    <i class="fas fa-calendar-plus"></i>
                                    Mulai Cuti
                                    <span class="required">*</span>
                                </label>
                                <!-- Input Mulai Cuti -->
                                <input type="date" id="mulai_cuti" name="mulai_cuti" required>
                            </div>

                            <!-- Form group Sampai Dengan -->
                            <div class="form-group">
                                <label for="sampai_dengan">
                                    <i class="fas fa-calendar-minus"></i>
                                    Sampai Dengan
                                    <span class="required">*</span>
                                </label>
                                <!-- Input Sampai Dengan -->
                                <input type="date" id="sampai_dengan" name="sampai_dengan" required>
                            </div>
                        </div>

                        <!-- Form group Sisa Cuti -->
                        <div class="form-group">
                            <label for="sisa_cuti">
                                <i class="fas fa-hourglass-half"></i>
                                Sisa Cuti
                                <span class="required">*</span>
                            </label>
                            <!-- Input Sisa Cuti -->
                            <input type="text" id="sisa_cuti" name="sisa_cuti" required
                                placeholder="Masukkan sisa cuti (contoh: 10 hari)">
                            <small class="form-help">
                                <i class="fas fa-info-circle"></i> Masukkan sisa cuti yang dimiliki
                            </small>
                        </div>

                        <!-- Form group File Surat -->
                        <div class="form-group">
                            <label for="file_surat">
                                <i class="fas fa-file-pdf"></i>
                                File Surat Cuti (PDF)
                            </label>
                            <input type="file" id="file_surat" name="file_surat" accept=".pdf" class="file-input">
                            <small class="form-help">
                                <i class="fas fa-info-circle"></i> Format: PDF, Maksimal ukuran: 10MB
                            </small>
                        </div>

                        <!-- Form footer -->
                        <div class="form-footer">
                            <!-- Tombol batal -->
                            <a href="surat-cuti.php" class="btn-secondary">
                                <i class="fas fa-times"></i>
                                Batal
                            </a>
                            <!-- Tombol simpan -->
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- JavaScript dashboard -->
    <script src="../js/dashboard.js"></script>
    <script>
        // Validasi form sebelum submit
        document.getElementById('formSuratCuti').addEventListener('submit', function(e) {
            // Ambil nilai form
            const namaNip = document.getElementById('nama_nip').value.trim();
            // Ambil nilai pangkat/gol
            const pangkatGol = document.getElementById('pangkat_gol').value.trim();
            // Ambil nilai jabatan
            const jabatan = document.getElementById('jabatan').value.trim();
            // Ambil nilai jenis cuti
            const jenisCuti = document.getElementById('jenis_cuti').value;
            // Ambil nilai lamanya
            const lamanya = document.getElementById('lamanya').value.trim();
            // Ambil nilai dilaksanakan di
            const dilaksanakanDi = document.getElementById('dilaksanakan_di').value.trim();
            // Ambil nilai mulai cuti
            const mulaiCuti = document.getElementById('mulai_cuti').value;
            // Ambil nilai sampai dengan
            const sampaiDengan = document.getElementById('sampai_dengan').value;
            // Ambil nilai sisa cuti
            const sisaCuti = document.getElementById('sisa_cuti').value.trim();

            // Validasi semua field wajib
            if (namaNip === '' || pangkatGol === '' || jabatan === '' || jenisCuti === '' || 
                lamanya === '' || dilaksanakanDi === '' || mulaiCuti === '' || 
                sampaiDengan === '' || sisaCuti === '') {
                // Cegah submit jika ada field kosong
                e.preventDefault();
                // Tampilkan alert
                alert('Semua field yang bertanda * wajib diisi!');
                return false;
            }

            // Validasi tanggal: sampai dengan harus lebih besar dari mulai cuti
            if (new Date(sampaiDengan) < new Date(mulaiCuti)) {
                // Cegah submit jika tanggal tidak valid
                e.preventDefault();
                // Tampilkan alert
                alert('Tanggal Sampai Dengan harus lebih besar atau sama dengan Tanggal Mulai Cuti!');
                return false;
            }

            return true;
        });
    </script>
</body>

</html>