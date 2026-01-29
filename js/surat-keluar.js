// DataTables Initialization
$(document).ready(function() {
    if ($('#suratKeluarTable').length) {
    var tableSuratKeluar = $('#suratKeluarTable').DataTable({
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
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
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
    $('#btnFilterTanggal').on('click', function() { tableSuratKeluar.draw(); });
    $('#btnResetTanggal').on('click', function() {
        $('#filterDari').val('');
        $('#filterSampai').val('');
        tableSuratKeluar.draw();
    });
    }
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