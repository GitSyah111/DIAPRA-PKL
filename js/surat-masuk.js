// ========================================
// SURAT MASUK - JAVASCRIPT
// ========================================

// Initialize DataTables
$(document).ready(function () {
    if ($('#suratMasukTable').length) {
        var tableSuratMasuk = $('#suratMasukTable').DataTable({
            "language": {
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data tersedia",
                "infoFiltered": "(difilter dari _MAX_ total data)",
                "search": "Cari:",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            },
            "pageLength": 10,
            "ordering": true,
            "order": [[0, "desc"]], // Sort by ID descending
            "scrollX": true, // Enable horizontal scrolling
            "autoWidth": false, // Disable auto width calculation to allow responsive sizing
            "dom": 'Bfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'dt-button',
                    title: 'Data Surat Masuk',
                    exportOptions: {
                        columns: ':not(.no-export)'
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'dt-button',
                    title: 'Data Surat Masuk',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':not(.no-export)'
                    }
                }
            ],
            "columnDefs": [
                { "orderable": false, "targets": -1 } // Disable sorting on action column
            ]
        });
        // Filter tanggal (Tanggal Terima = kolom index 2)
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'suratMasukTable') return true;
            var dari = $('#filterDari').val();
            var sampai = $('#filterSampai').val();
            if (!dari && !sampai) return true;
            var row = $(tableSuratMasuk.row(dataIndex).node());
            var dateVal = row.find('td:eq(2)').attr('data-date');
            if (!dateVal) return false;
            if (dari && dateVal < dari) return false;
            if (sampai && dateVal > sampai) return false;
            return true;
        });
        $('#btnFilterTanggal').on('click', function () { tableSuratMasuk.draw(); });
        $('#btnResetTanggal').on('click', function () {
            $('#filterDari').val('');
            $('#filterSampai').val('');
            tableSuratMasuk.draw();
        });
    }
});

