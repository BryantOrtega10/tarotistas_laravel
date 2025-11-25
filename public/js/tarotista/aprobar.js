$(document).ready(function () {
    $("body").on("click", "#btnAprobar", function (e) {
        $("#formAprobar").prop("action", $("#urlAprobar").val());
        $("#formAprobar").trigger("submit");
    });
    $("body").on("click", "#btnRechazar", function (e) {
        $("#formAprobar").prop("action", $("#urlRechazar").val());
        $("#formAprobar").trigger("submit");
    });
    

});
