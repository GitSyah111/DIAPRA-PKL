// ========================================
// SURAT MASUK - JAVASCRIPT
// ========================================

$(document).ready(function () {
    // Initialize DataTables
    var table = $('#suratMasukTable').DataTable({
        responsive: true,
        // "scrollX": true,
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
            orientation: 'landscape',
            pageSize: 'A4',
            download: 'open',
            title: 'Laporan Surat Masuk',
            exportOptions: {
                columns: ':not(.no-export)'
            },
            customize: function (doc) {
                // Styling
                doc.defaultStyle.fontSize = 10;
                doc.styles.tableHeader.fontSize = 11;
                doc.styles.tableHeader.alignment = 'center';
                doc.styles.tableHeader.fillColor = '#3b82f6'; // Blue header
                doc.styles.tableHeader.color = '#ffffff';

                doc.styles.tableBodyOdd.alignment = 'center';
                doc.styles.tableBodyEven.alignment = 'center';

                // Layout columns & Borders
                var tableNode;
                for (var i = 0; i < doc.content.length; i++) {
                    if (doc.content[i].table) {
                        tableNode = doc.content[i];
                        break;
                    }
                }

                if (tableNode) {
                    // Widen table columns - give priority to Alamat (3) and Perihal (6)
                    // Indices: 0: No, 1: Agenda, 2: Tgl Terima, 3: Alamat, 4: Tgl Surat, 5: No Surat, 6: Perihal
                    tableNode.table.widths = ['5%', '8%', '12%', '*', '12%', '15%', '*'];

                    // Add all borders
                    tableNode.layout = {
                        hLineWidth: function (i, node) { return 1; },
                        vLineWidth: function (i, node) { return 1; },
                        hLineColor: function (i, node) { return '#d1d5db'; },
                        vLineColor: function (i, node) { return '#d1d5db'; },
                        paddingLeft: function (i, node) { return 8; },
                        paddingRight: function (i, node) { return 8; },
                        paddingTop: function (i, node) { return 8; },
                        paddingBottom: function (i, node) { return 8; }
                    };
                }

                // Add Filter Period or Auto Date Range
                var dari = $('#filterDari').val();
                var sampai = $('#filterSampai').val();
                var periodeText = '';
                function fmt(d) { return d.split('-').reverse().join('/'); }

                if (dari && sampai) {
                    periodeText = 'Periode: ' + fmt(dari) + ' s/d ' + fmt(sampai);
                } else if (dari) {
                    periodeText = 'Periode: Dari ' + fmt(dari);
                } else if (sampai) {
                    periodeText = 'Periode: Sampai ' + fmt(sampai);
                } else {
                    // Auto-calculate from visible data if no filter
                    var dates = [];
                    // Column 2 is Tanggal Terima
                    table.column(2, { search: 'applied' }).data().each(function (val) {
                        // Cleanup HTML if any, though usually pure text in data()
                        var dateStr = val.replace(/<[^>]+>/g, '').trim();
                        if (dateStr) dates.push(dateStr);
                    });

                    if (dates.length > 0) {
                        dates.sort(function (a, b) {
                            var da = a.split('/').reverse().join('-');
                            var db = b.split('/').reverse().join('-');
                            return da < db ? -1 : (da > db ? 1 : 0);
                        });
                        // Use first and last date from the sorted list
                        var minDate = dates[0];
                        var maxDate = dates[dates.length - 1];
                        periodeText = 'Arsip dari tanggal ' + minDate + ' sampai tanggal ' + maxDate;
                    }
                }

                if (periodeText) {
                    doc.content.splice(1, 0, {
                        text: periodeText,
                        alignment: 'center',
                        margin: [0, 0, 0, 15],
                        fontSize: 11,
                        italics: true
                    });
                }

                // Add Total Count
                var rowCount = tableNode ? (tableNode.table.body.length - 1) : 0;
                doc.content.push({
                    text: 'Total Arsip: ' + rowCount,
                    margin: [0, 20, 0, 0],
                    fontSize: 11,
                    bold: true
                });
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