// Custom Delete Confirmation Modal
function showDeleteConfirm(message, onConfirm) {
    const modal = document.createElement('div');
    modal.className = 'custom-confirm-overlay';
    modal.innerHTML = `
        <div class="custom-confirm-modal">
            <div class="custom-confirm-icon delete-icon">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h3 class="custom-confirm-title">Konfirmasi Hapus</h3>
            <p class="custom-confirm-message">${message}</p>
            <div class="custom-confirm-buttons">
                <button class="custom-btn custom-btn-cancel" onclick="this.closest('.custom-confirm-overlay').remove()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button class="custom-btn custom-btn-delete" id="confirmDeleteBtn">
                    <i class="fas fa-trash"></i> Ya, Hapus
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    // Add styles if not exists
    if (!document.getElementById('customConfirmStyles')) {
        const style = document.createElement('style');
        style.id = 'customConfirmStyles';
        style.textContent = `
            .custom-confirm-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.6);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10000;
                animation: fadeIn 0.3s ease;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            .custom-confirm-modal {
                background: white;
                border-radius: 12px;
                padding: 30px;
                max-width: 400px;
                width: 90%;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
                animation: slideUp 0.3s ease;
            }
            
            @keyframes slideUp {
                from { 
                    transform: translateY(50px);
                    opacity: 0;
                }
                to { 
                    transform: translateY(0);
                    opacity: 1;
                }
            }
            
            .custom-confirm-icon {
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                color: white;
                font-size: 24px;
            }
            
            .delete-icon {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            }
            
            .custom-confirm-title {
                text-align: center;
                color: #333;
                font-size: 20px;
                font-weight: 600;
                margin-bottom: 10px;
            }
            
            .custom-confirm-message {
                text-align: center;
                color: #666;
                margin-bottom: 25px;
                line-height: 1.5;
            }
            
            .custom-confirm-buttons {
                display: flex;
                gap: 10px;
                justify-content: center;
            }
            
            .custom-btn {
                padding: 12px 24px;
                border: none;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            
            .custom-btn-cancel {
                background: #f1f3f5;
                color: #495057;
            }
            
            .custom-btn-cancel:hover {
                background: #e9ecef;
                transform: translateY(-2px);
            }
            
            .custom-btn-delete {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                color: white;
            }
            
            .custom-btn-delete:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(245, 87, 108, 0.4);
            }
        `;
        document.head.appendChild(style);
    }

    // Handle confirm button
    setTimeout(() => {
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function () {
                modal.remove();
                onConfirm();
            });
        }
    }, 100);

    // Close on overlay click
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Delete Confirmation
function confirmDelete(id) {
    showDeleteConfirm('Apakah Anda yakin ingin menghapus surat ini? Data yang terhapus tidak dapat dikembalikan!', function () {
        window.location.href = 'proses-surat-masuk.php?action=delete&id=' + id;
    });
}

// File Upload Preview
if (document.getElementById('file_surat')) {
    document.getElementById('file_surat').addEventListener('change', function (e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file PDF (Maksimal 10MB)';
        const fileNameSpan = document.getElementById('file-name');

        if (fileNameSpan) {
            fileNameSpan.textContent = fileName;
        }

        // Validasi ukuran file
        if (e.target.files[0] && e.target.files[0].size > 10485760) {
            alert('Ukuran file terlalu besar! Maksimal 10MB');
            e.target.value = '';
            if (fileNameSpan) {
                fileNameSpan.textContent = 'Pilih file PDF (Maksimal 10MB)';
            }
        }

        // Validasi ekstensi file
        if (e.target.files[0]) {
            const fileExt = e.target.files[0].name.split('.').pop().toLowerCase();
            if (fileExt !== 'pdf') {
                alert('Hanya file PDF yang diperbolehkan!');
                e.target.value = '';
                if (fileNameSpan) {
                    fileNameSpan.textContent = 'Pilih file PDF (Maksimal 10MB)';
                }
            }
        }
    });
}

// Form Validation for Tambah/Edit Surat Masuk
if (document.getElementById('formSuratMasuk')) {
    document.getElementById('formSuratMasuk').addEventListener('submit', function (e) {
        const perihal = document.getElementById('perihal').value.trim();
        const alamatPengirim = document.getElementById('alamat_pengirim').value.trim();
        const nomorSurat = document.getElementById('nomor_surat').value.trim();
        const tanggalTerima = document.getElementById('tanggal_terima').value;
        const tanggalSurat = document.getElementById('tanggal_surat').value;

        if (perihal === '' || alamatPengirim === '' || nomorSurat === '' || tanggalTerima === '' || tanggalSurat === '') {
            e.preventDefault();
            alert('Semua field yang bertanda * wajib diisi!');
            return false;
        }

        return true;
    });
}

// Form Validation for Disposisi
if (document.getElementById('formDisposisi')) {
    document.getElementById('formDisposisi').addEventListener('submit', function (e) {
        const tujuanChecked = document.querySelectorAll('input[name="tujuan_disposisi[]"]:checked').length;

        if (tujuanChecked === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 tujuan disposisi!');
            return false;
        }

        return true;
    });
}

// Auto-set tanggal terima ke hari ini
if (document.getElementById('tanggal_terima') && document.getElementById('tanggal_terima').value === '') {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_terima').value = today;
}

// Checkbox Select All (Optional Feature)
function addSelectAllCheckbox(groupName, selectAllId) {
    const checkboxes = document.querySelectorAll(`input[name="${groupName}"]`);
    const selectAll = document.getElementById(selectAllId);

    if (selectAll && checkboxes.length > 0) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                selectAll.checked = allChecked;
            });
        });
    }
}

// Format tanggal Indonesia
function formatTanggalIndonesia(dateString) {
    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    const date = new Date(dateString);
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear();

    return `${day} ${month} ${year}`;
}

// Tooltip for action buttons (optional)
document.addEventListener('DOMContentLoaded', function () {
    const actionButtons = document.querySelectorAll('.btn-action');
    actionButtons.forEach(btn => {
        btn.addEventListener('mouseenter', function () {
            const title = this.getAttribute('title');
            if (title) {
                this.setAttribute('data-tooltip', title);
            }
        });
    });
});

// Print function for specific element
function printElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        const printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Print</title>');
        printWindow.document.write('<link rel="stylesheet" href="../css/dashboard.css">');
        printWindow.document.write('<link rel="stylesheet" href="../css/surat-masuk.css">');
        printWindow.document.write('</head><body>');
        printWindow.document.write(element.innerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    }
}

// Enhanced search with highlights (optional)
function highlightSearchResults(searchTerm) {
    const tableBody = document.getElementById('tableBody');
    if (!tableBody) return;

    const rows = tableBody.getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let rowText = '';

        for (let j = 0; j < cells.length - 1; j++) { // Exclude action column
            rowText += cells[j].textContent.toLowerCase() + ' ';
        }

        if (searchTerm === '' || rowText.includes(searchTerm.toLowerCase())) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}

// Status badge color animation
document.addEventListener('DOMContentLoaded', function () {
    const badges = document.querySelectorAll('.badge');
    badges.forEach((badge, index) => {
        setTimeout(() => {
            badge.style.opacity = '0';
            badge.style.transform = 'scale(0.8)';
            setTimeout(() => {
                badge.style.transition = 'all 0.3s ease';
                badge.style.opacity = '1';
                badge.style.transform = 'scale(1)';
            }, 50);
        }, index * 50);
    });
});

// Auto-hide alerts (if using custom alerts)
function showAutoHideAlert(message, type = 'success', duration = 3000) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `auto-alert alert-${type}`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 300);
    }, duration);
}

console.log('Surat Masuk JS loaded successfully');