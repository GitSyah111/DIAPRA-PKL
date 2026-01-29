<?php
// Koneksi database
include 'database.php';
require_once 'auth_check.php';

// Query untuk mengambil data Surat Cuti dengan urutan terbaru
$query = "SELECT * FROM `surat cuti` ORDER BY id DESC";
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
    <!-- Link CSS untuk dashboard -->
    <link rel="stylesheet" href="../css/dashboard.css">
    <!-- Link CSS untuk kepala dinas -->
    <link rel="stylesheet" href="../css/kepala-dinas.css">
    <!-- Link CSS untuk surat masuk -->
    <link rel="stylesheet" href="../css/surat-masuk.css">
    <!-- Link Font Awesome untuk icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
</head>

<body>
    <!-- Container utama -->
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <!-- Header sidebar -->
            <div class="sidebar-header">
                <!-- Logo -->
                <div class="logo">
                    <img src="../assets/img/LOGO.png" alt="Logo DPPKBPM" class="logo-img">
                </div>
                <!-- Nama instansi -->
                <h2 class="sidebar-text">DPPKBPM</h2>
                <!-- Subtitle instansi -->
                <p class="subtitle sidebar-text">DIAPRA</p>
                <!-- Username pengguna -->
                <p class="username sidebar-text"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($nama) ?></p>
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
                <a href="spj-umpeg.php" class="nav-item" title="SPJ UMPEG">
                    <i class="fas fa-file-invoice"></i>
                    <span class="sidebar-text">SPJ UMPEG</span>
                </a>
                <!-- Menu Surat Cuti -->
                <a href="surat-cuti.php" class="nav-item active" title="Surat Cuti">
                    <i class="fas fa-calendar-check"></i>
                    <span class="sidebar-text">Surat Cuti</span>
                </a>
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
            </nav>

            <!-- Footer sidebar -->
            <div class="sidebar-footer sidebar-text">
                <p><i class="fas fa-info-circle"></i> Versi 1.0.0</p>
            </div>

            <!-- Toggle Button untuk sidebar -->
            <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
                <i class="fas fa-chevron-left"></i>
            </button>
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
                    <!-- Judul halaman -->
                    <h1 class="header-title">Surat Cuti</h1>
                </div>
                <!-- Bagian kanan header -->
                <div class="header-right">
                    <!-- Info pengguna -->
                    <div class="user-info">
                        <span class="user-name"><?= htmlspecialchars($nama) ?></span>
                        <i class="fas fa-chevron-down"></i>
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
                    <!-- Header box -->
                    <div class="box-header">
                        <h2><i class="fas fa-calendar-check"></i> Daftar Surat Cuti</h2>
                    </div>

                    <!-- Container tabel -->
                    <div class="table-container">
                        <!-- Tabel DataTables -->
                        <table id="suratCutiTable" class="data-table display" style="width:100%">
                            <!-- Header tabel -->
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="12%">Nama/NIP</th>
                                    <th width="10%">Pangkat/GOL RUANG</th>
                                    <th width="12%">Jabatan</th>
                                    <th width="10%">Jenis Cuti</th>
                                    <th width="8%">Lamanya</th>
                                    <th width="10%">Mulai Cuti</th>
                                    <th width="10%">Sampai Dengan</th>
                                    <th width="8%">Sisa Cuti</th>
                                    <th width="15%" class="no-export">Aksi</th>
                                </tr>
                            </thead>
                            <!-- Body tabel -->
                            <tbody>
                                <?php
                                // Cek apakah ada data
                                if (mysqli_num_rows($result) > 0) {
                                    // Inisialisasi nomor urut
                                    $no = 1;
                                    // Loop untuk menampilkan data
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        // Konversi timestamp Mulai Cuti ke format tanggal
                                        $mulai_cuti = $row['Mulai Cuti'] > 0 ? date('d/m/Y', $row['Mulai Cuti']) : '-';
                                        // Konversi timestamp Sampai Dengan ke format tanggal
                                        $sampai_dengan = $row['Sampai Dengan'] > 0 ? date('d/m/Y', $row['Sampai Dengan']) : '-';
                                ?>
                                        <tr>
                                            <!-- Kolom nomor -->
                                            <td class="text-center"><?php echo $no++; ?></td>
                                            <!-- Kolom Nama/NIP -->
                                            <td><?php echo htmlspecialchars($row['Nama/NIP']); ?></td>
                                            <!-- Kolom Pangkat/GOL RUANG -->
                                            <td><?php echo htmlspecialchars($row['Pangkat/GOL RUANG']); ?></td>
                                            <!-- Kolom Jabatan -->
                                            <td><?php echo htmlspecialchars($row['Jabatan']); ?></td>
                                            <!-- Kolom Jenis Cuti -->
                                            <td><?php echo htmlspecialchars($row['Jenis Cuti']); ?></td>
                                            <!-- Kolom Lamanya -->
                                            <td><?php echo htmlspecialchars($row['Lamanya']); ?></td>
                                            <!-- Kolom Mulai Cuti -->
                                            <td data-date="<?= $row['Mulai Cuti'] > 0 ? date('Y-m-d', $row['Mulai Cuti']) : '' ?>"><?php echo $mulai_cuti; ?></td>
                                            <!-- Kolom Sampai Dengan -->
                                            <td><?php echo $sampai_dengan; ?></td>
                                            <!-- Kolom Sisa Cuti -->
                                            <td><?php echo htmlspecialchars($row['Sisa Cuti']); ?></td>
                                            <!-- Kolom Aksi -->
                                            <td class="text-center action-buttons-cell no-export">
                                                <!-- Tombol lihat detail -->
                                                <a href="detail-surat-cuti.php?id=<?php echo $row['id']; ?>"
                                                    class="btn-action btn-view" title="Lihat Detail"
                                                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <!-- Tombol edit -->
                                                <a href="edit-surat-cuti.php?id=<?php echo $row['id']; ?>"
                                                    class="btn-action btn-edit" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <!-- Tombol hapus -->
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

    <!-- JavaScript dashboard -->
    <script src="../js/dashboard.js"></script>

    <script>
        // Inisialisasi ketika dokumen siap
        $(document).ready(function() {
            // Initialize DataTable
            var tableCuti = $('#suratCutiTable').DataTable({
                // Layout DataTables dengan buttons
                dom: 'Bfrtip',
                // Konfigurasi tombol export
                buttons: [{
                        // Tombol export Excel
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'dt-button',
                        // Opsi export, exclude kolom no-export
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    },
                    {
                        // Tombol export PDF
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'dt-button',
                        // Opsi export, exclude kolom no-export
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    }
                ],
                // Konfigurasi bahasa Indonesia
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
                // Jumlah data per halaman
                pageLength: 10,
                // Urutan default berdasarkan kolom pertama ascending
                order: [
                    [0, 'asc']
                ]
            });
            // Filter tanggal (Mulai Cuti = kolom index 6)
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'suratCutiTable') return true;
                var dari = $('#filterDari').val();
                var sampai = $('#filterSampai').val();
                if (!dari && !sampai) return true;
                var row = $(tableCuti.row(dataIndex).node());
                var dateVal = row.find('td:eq(6)').attr('data-date');
                if (!dateVal) return false;
                if (dari && dateVal < dari) return false;
                if (sampai && dateVal > sampai) return false;
                return true;
            });
            $('#btnFilterTanggal').on('click', function() { tableCuti.draw(); });
            $('#btnResetTanggal').on('click', function() {
                $('#filterDari').val('');
                $('#filterSampai').val('');
                tableCuti.draw();
            });
        });

        // Fungsi konfirmasi hapus data
        function confirmDelete(id) {
            // Tampilkan dialog konfirmasi
            if (confirm('Apakah Anda yakin ingin menghapus data Surat Cuti ini?')) {
                // Redirect ke proses-surat-cuti.php dengan action delete
                window.location.href = 'proses-surat-cuti.php?action=delete&id=' + id;
            }
        }
    </script>
</body>

</html>