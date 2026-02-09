<?php
include 'database.php';

// Array mapping username => nama_bidang
$updates = [
    'bidangdalduk' => 'Bidang Pengendalian Penduduk',
    'bidangKB' => 'Bidang Keluarga Berencana',
    'bidangKS' => 'Bidang Keluarga Sejahtera',
    'bidangPM' => 'Bidang Pemberdayaan Masyarakat'
];

foreach ($updates as $username => $nama_bidang) {
    $query = "UPDATE user SET nama_bidang = '$nama_bidang' WHERE username = '$username'";
    if (mysqli_query($conn, $query)) {
        echo "Updated $username -> $nama_bidang\n";
    } else {
        echo "Error updating $username: " . mysqli_error($conn) . "\n";
    }
}

// Also update Admin/SuperAdmin to have NULL nama_bidang if needed (already NULL)
echo "Update complete.\n";
?>
