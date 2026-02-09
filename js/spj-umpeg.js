// DataTables Initialization for SPJ UMPEG
$(document).ready(function () {
    if ($('#spjUmpegTable').length) {
        var tableSpj = $('#spjUmpegTable').DataTable({
            "scrollX": true,
            "autoWidth": false,
            dom: 'Bfrtip',
            buttons: [{
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
                title: 'Laporan SPJ UMPEG',
                exportOptions: {
                    columns: ':not(.no-export)'
                },
                customize: function (doc) {
                    // Styling
                    doc.defaultStyle.fontSize = 10;
                    doc.styles.tableHeader.fontSize = 11;
                    doc.styles.tableHeader.alignment = 'center';
                    doc.styles.tableBodyOdd.alignment = 'center';
                    doc.styles.tableBodyEven.alignment = 'center';

                    // Layout columns
                    var tableNode;
                    for (var i = 0; i < doc.content.length; i++) {
                        if (doc.content[i].table) {
                            tableNode = doc.content[i];
                            break;
                        }
                    }
                    if (tableNode) {
                        tableNode.table.widths = Array(tableNode.table.body[0].length).fill('*');
                    }

                    // Add Filter Period
                    var dari = $('#filterDari').val();
                    var sampai = $('#filterSampai').val();
                    var periodeText = '';
                    function fmt(d) { return d.split('-').reverse().join('/'); }

                    if (dari && sampai) periodeText = 'Periode: ' + fmt(dari) + ' s/d ' + fmt(sampai);
                    else if (dari) periodeText = 'Periode: Dari ' + fmt(dari);
                    else if (sampai) periodeText = 'Periode: Sampai ' + fmt(sampai);

                    if (periodeText) {
                        doc.content.splice(1, 0, {
                            text: periodeText,
                            alignment: 'center',
                            margin: [0, 0, 0, 10],
                            fontSize: 11
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
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            },
            pageLength: 10,
            order: [
                [0, 'asc']
            ]
        });
        // Filter tanggal (Tanggal = kolom index 3)
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'spjUmpegTable') return true;
            var dari = $('#filterDari').val();
            var sampai = $('#filterSampai').val();
            if (!dari && !sampai) return true;
            var row = $(tableSpj.row(dataIndex).node());
            var dateVal = row.find('td:eq(3)').attr('data-date');
            if (!dateVal) return false;
            if (dari && dateVal < dari) return false;
            if (sampai && dateVal > sampai) return false;
            return true;
        });
        $('#btnFilterTanggal').on('click', function () {
            tableSpj.draw();
        });
        $('#btnResetTanggal').on('click', function () {
            $('#filterDari').val('');
            $('#filterSampai').val('');
            tableSpj.draw();
        });
    }
});

// Delete confirmation with SweetAlert2
function confirmDelete(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data SPJ UMPEG ini akan dihapus permanen beserta filenya!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'proses-spj-umpeg.php?action=delete&id=' + id;
        }
    });
}
