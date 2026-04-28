$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {

    $(".datatable").DataTable({
        pageLength: 50,
        layout: {
            topStart: {
                buttons: [
                    'copy', 'excel', 'pdf'
                ]
            }
        },
        scrollX: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: $(".datatable").data("url"), 
            type: "POST", 
        },
        columns: [
            { data: 'nombre'},
            { data: 'email'},
            { data: 'provider'},
            { data: 'estado'},
            { data: 'pais'},
            {
                data: 'accion',
                render: function (data, type, row) {
                    return `<a href="${data.href}" class="btn btn-outline-primary">${data.text}</a>`;
                },
            },
        ],
    });
});