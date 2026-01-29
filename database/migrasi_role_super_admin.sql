-- Tambah role super_admin ke enum dan set user Admin (no=1) sebagai super_admin
ALTER TABLE `user` MODIFY COLUMN `role` ENUM('super_admin','admin','user') NOT NULL;
UPDATE `user` SET `role` = 'super_admin' WHERE `no` = 1;
