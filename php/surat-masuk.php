<?php
// Koneksi database
include 'database.php';
require_once 'auth_check.php';

// Query untuk mengambil data surat masuk
$query = "SELECT * FROM surat_masuk ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Masuk - DPPKBPM</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/kepala-dinas.css">
    <link rel="stylesheet" href="../css/surat-masuk.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
</head>

<body>
    <div class="container">
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
                    <h1 class="header-title">Surat Masuk</h1>
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
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="tambah-surat-masuk.php" class="btn-primary">
                        <i class="fas fa-plus"></i>
                        Tambah Surat Masuk
                    </a>
                </div>

                <!-- Data Table -->
                <div class="content-box">
                    <div class="box-header">
                        <h2><i class="fas fa-inbox"></i> Daftar Surat Masuk</h2>
                    </div>

                    <div class="table-container">
                        <table id="suratMasukTable" class="data-table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="3%">No</th>
                                    <th width="7%">Nomor Agenda</th>
                                    <th width="10%">Tanggal Terima</th>
                                    <th width="15%">Alamat Pengirim</th>
                                    <th width="10%">Tanggal Surat</th>
                                    <th width="12%">Nomor Surat</th>
                                    <th width="18%">Perihal</th>
                                    <th width="10%" class="no-export">Status Disposisi</th>
                                    <th width="10%" class="no-export">Dapat Dilihat</th>
                                    <th width="15%" class="no-export">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        // Status badge
                                        $statusClass = '';
                                        $statusText = '';
                                        if ($row['status_disposisi'] == 'Belum diproses') {
                                            $statusClass = 'badge-warning';
                                            $statusText = 'Belum diproses';
                                        } elseif ($row['status_disposisi'] == 'Sudah didisposisi') {
                                            $statusClass = 'badge-success';
                                            $statusText = 'Sudah didisposisi';
                                        } else {
                                            $statusClass = 'badge-info';
                                            $statusText = $row['status_disposisi'];
                                        }

                                        // Format tanggal
                                        $tgl_terima = date('d/m/Y', strtotime($row['tanggal_terima']));
                                        $tgl_surat = date('d/m/Y', strtotime($row['tanggal_surat']));

                                        // Dilihat oleh
                                        $dilihat = !empty($row['dilihat_oleh']) ? $row['dilihat_oleh'] : '-';
                                ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no++; ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($row['nomor_agenda']); ?></td>
                                            <td data-date="<?= date('Y-m-d', strtotime($row['tanggal_terima'])) ?>"><?php echo $tgl_terima; ?></td>
                                            <td><?php echo htmlspecialchars($row['alamat_pengirim']); ?></td>
                                            <td><?php echo $tgl_surat; ?></td>
                                            <td><?php echo htmlspecialchars($row['nomor_surat']); ?></td>
                                            <td><?php echo htmlspecialchars($row['perihal']); ?></td>
                                            <td class="text-center no-export">
                                                <span class="badge <?php echo $statusClass; ?>">
                                                    <?php echo $statusText; ?>
                                                </span>
                                            </td>
                                            <td class="no-export"><?php echo htmlspecialchars($dilihat); ?></td>
                                            <td class="text-center action-buttons-cell no-export">
                                                <?php if (!empty($row['file_surat'])): ?>
                                                    <a href="view-surat.php?id=<?php echo $row['id']; ?>" class="btn-action btn-view" title="Lihat Surat">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn-action btn-disabled" title="Belum ada file" disabled>
                                                        <i class="fas fa-file-pdf"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if (!empty($row['file_disposisi'])): ?>
                                                    <a href="../uploads/disposisi/<?php echo $row['file_disposisi']; ?>" target="_blank" class="btn-action btn-view-disposisi" title="Lihat File Disposisi" style="background-color: #17a2b8; color: white;">
                                                        <i class="fas fa-file-invoice"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <a href="disposisi-surat.php?id=<?php echo $row['id']; ?>" class="btn-action btn-disposisi" title="Disposisi">
                                                    <i class="fas fa-share-square"></i>
                                                </a>

                                                <a href="edit-surat-masuk.php?id=<?php echo $row['id']; ?>" class="btn-action btn-edit" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <button class="btn-action btn-delete" onclick="confirmDelete(<?php echo $row['id']; ?>)" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script src="../js/dashboard.js"></script>
    <script src="../js/surat-masuk.js"></script>
</body>

</html>