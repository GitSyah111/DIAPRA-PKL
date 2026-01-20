// DataTables Initialization
$(document).ready(function() {
    $('#suratKeluarTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copy',
                className: 'btn-datatable',
                exportOptions: {
                    columns: ':not(.no-export)'
                }
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn-datatable',
                exportOptions: {
                    columns: ':not(.no-export)'
                }
            },
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
                title: 'Data Surat Keluar',
                exportOptions: {
                    columns: ':not(.no-export)'
                },
                customize: function(doc) {
                    doc.content[1].table.widths = 
                        Array(doc.content[1].table.body[0].length).fill('*');
                    doc.styles.tableHeader = {
                        fillColor: [44, 74, 109],
                        color: [255, 255, 255],
                        bold: true
                    };
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn-datatable',
                title: 'Data Surat Keluar',
                exportOptions: {
                    columns: ':not(.no-export)'
                },
                customize: function(win) {
                    $(win.document.body)
                        .css('font-size', '10pt')
                        .prepend(
                            '<div style="text-align:center; margin-bottom: 20px;">' +
                            '<h2>DPPKBPM</h2>' +
                            '<h3>Dinas Pengendalian Penduduk dan Keluarga Berencana Pemberdayaan Masyarakat</h3>' +
                            '<h4>Data Surat Keluar</h4>' +
                            '</div>'
                        );

                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
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
});

// Konfirmasi hapus
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus surat keluar ini?')) {
        // Buat form untuk submit
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
}