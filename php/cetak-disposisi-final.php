<?php
// FORCE REFRESH - Clear All Cache
if (function_exists('opcache_reset')) {
    opcache_reset();
}
clearstatcache();

// Disable Browser Cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// 1. Panggil Composer Autoload
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Sertakan koneksi Database
include 'database.php';

// 3. Ambil data
if (!isset($_GET['id'])) {
    die("Error: ID Surat tidak ditemukan.");
}
$id_surat = mysqli_real_escape_string($conn, $_GET['id']);

$query_surat = "SELECT * FROM surat_masuk WHERE id = '$id_surat'";
$result_surat = mysqli_query($conn, $query_surat);
if (mysqli_num_rows($result_surat) == 0) {
    die("Error: Data surat tidak ditemukan.");
}
$surat = mysqli_fetch_assoc($result_surat);

$query_kadis = "SELECT * FROM kadis ORDER BY no DESC LIMIT 1";
$result_kadis = mysqli_query($conn, $query_kadis);
if (mysqli_num_rows($result_kadis) == 0) {
    die("Error: Data Kepala Dinas tidak ditemukan!");
}
$kadis = mysqli_fetch_assoc($result_kadis);

// Helper array untuk data checkbox
$tujuan_selected = !empty($surat['tujuan_disposisi']) ? explode(', ', $surat['tujuan_disposisi']) : [];
$instruksi_selected = !empty($surat['instruksi_disposisi']) ? explode(', ', $surat['instruksi_disposisi']) : [];

// Fungsi checkbox untuk mPDF
function getCheckbox($isChecked)
{
    // Gunakan font yang mendukung simbol (dejavusans)
    return $isChecked ? '<span style="font-family: dejavusans;">☑</span>' : '<span style="font-family: dejavusans;">☐</span>';
}

// 4. Mulai Output Buffering
ob_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Lembar Disposisi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 13px;
        }

        .blue-text {
            color: blue;
        }

        .kota {
            font-size: 15px;
        }

        .disposisi-container {
            width: 100%;
        }

        /* Header - Menggunakan Table untuk Kompatibilitas mPDF */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            height: fit-content;
        }

        .header-table td {
            vertical-align: middle;
            padding: 0;
        }

        .logo-cell {
            width: 100px;
            text-align: left;
            padding-left: 30px;
            padding-top: 20px;
        }

        .header-text {
            text-align: center;
            padding-top: 15px;
        }

        .header-text h2 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
            font-weight: bold;
            line-height: 1.4;
        }

        .header-text p {
            margin: 2px 0;
            font-size: 11px;
        }

        /* Kotak Garis */
        .garis {
            border: 1px solid #000;
            padding: 2px;
        }

        .title {
            text-align: center;
            font-size: 20px;
            margin: 15px 0 10px 0;
            font-weight: bold;
        }

        /* Content Section - Mirip Blade Template */
        .content-section {
            width: 100%;
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 2px;
            background: #f9f9f9;
            box-sizing: border-box;
        }

        /* Content Section khusus untuk Catatan - padding lebih kecil */
        .content-section.catatan-section {
            padding: 5px;
        }

        /* Info Surat dengan Input Style */
        .info-row {
            margin-bottom: 2px;
            line-height: 1.5;
        }

        .info-label {
            display: inline-block;
            width: 120px;
            font-weight: bold;
            vertical-align: top;
        }

        .info-value {
            display: inline-block;
            width: 75%;
            border-bottom: 1px solid #ccc;
            padding: 2px 5px;
            background: white;
        }

        /* Checkbox Group untuk Sifat */
        .checkbox-group {
            display: inline-block;
            margin-left: 5px;
        }

        .checkbox-item {
            display: inline-block;
            margin-right: 15px;
        }

        /* Textarea Style */
        .textarea-box {
            width: 80%;
            min-height: 90px;
            border: 1px solid #999;
            padding: 5px;
            background: white;
            font-family: Arial, sans-serif;
            font-size: 13px;
            line-height: 1.4;
            vertical-align: top;
            display: inline-block;
        }

        /* Disposisi Section dengan Table */
        .disposisi-table {
            width: 100%;
            border-collapse: collapse;
        }

        .disposisi-table td {
            width: 48%;
            vertical-align: top;
            padding: 5px;
        }

        .disposisi-table td:first-child {
            padding-right: 10px;
        }

        .disposisi-table td:last-child {
            padding-left: 20px;
        }

        .disposisi-label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }

        .disposisi-checkbox-item {
            margin-bottom: 2px;
            line-height: 1.5;
        }

        /* Catatan Section */
        .catatan-label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }

        .catatan-textarea {
            width: 100%;
            min-height: 120px;
            border: 1px solid #999;
            padding: 100px;
            background: white;
            font-family: Arial, sans-serif;
            font-size: 13px;
            line-height: 1.45;
            box-sizing: border-box;
        }

        /* Signature Section - Di Luar Kotak Garis */
        .signature-section {
            margin-top: 15px;
            margin-left: 500px;
            text-align: left;
            page-break-inside: avoid;
        }

        .signature-section p {
            margin: 1px 0;
            line-height: 1.3;
            font-size: 13px;
        }

        .signature-space {
            height: 50px;
        }
    </style>
</head>

