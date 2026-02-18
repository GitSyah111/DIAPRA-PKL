// DataTables Initialization for SPJ UMPEG
$(document).ready(function () {
    if ($('#spjUmpegTable').length) {
        var tableSpj = $('#spjUmpegTable').DataTable({
            responsive: true,
            // "scrollX": true,
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
                download: 'open',
                title: 'Laporan SPJ UMPEG',
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
                        // Indices: 0: No, 1: No Agenda, 2: Pelaksana, 3: Tanggal, 4: Nama Kegiatan, 5: Kode Rek, 6: Jumlah
                        // Give more width to Nama Kegiatan (4)
                        tableNode.table.widths = ['5%', '10%', '15%', '12%', '*', '15%', '15%'];

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
                        // Column 3 is Tanggal
                        tableSpj.column(3, { search: 'applied' }).data().each(function (val) {
                            var dateStr = val.replace(/<[^>]+>/g, '').trim();
                            if (dateStr) dates.push(dateStr);
                        });

                        if (dates.length > 0) {
                            dates.sort(function (a, b) {
                                var da = a.split('/').reverse().join('-');
                                var db = b.split('/').reverse().join('-');
                                return da < db ? -1 : (da > db ? 1 : 0);
                            });
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
