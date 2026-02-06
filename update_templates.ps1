# PowerShell script to batch update PHP files
# This script updates sidebar and header structure for all PHP pages

$phpDir = "c:\laragon\www\si_surat\php"
$files = @(
    "surat-keluar.php",
    "surat-cuti.php",
    "spj-umpeg.php",
    "data-pengguna.php",
    "data-kepala-dinas.php",
    "tambah-surat-masuk.php",
    "tambah-surat-keluar.php",
    "tambah-surat-cuti.php",
    "tambah-spj-umpeg.php",
    "edit-surat-masuk.php",
    "edit-surat-keluar.php",
    "edit-surat-cuti.php",
    "edit-spj-umpeg.php",
    "disposisi-surat.php",
    "detail-surat-cuti.php",
    "detail-spj-umpeg.php",
    "surat-belum-disposisi.php"
)

foreach ($file in $files) {
    $filePath = Join-Path $phpDir $file
    
    if (Test-Path $filePath) {
        Write-Host "Updating $file..."
        $content = Get-Content $filePath -Raw -Encoding UTF8
        
        # 1. Merge sidebar header text
        $content = $content -replace '<h2 class="sidebar-text">DPPKBPM</h2>\r?\n\s*<p class="subtitle sidebar-text">DIAPRA</p>\r?\n\s*<p class="username sidebar-text"><i class="fas fa-user-circle"></i> <\?= htmlspecialchars\(\$nama\) \?></p>', '<h2 class="sidebar-text">DIAPRA DPPKBPM</h2>'
        
        # 2. Remove toggle button
        $content = $content -replace '\s*<!-- Toggle Button -->\r?\n\s*<button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">\r?\n\s*<i class="fas fa-chevron-left"></i>\r?\n\s*</button>\r?\n', "`r`n"
        
        # 3. Add header menu button
        $content = $content -replace '(<button class="menu-toggle" id="mobileMenuToggle">\r?\n\s*<i class="fas fa-bars"></i>\r?\n\s*</button>\r?\n)([ \t]+)(<h1 class="header-title">)', '$1$2<button class="header-menu-btn" id="headerMenuBtn">' + "`r`n" + '$2    <i class="fas fa-bars"></i>' + "`r`n" + '$2</button>' + "`r`n" + '$2$3'
        
        # 4. Update user-info with dropdown
        $content = $content -replace '<div class="user-info">\r?\n\s*<span class="user-name"><\?= htmlspecialchars\(\$nama\) \?></span>\r?\n\s*<i class="fas fa-chevron-down"></i>\r?\n\s*</div>', '<div class="user-info" id="userInfoToggle">' + "`r`n" + '                        <span class="user-name"><?= htmlspecialchars($nama) ?></span>' + "`r`n" + '                        <span class="user-role"><?= ucfirst(htmlspecialchars($role)) ?></span>' + "`r`n" + '                        <i class="fas fa-chevron-down"></i>' + "`r`n" + '                    </div>' + "`r`n" + '                    <div class="user-dropdown" id="userDropdown">' + "`r`n" + '                        <a href="edit-akun.php">' + "`r`n" + '                            <i class="fas fa-user-edit"></i> Edit Akun' + "`r`n" + '                        </a>' + "`r`n" + '                    </div>'
        
        # Save updated content
        Set-Content -Path $filePath -Value $content -Encoding UTF8 -NoNewline
        Write-Host "  Updated successfully!"
    } else {
        Write-Host "  File not found: $file"
    }
}

Write-Host "`nBatch update completed!"
