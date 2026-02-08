// ========================================
// SURAT MASUK - JAVASCRIPT
// ========================================

$(document).ready(function () {
    // Initialize DataTables
    var table = $('#suratMasukTable').DataTable({
        "scrollX": true,
        "autoWidth": false, // Biarkan CSS mengatur lebar
        "dom": 'Bfrtip',
        "buttons": [{
            extend: 'excel',
            text: '<i class="fas fa-file-excel"></i> Excel',
            className: 'dt-button',
            exportOptions: {
                columns: ':not(.no-export)'
            }
        },
        {
            extend: 'pdf',
            text: '<i class="fas fa-file-pdf"></i> PDF',
            className: 'dt-button',
            exportOptions: {
                columns: ':not(.no-export)'
            }
        }
        ],
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "zeroRecords": "Data tidak ditemukan",
            "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
            "infoEmpty": "Tidak ada data yang tersedia",
            "infoFiltered": "(difilter dari _MAX_ total data)",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        },
        "pageLength": 10,
        "order": [
            [0, 'asc']
        ]
    });

    // Custom Filtering Function
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        if (settings.nTable.id !== 'suratMasukTable') return true;

        // Status Filter (Column index 7)
        var statusFilter = $('#filterStatus').val();
        var statusData = data[7]; // Index 7 is Status Disposisi

        if (statusFilter && !statusData.includes(statusFilter)) {
            return false;
        }

        // Date Filter (Column index 2 - Tanggal Terima)
        var dari = $('#filterDari').val();
        var sampai = $('#filterSampai').val();

        // Get data-date attribute from the cell
        var row = $(table.row(dataIndex).node());
        var dateVal = row.find('td:eq(2)').attr('data-date');

        if (!dari && !sampai) return true;
        if (!dateVal) return false;
        if (dari && dateVal < dari) return false;
        if (sampai && dateVal > sampai) return false;

        return true;
    });

    // Event Listeners for Filters
    $('#btnFilter').on('click', function () {
        table.draw();
    });

    $('#btnReset').on('click', function () {
        $('#filterStatus').val('');
        $('#filterDari').val('');
        $('#filterSampai').val('');
        table.draw();
    });
});

// Delete confirmation using SweetAlert2
function confirmDelete(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data surat masuk ini akan dihapus permanen beserta filenya!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'proses-surat-masuk.php?action=delete&id=' + id;
        }
    });
}