$(document).ready(function() {
    $('.datatable').DataTable({
        layout: {
            topStart: {
                buttons: [
                    'copy', 'excel', 'pdf'
                ]
            }
        }
    });
});
