<?php
// File: disposisi-masuk.php
// Halaman Inbox Disposisi untuk Role Bidang

include 'database.php';
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED); // Suppress warnings for cleaner output
require_once 'auth_check.php';

// Cek Role: Hanya untuk 'bidang'
if ($_SESSION['role'] !== 'bidang') {
    header("Location: dashboard.php");
    exit();
}

$nama_bidang = $_SESSION['nama_bidang'];

// Query mengambil disposisi yang ditujukan ke bidang ini
// Join dengan surat_masuk untuk ambil detail suratnya
$query = "SELECT disposisi.*, 
                 surat_masuk.nomor_agenda, 
                 surat_masuk.nomor_surat, 
                 surat_masuk.perihal, 
                 surat_masuk.tanggal_surat,
                 surat_masuk.alamat_pengirim
          FROM disposisi 
          JOIN surat_masuk ON disposisi.id_surat_masuk = surat_masuk.id 
          WHERE disposisi.tujuan_bidang = '$nama_bidang' 
          ORDER BY disposisi.id DESC";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disposisi Masuk - DPPKBPM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/kepala-dinas.css">
    <link rel="stylesheet" href="../css/surat-masuk.css">
    
    <style>
        /* Status Baca Badge */
        .badge-unread {
            background-color: #ef4444;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            animation: pulse 2s infinite;
        }
        
        .badge-read {
            background-color: #10b981;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
    </style>
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
                <a href="surat-masuk.php" class="nav-item" title="Surat Masuk">
                    <i class="fas fa-inbox"></i>
                    <span class="sidebar-text">Surat Masuk</span>
                </a>
                <a href="surat-keluar.php" class="nav-item" title="Surat Keluar">
                    <i class="fas fa-paper-plane"></i>
                    <span class="sidebar-text">Surat Keluar</span>
                </a>
                
                <!-- Menu Disposisi Masuk (Active) -->
                <a href="disposisi-masuk.php" class="nav-item active" title="Disposisi Masuk">
                    <i class="fas fa-inbox"></i>
                    <span class="sidebar-text">Disposisi Masuk</span>
                </a>

                <?php if ($role !== 'user' && $role !== 'bidang'): ?>
                <a href="spj-umpeg.php" class="nav-item" title="SPJ UMPEG">
                    <i class="fas fa-file-invoice"></i>
                    <span class="sidebar-text">SPJ UMPEG</span>
                </a>
                <?php endif; ?>
                
                <a href="surat-cuti.php" class="nav-item" title="Surat Cuti">
                    <i class="fas fa-calendar-check"></i>
                    <span class="sidebar-text">Surat Cuti</span>
                </a>
                
                <?php if ($role !== 'user' && $role !== 'bidang'): ?>
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
                <div style="margin-top: 10px; font-size: 0.8rem; color: #a1a1aa;">
                    Data Tahun: <strong><?= htmlspecialchars(isset($tahun_aktif) ? $tahun_aktif : date('Y')) ?></strong>
                </div>
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
                    <h1 class="header-title"><button class="header-menu-btn" id="headerMenuBtn">
                        <i class="fas fa-bars"></i>
                    </button>
                    Disposisi Masuk</h1>
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
                        <button class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content">
                <!-- Info Box -->
                <div style="background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #0ea5e9; display: flex; align-items: center; gap: 15px;">
                    <i class="fas fa-info-circle" style="font-size: 24px; color: #0ea5e9;"></i>
                    <div>
                        <strong style="color: #0c4a6e; font-size: 16px;">Disposisi Masuk untuk: <?php echo htmlspecialchars($nama_bidang ?? ''); ?></strong>
                        <p style="color: #0c4a6e; margin-top: 5px; font-size: 14px;">
                            Daftar disposisi surat yang ditujukan kepada bidang Anda.
                        </p>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="content-box">
                    <div class="box-header">
                        <h2><i class="fas fa-inbox"></i> Daftar Disposisi</h2>
                    </div>

                    <div class="table-container">
                        <table id="disposisiTable" class="data-table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">No. Surat</th>
                                    <th width="20%">Perihal</th>
                                    <th width="20%">Isi Disposisi</th>
                                    <th width="10%">Sifat</th>
                                    <th width="10%">Batas Waktu</th>
                                    <th width="20%" class="no-export">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        // Format tanggal
                                        $batas_waktu = !empty($row['batas_waktu']) ? date('d/m/Y', strtotime($row['batas_waktu'])) : '-';
                                        
                                        // Status Baca
                                        $status_baca = $row['status_baca'] == 0 ? 
                                            '<span class="badge-unread">Belum Dibaca</span>' : 
                                            '<span class="badge-read">Sudah Dibaca</span>';
                                ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($row['nomor_surat'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['perihal'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars(substr($row['isi_disposisi'] ?? '', 0, 50)) . '...'; ?></td>
                                            <td><span class="badge badge-info"><?php echo htmlspecialchars($row['sifat'] ?? ''); ?></span></td>
                                            <td><?php echo $batas_waktu; ?></td>
                                            <td class="text-center action-buttons-cell">
                                                <!-- Download File Disposisi -->
                                                <?php if (!empty($row['file_disposisi'])): ?>
                                                    <a href="../uploads/disposisi/<?php echo $row['file_disposisi']; ?>" class="btn-action btn-view-disposisi" title="Lihat File Disposisi" target="_blank">
                                                        <i class="fas fa-file-invoice"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <!-- Lihat Surat Asli -->
                                                 <?php if (!empty($row['file_surat'])): ?>
                                                    <a href="../uploads/<?php echo $row['file_surat']; ?>" class="btn-action btn-view" title="Lihat Surat Asli" target="_blank" style="background: #3b82f6;">
                                                        <i class="fas fa-eye"></i> Surat Asli
                                                    </a>
                                                <?php endif; ?>
                                                

                                            </td>
                                        </tr>
                                <?php
                                    }
                                    // PENTING: Jangan gunakan colspan manual yang tidak sesuai jumlah kolom thead
                                    // DataTables akan otomatis menangani "No data available" jika tbody kosong
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Detail Disposisi -->
            <div id="detailModal" class="preview-modal" style="display: none;">
                <div class="preview-modal-content" style="max-width: 600px; height: auto; max-height: 90vh; overflow-y: auto;">
                    <div class="preview-header">
                        <h2 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-info-circle"></i> Detail Disposisi
                        </h2>
                        <button onclick="closeDetailModal()" class="close-btn">&times;</button>
                    </div>
                    <div class="preview-body" style="padding: 20px;">
                        <div class="detail-row" style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                            <strong>No. Agenda / Surat:</strong><br>
                            <span id="modalNoAgenda"></span> / <span id="modalNoSurat"></span>
                        </div>
                        <div class="detail-row" style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                            <strong>Perihal:</strong><br>
                            <span id="modalPerihal"></span>
                        </div>
                        <div class="detail-row" style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                            <strong>Sifat:</strong> <span id="modalSifat" class="badge badge-info"></span>
                        </div>
                         <div class="detail-row" style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                            <strong>Batas Waktu:</strong> <span id="modalBatasWaktu"></span>
                        </div>
                        <div class="detail-row" style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                            <strong>Isi Instruksi:</strong><br>
                            <p id="modalInstruksi" style="background: #f9fafb; padding: 10px; border-radius: 6px; margin-top: 5px;"></p>
                        </div>
                        <div class="detail-row" style="margin-bottom: 15px;">
                            <strong>Catatan:</strong><br>
                            <p id="modalCatatan" style="font-style: italic; color: #666;"></p>
                        </div>
                        <div id="modalFileArea" style="margin-top: 20px; text-align: center;">
                            <a href="#" id="btnDownloadFile" class="btn-primary" target="_blank" style="display: inline-block; width: 100%; text-decoration: none;">
                                <i class="fas fa-download"></i> Download File Disposisi
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="main-footer">
                <p>&copy; 2025 <strong>DPPKBPM</strong> - Dinas Pengendalian Penduduk dan Keluarga Berencana Pemberdayaan Masyarakat</p>
            </footer>
        </main>
    </div>

    <!-- jQuery & Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="../js/dashboard.js"></script>
    <script>
        $(document).ready(function() {
            // Hapus instance lama jika ada untuk mencegah konflik
            if ($.fn.DataTable.isDataTable('#disposisiTable')) {
                $('#disposisiTable').DataTable().destroy();
            }

            $('#disposisiTable').DataTable({
                responsive: true,
                stateSave: false, // Hindari cache struktur lama
                // HAPUS properti 'columns: [...]' yang kaku agar jumlah kolom dideteksi otomatis dari HTML
                columnDefs: [{
                    targets: -1, // Gunakan indeks negatif (-1) untuk selalu merujuk ke kolom TERAKHIR (Aksi)
                    orderable: false, // Matikan sorting
                    searchable: false // Matikan pencarian di kolom aksi
                }],
                language: {
                    search: "Cari Disposisi:",
                    zeroRecords: "Tidak ada data disposisi",
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
                order: [
                    [0, 'asc'] // Default urutkan berdasarkan kolom pertama (No)
                ]
            });
        });


    </script>
</body>
</html>
