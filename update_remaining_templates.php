<?php
// PHP script to batch update remaining PHP files with header menu button and user dropdown

$phpDir = __DIR__ . '/php';
$files = [
    'surat-cuti.php',
    'spj-umpeg.php',
    'data-pengguna.php',
    'data-kepala-dinas.php',
    'tambah-surat-masuk.php',
    'tambah-surat-keluar.php',
    'tambah-surat-cuti.php',
    'tambah-spj-umpeg.php',
    'edit-surat-masuk.php',
    'edit-surat-keluar.php',
    'edit-surat-cuti.php',
    'edit-spj-umpeg.php',
    'disposisi-surat.php',
    'detail-surat-cuti.php',
    'detail-spj-umpeg.php',
    'surat-belum-disposisi.php'
];

foreach ($files as $file) {
    $filePath = $phpDir . '/' . $file;
    
    if (file_exists($filePath)) {
        echo "Updating $file...\n";
        $content = file_get_contents($filePath);
        
        // Pattern 1: Add header menu button
        $pattern1 = '/(<button class="menu-toggle" id="mobileMenuToggle">[\s\S]*?<\/button>\s*)([\s]*<h1 class="header-title">)/';
        $replacement1 = '$1$2<button class="header-menu-btn" id="headerMenuBtn">' . "\r\n" . '                        <i class="fas fa-bars"></i>' . "\r\n" . '                    </button>' . "\r\n" . '                    ';
        $content = preg_replace($pattern1, $replacement1, $content);
        
        // Pattern 2: Update user-info and add dropdown
        $pattern2 = '/<div class="user-info">\s*<span class="user-name"><\?= htmlspecialchars\(\$nama\) \?><\/span>\s*<i class="fas fa-chevron-down"><\/i>\s*<\/div>/s';
        $replacement2 = '<div class="user-info" id="userInfoToggle">' . "\r\n" .
                       '                        <span class="user-name"><?= htmlspecialchars($nama) ?></span>' . "\r\n" .
                       '                        <span class="user-role"><?= ucfirst(htmlspecialchars($role)) ?></span>' . "\r\n" .
                       '                        <i class="fas fa-chevron-down"></i>' . "\r\n" .
                       '                    </div>' . "\r\n" .
                       '                    <div class="user-dropdown" id="userDropdown">' . "\r\n" .
                       '                        <a href="edit-akun.php">' . "\r\n" .
                       '                            <i class="fas fa-user-edit"></i> Edit Akun' . "\r\n" .
                       '                        </a>' . "\r\n" .
                       '                    </div>';
        $content = preg_replace($pattern2, $replacement2, $content);
        
        // Save updated content
        file_put_contents($filePath, $content);
        echo "  ✓ Updated successfully!\n";
    } else {
        echo "  ✗ File not found: $file\n";
    }
}

echo "\nBatch update completed!\n";
?>