<body>
    <div class="disposisi-container">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <?php
                    $logo_path = __DIR__ . '/../assets/img/LOGO.png';
                    if (file_exists($logo_path)) {
                        $logo_data = base64_encode(file_get_contents($logo_path));
                        echo '<img src="data:image/png;base64,' . $logo_data . '" width="100" />';
                    } else {
                        echo '<div style="width:100px;height:100px;border:1px solid #000;"></div>';
                    }
                    ?>
                </td>
                <td class="header-text">
                    <p class="kota">PEMERINTAH KOTA BANJARMASIN</p>
                    <h2>DINAS PENGENDALIAN PENDUDUK KELUARGA<br>BERENCANA DAN PEMBERDAYAAN MASYARAKAT</h2>
                    <p>Jalan Brigjend. H. Hasan Basri - Kayutangi II RT. 16 Banjarmasin 70124</p>
                    <p>Pos-el: <u class="blue-text"><?php echo htmlspecialchars($kadis['email'] ?? 'dppkbpm@gmail.com'); ?></u>, Laman <u class="blue-text"><?php echo htmlspecialchars($kadis['laman'] ?? 'https://dppkbpm.banjarmasinkota.go.id/'); ?></u></p>
                </td>
            </tr>
        </table>

        <!-- Kotak Garis -->
        <div class="garis">
            <div class="title">Lembar Disposisi</div>

            <!-- Section 1: Info Surat -->
            <div class="content-section">
                <div class="info-row">
                    <span class="info-label">Surat dari :</span>
                    <span class="info-value"><?php echo htmlspecialchars($surat['alamat_pengirim']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nomor Surat :</span>
                    <span class="info-value"><?php echo htmlspecialchars($surat['nomor_surat']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Surat:</span>
                    <span class="info-value"><?php echo date('d-m-Y', strtotime($surat['tanggal_surat'])); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nomor Agenda:</span>
                    <span class="info-value"><?php echo htmlspecialchars($surat['nomor_agenda']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Sifat:</span>
                    <div class="checkbox-group">
                        <span class="checkbox-item"><?php echo getCheckbox(($surat['sifat_surat'] ?? '') == 'Sangat Segera'); ?> Sangat Segera</span>
                        <span class="checkbox-item"><?php echo getCheckbox(($surat['sifat_surat'] ?? '') == 'Segera'); ?> Segera</span>
                        <span class="checkbox-item"><?php echo getCheckbox(($surat['sifat_surat'] ?? '') == 'Rahasia'); ?> Rahasia</span>
                    </div>
                </div>
                <div class="info-row">
                    <span class="info-label" style="vertical-align: top;">Perihal:</span>
                    <div class="textarea-box"><?php echo nl2br(htmlspecialchars($surat['perihal'])); ?></div>
                </div>
            </div>

            <!-- Section 2: Disposisi -->
            <div class="content-section">
                <table class="disposisi-table">
                    <tr>
                        <td>
                            <span class="disposisi-label">Diteruskan kepada Sdr:</span>
                            <?php
                            $tujuan_options = [
                                'Sekretaris',
                                'Kabid Keluarga Berencana',
                                'Kabid Keluarga Sejahtera',
                                'Kabid Pengendalian Penduduk dan Informasi Data',
                                'Kabid Pemberdayaan Masyarakat'
                            ];
                            foreach ($tujuan_options as $option) {
                                echo '<div class="disposisi-checkbox-item">' . getCheckbox(in_array($option, $tujuan_selected)) . ' ' . htmlspecialchars($option) . '</div>';
                            }
                            ?>
                        </td>
                        <td>
                            <span class="disposisi-label">Dengan hormat harap:</span>
                            <?php
                            $instruksi_options = [
                                'Tanggapan dan Saran',
                                'Proses lebih lanjut',
                                'Koordinasi/Konfirmasi',
                                '...................................'
                            ];
                            foreach ($instruksi_options as $option) {
                                echo '<div class="disposisi-checkbox-item">' . getCheckbox(in_array($option, $instruksi_selected)) . ' ' . htmlspecialchars($option) . '</div>';
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Section 3: Catatan -->
            <div class="content-section catatan-section">
                <span class="catatan-label">Catatan:</span>
                <div class="catatan-textarea">
                    <?php echo nl2br(htmlspecialchars($surat['catatan_disposisi'] ?? '')); ?>
                </div>
            </div>

        </div>
        <!-- End Kotak Garis -->

        <!-- Signature - Di Luar Kotak -->
        <div class="signature-section">
            <p>Kepala DPPKBPM<br>Kota Banjarmasin</p>
            <div class="signature-space"></div>
            <p><strong><?php echo htmlspecialchars($kadis['nama']); ?></strong><br>
                <?php echo htmlspecialchars($kadis['pangkat']); ?><br>
                NIP. <?php echo htmlspecialchars($kadis['NIP']); ?></p>
        </div>

    </div>
</body>

</html>
<?php
// 5. Ambil HTML dari buffer
$html = ob_get_clean();

// 6. Inisialisasi mPDF
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 15,
    'margin_right' => 15,
    'margin_top' => 10,
    'margin_bottom' => 10,
    'default_font' => 'dejavusans',
    'default_font_size' => 13,
    'tempDir' => sys_get_temp_dir() . '/mpdf_' . time() // Unique temp dir setiap request
]);

// Clear mPDF cache
$mpdf->SetDisplayMode('fullpage');

// 7. Tulis HTML ke PDF
$mpdf->WriteHTML($html);

// 8. Output PDF dengan timestamp untuk prevent cache
$nama_file = 'Lembar_Disposisi_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $surat['nomor_surat']) . '_' . time() . '.pdf';
$mpdf->Output($nama_file, 'I');
exit;
?>