<?php
// Koneksi database
include 'database.php';

// Query untuk mengambil data surat masuk yang belum disposisi
$query = "SELECT * FROM surat_masuk WHERE status_disposisi = 'Belum diproses' ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Belum Disposisi - DPPKBPM</title>
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
                    <h1 class="header-title">Surat Belum Disposisi</h1>
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
                <!-- Info Box -->
                <div style="background: linear-gradient(135deg, #fff3cd 0%, #fff8e1 100%); padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #f59e0b; display: flex; align-items: center; gap: 15px;">
                    <i class="fas fa-info-circle" style="font-size: 24px; color: #f59e0b;"></i>
                    <div>
                        <strong style="color: #1e3a5f; font-size: 16px;">Surat Menunggu Disposisi</strong>
                        <p style="color: #6b7280; margin-top: 5px; font-size: 14px;">
                            Halaman ini menampilkan surat masuk yang belum didisposisi. Silakan proses disposisi untuk setiap surat.
                        </p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="action-buttons">
                    <a href="surat-masuk.php" class="btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Surat Masuk
                    </a>
                </div>

                <!-- Data Table -->
                <div class="content-box">
                    <div class="box-header">
                        <h2><i class="fas fa-clock"></i> Daftar Surat Belum Disposisi</h2>
                        <span style="background: #fff3cd; color: #f59e0b; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600;">
                            <i class="fas fa-exclamation-triangle"></i> <?php echo mysqli_num_rows($result); ?> Surat
                        </span>
                    </div>

                    <div class="table-container">
                        <table id="suratBelumDisposisiTable" class="data-table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="3%">No</th>
                                    <th width="7%">Nomor Agenda</th>
                                    <th width="10%">Tanggal Terima</th>
                                    <th width="15%">Alamat Pengirim</th>
                                    <th width="10%">Tanggal Surat</th>
                                    <th width="12%">Nomor Surat</th>
                                    <th width="23%">Perihal</th>
                                    <th width="10%" class="no-export">Status</th>
                                    <th width="15%" class="no-export">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        // Format tanggal
                                        $tgl_terima = date('d/m/Y', strtotime($row['tanggal_terima']));
                                        $tgl_surat = date('d/m/Y', strtotime($row['tanggal_surat']));
                                ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no++; ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($row['nomor_agenda']); ?></td>
                                            <td><?php echo $tgl_terima; ?></td>
                                            <td><?php echo htmlspecialchars($row['alamat_pengirim']); ?></td>
                                            <td><?php echo $tgl_surat; ?></td>
                                            <td><?php echo htmlspecialchars($row['nomor_surat']); ?></td>
                                            <td><?php echo htmlspecialchars($row['perihal']); ?></td>
                                            <td class="text-center no-export">
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> Belum Diproses
                                                </span>
                                            </td>
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

                                                <a href="disposisi-surat.php?id=<?php echo $row['id']; ?>" class="btn-action btn-disposisi" title="Disposisi" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
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
                                } else {
                                    echo '<tr><td colspan="9" class="text-center" style="padding: 40px;">
                                            <i class="fas fa-check-circle" style="font-size: 48px; color: #10b981; margin-bottom: 15px;"></i>
                                            <p style="font-size: 16px; color: #6b7280;"><strong>Tidak ada surat yang menunggu disposisi</strong></p>
                                            <p style="font-size: 14px; color: #9ca3af; margin-top: 5px;">Semua surat masuk sudah didisposisi</p>
                                          </td></tr>';
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
        // DataTables Initialization
        $(document).ready(function() {
            $('#suratBelumDisposisiTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copy',
                        className: 'btn-datatable',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn-datatable',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn-datatable',
                        title: 'Surat Belum Disposisi',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn-datatable',
                        title: 'Surat Belum Disposisi',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn-datatable',
                        title: 'Surat Belum Disposisi',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    }
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
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
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "Semua"]
                ],
                order: [
                    [0, 'desc']
                ],
                responsive: true
            });
        });

        // Konfirmasi hapus
        function confirmDelete(id) {
            if (confirm('Apakah Anda yakin ingin menghapus surat ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'proses-surat-masuk.php';

                const inputAction = document.createElement('input');
                inputAction.type = 'hidden';
                inputAction.name = 'action';
                inputAction.value = 'hapus';

                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'id';
                inputId.value = id;

                form.appendChild(inputAction);
                form.appendChild(inputId);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>

</html>