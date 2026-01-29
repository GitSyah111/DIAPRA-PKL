<?php
// Koneksi database
include 'database.php';
require_once 'auth_check.php';

// Query untuk mengambil data SPJ UMPEG
$query = "SELECT * FROM spj_umpeg ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPJ UMPEG - DPPKBPM</title>
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
                    <h1 class="header-title">SPJ UMPEG</h1>
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
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="tambah-spj-umpeg.php" class="btn-primary">
                        <i class="fas fa-plus"></i>
                        Tambah SPJ UMPEG
                    </a>
                </div>

                <!-- Data Table -->
                <div class="content-box">
                    <div class="box-header">
                        <h2><i class="fas fa-file-invoice"></i> Daftar SPJ UMPEG</h2>
                        <div class="date-filter" style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-top:10px;">
                            <label>Filter Tanggal:</label>
                            <input type="date" id="filterDari" placeholder="Dari">
                            <input type="date" id="filterSampai" placeholder="Sampai">
                            <button type="button" class="btn-primary" id="btnFilterTanggal" style="padding:6px 12px;"><i class="fas fa-filter"></i> Filter</button>
                            <button type="button" class="btn-secondary" id="btnResetTanggal" style="padding:6px 12px;"><i class="fas fa-times"></i> Reset</button>
                        </div>
                    </div>

                    <div class="table-container">
                        <table id="spjUmpegTable" class="data-table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="8%">Nomor Urut</th>
                                    <th width="18%">Nomor SPJ</th>
                                    <th width="12%">Tanggal</th>
                                    <th width="32%">Nama Kegiatan</th>
                                    <th width="12%">Dibuat Oleh</th>
                                    <th width="13%" class="no-export">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        // Format tanggal
                                        $tanggal = date('d/m/Y', strtotime($row['tanggal']));
                                ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no++; ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($row['nomor_urut']); ?></td>
                                            <td><?php echo htmlspecialchars($row['nomor_spj']); ?></td>
                                            <td data-date="<?= date('Y-m-d', strtotime($row['tanggal'])) ?>"><?php echo $tanggal; ?></td>
                                            <td><?php echo htmlspecialchars($row['nama_kegiatan']); ?></td>
                                            <td><?php echo htmlspecialchars($row['dibuat_oleh']); ?></td>
                                            <td class="text-center action-buttons-cell no-export">
                                                <?php if (!empty($row['file_spj'])): ?>
                                                    <a href="../uploads/spj_umpeg/<?php echo $row['file_spj']; ?>"
                                                        class="btn-action btn-view" title="Lihat PDF" target="_blank">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn-action btn-disabled" title="Belum ada file" disabled>
                                                        <i class="fas fa-file-pdf"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <a href="detail-spj-umpeg.php?id=<?php echo $row['id']; ?>"
                                                    class="btn-action btn-view" title="Lihat Detail"
                                                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="edit-spj-umpeg.php?id=<?php echo $row['id']; ?>"
                                                    class="btn-action btn-edit" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <button class="btn-action btn-delete"
                                                    onclick="confirmDelete(<?php echo $row['id']; ?>)" title="Hapus">
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

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var tableSpj = $('#spjUmpegTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'dt-button',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'dt-button',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    }
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data yang tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                pageLength: 10,
                order: [
                    [0, 'asc']
                ]
            });
            // Filter tanggal (Tanggal = kolom index 3)
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'spjUmpegTable') return true;
                var dari = $('#filterDari').val();
                var sampai = $('#filterSampai').val();
                if (!dari && !sampai) return true;
                var row = $(tableSpj.row(dataIndex).node());
                var dateVal = row.find('td:eq(3)').attr('data-date');
                if (!dateVal) return false;
                if (dari && dateVal < dari) return false;
                if (sampai && dateVal > sampai) return false;
                return true;
            });
            $('#btnFilterTanggal').on('click', function() { tableSpj.draw(); });
            $('#btnResetTanggal').on('click', function() {
                $('#filterDari').val('');
                $('#filterSampai').val('');
                tableSpj.draw();
            });
        });

        // Delete confirmation
        function confirmDelete(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data SPJ UMPEG ini? File yang terlampir juga akan dihapus.')) {
                window.location.href = 'proses-spj-umpeg.php?action=delete&id=' + id;
            }
        }
    </script>
</body>

</html>