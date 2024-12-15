$(document).ready(function () {


    const users = $('#users').DataTable({
        ajax: {
            url: "ajax/getUsers.php",
            dataType: "json",
            type: "POST"
        },
        dom: 'Blfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                },
                text: '<i class="bi bi-clipboard"></i> Copy',
                className: 'btn btn-primary'
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                },
                text: '<i class="bi bi-filetype-pdf"></i> Excell',
                className: 'btn btn-success'
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4],
                    orientation: 'landscape',
                    pageSize: 'LEGAL'
                },
                text: '<i class="bi bi-file-pdf"></i> PDF',
                className: 'btn btn-danger'
            }
        ],


    });
});