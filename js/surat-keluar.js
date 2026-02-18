// DataTables Initialization
$(document).ready(function () {
    if ($('#suratKeluarTable').length) {
        var tableSuratKeluar = $('#suratKeluarTable').DataTable({
            // "scrollX": true,
            "autoWidth": false, // Biarkan CSS mengatur lebar
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn-datatable',
                    title: 'Data Surat Keluar',
                    exportOptions: {
                        columns: ':not(.no-export)'
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn-datatable',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    download: 'open',
                    title: 'Laporan Surat Keluar',
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
                            // Widen table columns - give priority to Tujuan (3) and Perihal (5)
                            // Indices: 0: No, 1: Agenda, 2: No Surat, 3: Tujuan, 4: Tgl Surat, 5: Perihal
                            tableNode.table.widths = ['5%', '8%', '15%', '20%', '12%', '*'];

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
                            // Column 4 is Tanggal Surat in Surat Keluar
                            tableSuratKeluar.column(4, { search: 'applied' }).data().each(function (val) {
                                // Cleanup HTML if any
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
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
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
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            order: [[0, 'desc']],
            responsive: true,
            columnDefs: [
                { orderable: false, targets: -1 } // Kolom aksi tidak bisa di-sort
            ]
        });
        // Filter tanggal (Tanggal Surat = kolom index 4)
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'suratKeluarTable') return true;
            var dari = $('#filterDari').val();
            var sampai = $('#filterSampai').val();
            if (!dari && !sampai) return true;
            var row = $(tableSuratKeluar.row(dataIndex).node());
            var dateVal = row.find('td:eq(4)').attr('data-date');
            if (!dateVal) return false;
            if (dari && dateVal < dari) return false;
            if (sampai && dateVal > sampai) return false;
            return true;
        });
        $('#btnFilterTanggal').on('click', function () { tableSuratKeluar.draw(); });
        $('#btnResetTanggal').on('click', function () {
            $('#filterDari').val('');
            $('#filterSampai').val('');
            tableSuratKeluar.draw();
        });
    }
});

// Konfirmasi hapus dengan SweetAlert2
function confirmDelete(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data surat keluar ini akan dihapus permanen beserta filenya!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Gunakan metode POST sesuai kode asli
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'proses-surat-keluar.php';

            const inputAction = document.createElement('input');
            inputAction.type = 'hidden';
            inputAction.name = 'action';
            inputAction.value = 'hapus';

            const inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'id';
            inputId.value = id;

            form.appendChild(inputAction);
            form.appendChild(inputId);
            document.body.appendChild(form);
            form.submit();
        }
    });
}