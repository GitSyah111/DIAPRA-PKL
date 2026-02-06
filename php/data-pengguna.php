<?php
// Koneksi database
include 'database.php';
require_once 'auth_check.php';

if ($role === 'user') {
    header('Location: dashboard.php');
    exit;
}
$can_edit = ($role === 'super_admin');

// Query untuk mengambil data pengguna
$query = "SELECT * FROM user ORDER BY no ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna - DPPKBPM</title>
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
                <p class="subtitle sidebar-text">DIAPRAt</p>
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
                <a href="data-pengguna.php" class="nav-item active" title="Data Pengguna">
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
                    <h1 class="header-title"><button class="header-menu-btn" id="headerMenuBtn">
                        <i class="fas fa-bars"></i>
                    </button>
                    Data Pengguna</h1>
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
                <?php if ($can_edit): ?>
                <div class="action-buttons">
                    <button class="btn-primary" onclick="openAddModal()">
                        <i class="fas fa-plus"></i>
                        Tambah Pengguna
                    </button>
                </div>
                <?php endif; ?>

                <!-- Data Table -->
                <div class="content-box">
                    <div class="box-header">
                        <h2><i class="fas fa-table"></i> Daftar Pengguna</h2>
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Cari nama, username, atau role...">
                        </div>
                    </div>

                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="25%">Nama</th>
                                    <th width="25%">Username</th>
                                    <th width="20%">Password</th>
                                    <th width="15%">Role</th>
                                    <?php if ($can_edit): ?><th width="10%">Aksi</th><?php endif; ?>
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
                                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                                            <td>
                                                <span class="password-hidden" id="pwd-<?php echo $row['no']; ?>">
                                                    <?php echo str_repeat('•', strlen($row['password'])); ?>
                                                </span>
                                                <span class="password-shown" id="pwd-show-<?php echo $row['no']; ?>" style="display:none;">
                                                    <?php echo htmlspecialchars($row['password']); ?>
                                                </span>
                                                <button class="btn-toggle-password" onclick="togglePassword(<?php echo $row['no']; ?>)" title="Lihat Password">
                                                    <i class="fas fa-eye" id="icon-<?php echo $row['no']; ?>"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php echo $row['role']; ?>">
                                                    <?php echo $row['role'] === 'super_admin' ? 'Super Admin' : ucfirst($row['role']); ?>
                                                </span>
                                            </td>
                                            <?php if ($can_edit): ?>
                                            <td class="text-center">
                                                <button class="btn-action btn-edit" onclick="openEditModal(<?php echo $row['no']; ?>, '<?php echo addslashes($row['nama']); ?>', '<?php echo addslashes($row['username']); ?>', '<?php echo $row['role']; ?>')" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-action btn-delete" onclick="confirmDelete(<?php echo $row['no']; ?>)" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="<?= $can_edit ? 6 : 5 ?>" class="text-center empty-data">
                                            <i class="fas fa-users"></i>
                                            <p>Belum ada data pengguna</p>
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
                <h3 id="modalTitle"><i class="fas fa-user-plus"></i> Tambah Data Pengguna</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="dataForm" method="POST" action="proses-pengguna.php">
                <input type="hidden" id="actionType" name="action" value="add">
                <input type="hidden" id="dataId" name="no">

                <div class="form-group">
                    <label for="nama"><i class="fas fa-user"></i> Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap" maxlength="100">
                </div>

                <div class="form-group">
                    <label for="username"><i class="fas fa-at"></i> Username</label>
                    <input type="text" id="username" name="username" required placeholder="Masukkan username" maxlength="255">
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="text" id="password" name="password" required placeholder="Masukkan password" maxlength="100">
                    <small style="color: #6b7280; font-size: 12px; display: block; margin-top: 5px;">
                        <i class="fas fa-info-circle"></i> Maksimal 100 karakter
                    </small>
                </div>

                <div class="form-group">
                    <label for="role"><i class="fas fa-user-tag"></i> Role</label>
                    <select id="role" name="role" required>
                        <option value="">Pilih Role</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
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
    <script src="../js/pengguna.js"></script>

    <style>
        /* Additional styles for user page */
        .password-hidden,
        .password-shown {
            display: inline;
            font-family: monospace;
        }

        .btn-toggle-password {
            background: none;
            border: none;
            color: #3b82f6;
            cursor: pointer;
            padding: 4px 8px;
            margin-left: 8px;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .btn-toggle-password:hover {
            background: #eff6ff;
            color: #2563eb;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-super_admin {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .badge-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .badge-user {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1f2937;
            background: white;
            cursor: pointer;
        }

        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-group small {
            display: block;
            margin-top: 5px;
        }
    </style>
</body>

</html>