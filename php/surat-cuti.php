<?php
// Koneksi database
include 'database.php';
require_once 'auth_check.php';

// Query untuk mengambil data Surat Cuti dengan urutan terbaru
// Query untuk mengambil data Surat Cuti dengan urutan terbaru
// Fix table name to surat_cuti
$query = "SELECT surat_cuti.*, user.nama_bidang, user.username 
          FROM `surat_cuti` 
          LEFT JOIN user ON surat_cuti.id_user = user.no 
          ORDER BY surat_cuti.id DESC";
// Eksekusi query ke database
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Meta tag untuk karakter set -->
    <meta charset="UTF-8">
    <!-- Meta tag untuk responsive design -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title halaman -->
    <title>Surat Cuti - DPPKBPM</title>
    <!-- Link Font Awesome untuk icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/kepala-dinas.css">
    <link rel="stylesheet" href="../css/surat-masuk.css">

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

                <?php if ($role == 'bidang'): ?>
                <a href="disposisi-masuk.php" class="nav-item" title="Disposisi Masuk">
                    <i class="fas fa-inbox"></i>
                    <span class="sidebar-text">Disposisi Masuk</span>
                </a>
                <?php endif; ?>

                <?php if ($role !== 'user' && $role !== 'bidang'): ?>
                <!-- Menu SPJ UMPEG -->
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
                <?php if ($role !== 'user' && $role !== 'bidang'): ?>
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
                <div style="margin-top: 10px; font-size: 0.8rem; color: #a1a1aa;">
                    Data Tahun: <strong><?= htmlspecialchars($tahun_aktif) ?></strong>
                    <br>
                    <a href="pilih_tahun.php" style="color: #60a5fa; text-decoration: none;">(Ganti Tahun)</a>
                </div>
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
                    <!-- Tombol menu toggle untuk desktop -->
                    <button class="header-menu-btn" id="headerMenuBtn">
                        <i class="fas fa-bars"></i>
                    </button>
                    <!-- Judul halaman -->
                    <h1 class="header-title">Surat Cuti</h1>
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
                        <!-- Tombol logout -->
                        <button class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content">
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <!-- Tombol tambah surat cuti -->
                    <a href="tambah-surat-cuti.php" class="btn-primary">
                        <i class="fas fa-plus"></i>
                        Tambah Surat Cuti
                    </a>
                </div>

                <!-- Data Table -->
                <div class="content-box">
                    <div class="box-header">
                        <h2><i class="fas fa-calendar-check"></i> Daftar Surat Cuti</h2>
                        <div class="filter-container" style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-top:10px;">
                            <div class="filter-group">
                                <label for="filterDari">Dari Tanggal:</label>
                                <input type="date" id="filterDari" class="form-control">
                            </div>
                            <div class="filter-group">
                                <label for="filterSampai">Sampai Tanggal:</label>
                                <input type="date" id="filterSampai" class="form-control">
                            </div>
                            <div class="filter-actions" style="display: flex; gap: 10px; align-items: flex-end;">
                                <button type="button" class="btn-primary" id="btnFilterTanggal" style="padding:6px 12px; height: 38px;"><i class="fas fa-filter"></i> Filter</button>
                                <button type="button" class="btn-secondary" id="btnResetTanggal" style="padding:6px 12px; height: 38px;"><i class="fas fa-times"></i> Reset</button>
                            </div>
                        </div>
                    </div>

                    <div class="table-container">
                        <table id="suratCutiTable" class="data-table display nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama/NIP</th>
                                    <th>Pangkat/GOL</th>
                                    <th>Jabatan</th>
                                    <th>Jenis Cuti</th>
                                    <th>Lamanya</th>
                                    <th>Mulai Cuti</th>
                                    <th>Sampai Dengan</th>
                                    <th>Sisa Cuti</th>
                                    <th class="no-export">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $mulai_cuti = $row['Mulai Cuti'] > 0 ? date('d/m/Y', $row['Mulai Cuti']) : '-';
                                        $sampai_dengan = $row['Sampai Dengan'] > 0 ? date('d/m/Y', $row['Sampai Dengan']) : '-';
                                ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($row['Nama/NIP']); ?></td>
                                            <td><?php echo htmlspecialchars($row['Pangkat/GOL RUANG']); ?></td>
                                            <td><?php echo htmlspecialchars($row['Jabatan']); ?></td>
                                            <td><?php echo htmlspecialchars($row['Jenis Cuti']); ?></td>
                                            <td><?php echo htmlspecialchars($row['Lamanya']); ?></td>
                                            <td data-date="<?= $row['Mulai Cuti'] > 0 ? date('Y-m-d', $row['Mulai Cuti']) : '' ?>"><?php echo $mulai_cuti; ?></td>
                                            <td><?php echo $sampai_dengan; ?></td>
                                            <td><?php echo htmlspecialchars($row['Sisa Cuti']); ?></td>
                                            <td class="text-center no-export">
                                                <div class="action-buttons-wrapper">
                                                    <?php if (!empty($row['file_surat'])): ?>
                                                        <a href="../uploads/surat_cuti/<?php echo $row['file_surat']; ?>"
                                                            target="_blank" class="btn-action btn-view" title="Lihat File">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn-action btn-disabled" title="Belum ada file" disabled>
                                                            <i class="fas fa-file-pdf"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <a href="detail-surat-cuti.php?id=<?php echo $row['id']; ?>"
                                                        class="btn-action btn-view" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    <a href="edit-surat-cuti.php?id=<?php echo $row['id']; ?>"
                                                        class="btn-action btn-edit" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <button class="btn-action btn-delete"
                                                        onclick="confirmDelete(<?php echo $row['id']; ?>)" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
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
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <!-- JSZip untuk export Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <!-- PDFMake untuk export PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <!-- PDFMake fonts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <!-- DataTables HTML5 buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <!-- DataTables Print button -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- JavaScript dashboard -->
    <script src="../js/dashboard.js"></script>
    <script src="../js/surat-cuti.js"></script>
</body>

</html>