$(document).ready(function() {
    $("body").on("click", ".ask", function(e) {
        e.preventDefault();
        const link = $(this).attr("href");
        const data_message = $(this).attr("data-message");
        let data_color = $(this).attr("data-color");
        if(typeof data_color === undefined)
        {
            data_color = '#dc3545';
        }
        Swal.fire({
            title: `<b>${data_message}</b>`,
            type: 'warning',
            text: ` En verdad desea ${data_message.toLowerCase()}?`,
            showCloseButton: true,
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: data_color
        }).then((result) => {
            if (result.value) {
                const form = document.createElement("form");
                form.setAttribute("id", "autoSubmitForm");
                form.setAttribute("action", link);
                form.setAttribute("method", "POST");
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const token = document.createElement("input");
                token.setAttribute("type", "hidden");
                token.setAttribute("name", "_token");
                token.setAttribute("value", csrfToken);
                form.appendChild(token);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
function alertSwal(message){
    Swal.fire({
        title: `<b>${message}</b>`,
        type: 'info',
        text: `${message}`,
        showCloseButton: true,
        confirmButtonText: 'Aceptar'
    });
}

function errorSwal(title, message){
    Swal.fire({
        title: `<b>${title}</b>`,
        type: 'warning',
        text: `${message}`,
        showCloseButton: true,
        confirmButtonText: 'Aceptar'
    });
}