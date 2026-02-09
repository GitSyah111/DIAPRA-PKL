// DataTables Initialization
$(document).ready(function () {
    if ($('#suratKeluarTable').length) {
        var tableSuratKeluar = $('#suratKeluarTable').DataTable({
            "scrollX": true,
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
                    title: 'Laporan Surat Keluar',
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
            responsive: false, // Matikan responsive DataTables, gunakan scrollX
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