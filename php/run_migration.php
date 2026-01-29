<?php
// Jalankan sekali untuk migrasi role super_admin. Hapus atau jangan diakses setelah dipakai.
include 'database.php';

$alter = "ALTER TABLE `user` MODIFY COLUMN `role` ENUM('super_admin','admin','user') NOT NULL";
if ($conn->query($alter)) {
    echo "OK: ALTER TABLE\n";
} else {
    echo "Error ALTER: " . $conn->error . "\n";
    exit(1);
}

$update = "UPDATE `user` SET `role` = 'super_admin' WHERE `no` = 1";
if ($conn->query($update)) {
    echo "OK: UPDATE user no=1 to super_admin\n";
} else {
    echo "Error UPDATE: " . $conn->error . "\n";
    exit(1);
}
echo "Migrasi selesai.\n";
