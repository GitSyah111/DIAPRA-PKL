<?php
// Koneksi database
include 'database.php';

// Query untuk mengambil data kepala dinas
$query = "SELECT * FROM kadis ORDER BY no ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kepala Dinas - DPPKBPM</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/kepala-dinas.css">
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
                <p class="username sidebar-text"><i class="fas fa-user-circle"></i> @Muhammad ibnu Riayath Syah</p>
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
                <a href="data-pengguna.php" class="nav-item" title="Data Pengguna">
                    <i class="fas fa-users"></i>
                    <span class="sidebar-text">Data Pengguna</span>
                </a>
                <a href="data-kepala-dinas.php" class="nav-item active" title="Data Kepala Dinas">
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
                    <h1 class="header-title">Data Kepala Dinas</h1>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span class="user-name">Admin</span>
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
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="btn-primary" onclick="openAddModal()">
                        <i class="fas fa-plus"></i>
                        Tambah Data
                    </button>
                </div>

                <!-- Data Table -->
                <div class="content-box">
                    <div class="box-header">
                        <h2><i class="fas fa-table"></i> Daftar Kepala Dinas</h2>
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Cari nama, pangkat, atau NIP...">
                        </div>
                    </div>

                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="35%">Nama</th>
                                    <th width="30%">Pangkat</th>
                                    <th width="20%">NIP</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                            <td><?php echo htmlspecialchars($row['pangkat']); ?></td>
                                            <td><?php echo htmlspecialchars($row['NIP']); ?></td>
                                            <td class="text-center">
                                                <button class="btn-action btn-edit" onclick="openEditModal(<?php echo $row['no']; ?>, '<?php echo addslashes($row['nama']); ?>', '<?php echo addslashes($row['pangkat']); ?>', '<?php echo $row['NIP']; ?>')" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-action btn-delete" onclick="confirmDelete(<?php echo $row['no']; ?>)" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="5" class="text-center empty-data">
                                            <i class="fas fa-inbox"></i>
                                            <p>Belum ada data kepala dinas</p>
                                        </td>
                                    </tr>
                                <?php } ?>
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

    <!-- Modal Add/Edit -->
    <div id="dataModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle"><i class="fas fa-user-tie"></i> Tambah Data Kepala Dinas</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="dataForm" method="POST" action="proses-kepala-dinas.php">
                <input type="hidden" id="actionType" name="action" value="add">
                <input type="hidden" id="dataId" name="no">

                <div class="form-group">
                    <label for="nama"><i class="fas fa-user"></i> Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap">
                </div>

                <div class="form-group">
                    <label for="pangkat"><i class="fas fa-award"></i> Pangkat</label>
                    <input type="text" id="pangkat" name="pangkat" required placeholder="Masukkan pangkat">
                </div>

                <div class="form-group">
                    <label for="NIP"><i class="fas fa-id-card"></i> NIP</label>
                    <input type="text" id="NIP" name="NIP" required placeholder="Masukkan NIP" maxlength="38">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/dashboard.js"></script>
    <script src="../js/kepala-dinas.js"></script>
</body>

</html>