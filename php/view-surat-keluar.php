<?php
// Koneksi database
include 'database.php';
require_once 'auth_check.php';

// Cek apakah ada ID
if (!isset($_GET['id'])) {
    header("Location: surat-keluar.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data surat keluar
$query = "SELECT * FROM surat_keluar WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>
        alert('Data surat tidak ditemukan!');
        window.location.href = 'surat-keluar.php';
    </script>";
    exit();
}

$surat = mysqli_fetch_assoc($result);

// Cek apakah file ada
if (empty($surat['file_surat'])) {
    echo "<script>
        alert('File surat tidak ditemukan!');
        window.location.href = 'surat-keluar.php';
    </script>";
    exit();
}

$file_path = '../uploads/surat_keluar/' . $surat['file_surat'];

// Cek apakah file fisik ada
if (!file_exists($file_path)) {
    echo "<script>
        alert('File surat tidak ditemukan di server!');
        window.location.href = 'surat-keluar.php';
    </script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat <?php echo htmlspecialchars($surat['nomor_surat']); ?> - DPPKBPM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            overflow: hidden;
        }

        /* Header */
        .pdf-header {
            background: linear-gradient(135deg, #2c4a6d 0%, #1e3a5f 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-left img {
            height: 40px;
            width: auto;
        }

        .header-info h1 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .header-info p {
            font-size: 13px;
            opacity: 0.9;
        }

        .header-right {
            display: flex;
            gap: 10px;
        }

        .btn-header {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-header:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        /* Toolbar */
        .pdf-toolbar {
            background: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            z-index: 999;
        }

        .toolbar-left,
        .toolbar-right {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .toolbar-center {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-toolbar {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            color: #1f2937;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-toolbar:hover {
            background: #e5e7eb;
            border-color: #d1d5db;
        }

        .btn-toolbar:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .page-info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #4b5563;
        }

        .page-input {
            width: 50px;
            padding: 6px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            text-align: center;
            font-size: 14px;
        }

        /* PDF Container */
        .pdf-container {
            position: fixed;
            top: 136px;
            left: 0;
            right: 0;
            bottom: 0;
            background: #e5e7eb;
            overflow: auto;
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        #pdf-canvas {
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            max-width: 100%;
            height: auto;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
        }

        /* Loading */
        .loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 2000;
        }

        .loading i {
            font-size: 48px;
            color: #3b82f6;
            animation: spin 1s linear infinite;
        }

        .loading p {
            margin-top: 15px;
            font-size: 16px;
            color: #6b7280;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-info h1 {
                font-size: 14px;
            }

            .header-info p {
                font-size: 11px;
            }

            .btn-header,
            .btn-toolbar {
                padding: 6px 10px;
                font-size: 12px;
            }

            .toolbar-left,
            .toolbar-right,
            .toolbar-center {
                gap: 5px;
            }

            .btn-toolbar span {
                display: none;
            }

            .pdf-container {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="pdf-header">
        <div class="header-left">
            <img src="../assets/img/LOGO.png" alt="Logo DPPKBPM">
            <div class="header-info">
                <h1>Surat <?php echo htmlspecialchars($surat['nomor_surat']); ?></h1>
                <p><?php echo htmlspecialchars($surat['perihal']); ?></p>
            </div>
        </div>
        <div class="header-right">
            <a href="surat-keluar.php" class="btn-header">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <a href="<?php echo $file_path; ?>" download class="btn-header btn-primary">
                <i class="fas fa-download"></i>
            </div>
            <button class="btn-toolbar" id="next-page">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <div class="toolbar-right">
            <button class="btn-toolbar" id="rotate-left">
                <i class="fas fa-undo"></i>
                <span>Rotate</span>
            </button>
            <button class="btn-toolbar" id="print-btn">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    <div class="loading" id="loading">
        <i class="fas fa-spinner"></i>
        <p>Loading PDF...</p>
    </div>

    <div class="pdf-container" id="pdf-container">
        <canvas id="pdf-canvas"></canvas>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let pdfDoc = null;
        let pageNum = 1;
        let pageRendering = false;
        let pageNumPending = null;
        let scale = 1.5;
        let rotation = 0;

        const canvas = document.getElementById('pdf-canvas');
        const ctx = canvas.getContext('2d');
        const loading = document.getElementById('loading');

        const url = '<?php echo $file_path; ?>';

        pdfjsLib.getDocument(url).promise.then(function(pdf) {
            pdfDoc = pdf;
            document.getElementById('total-pages').textContent = pdf.numPages;
            document.getElementById('page-input').max = pdf.numPages;
            loading.style.display = 'none';
            renderPage(pageNum);
        }).catch(function(error) {
            console.error('Error loading PDF:', error);
            alert('Error loading PDF: ' + error.message);
        });

        function renderPage(num) {
            pageRendering = true;
            pdfDoc.getPage(num).then(function(page) {
                const devicePixelRatio = window.devicePixelRatio || 1;
                const viewport = page.getViewport({
                    scale: scale * devicePixelRatio,
                    rotation: rotation
                });

                canvas.height = viewport.height;
                canvas.width = viewport.width;
                canvas.style.width = (viewport.width / devicePixelRatio) + 'px';
                canvas.style.height = (viewport.height / devicePixelRatio) + 'px';

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };

                const renderTask = page.render(renderContext);
                renderTask.promise.then(function() {
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                });
            });

            document.getElementById('page-input').value = num;
            updateButtons();
        }

        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        function updateButtons() {
            document.getElementById('prev-page').disabled = (pageNum <= 1);
            document.getElementById('next-page').disabled = (pdfDoc && pageNum >= pdfDoc.numPages);
        }

        document.getElementById('prev-page').addEventListener('click', function() {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
        });

        document.getElementById('next-page').addEventListener('click', function() {
            if (pdfDoc && pageNum >= pdfDoc.numPages) return;
            pageNum++;
            queueRenderPage(pageNum);
        });

        document.getElementById('page-input').addEventListener('change', function() {
            let num = parseInt(this.value);
            if (pdfDoc) {
                if (num < 1) num = 1;
                if (num > pdfDoc.numPages) num = pdfDoc.numPages;
            } else {
                num = 1;
            }
            pageNum = num;
            queueRenderPage(pageNum);
        });

        document.getElementById('rotate-left').addEventListener('click', function() {
            rotation = (rotation + 90) % 360;
            queueRenderPage(pageNum);
        });

        document.getElementById('print-btn').addEventListener('click', function() {
            const printWindow = window.open('<?php echo $file_path; ?>', '_blank');
            if (printWindow) {
                printWindow.onload = function() {
                    printWindow.focus();
                };
            } else {
                alert('Popup diblokir! Silakan izinkan popup untuk fitur print.');
            }
        });
    </script>
</body>

</html>