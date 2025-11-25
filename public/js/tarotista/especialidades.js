$(document).ready(function () {
    $("body").on("click", ".agregar-especialidad", function (e) {
        const especialidadesJson = JSON.parse($("#especialidadesJson").val());
        const especialidadesOptions = especialidadesJson
            .map((item) => {
                return `<option value="${item.id}">${item.nombre}</option>`;
            })
            .join("");

        $(
            ".especialidades-cont"
        ).append(`<div class="row align-items-end especialidad-item">
                                    <div class="col-md-6 col-8">
                                        <div class="form-group">
                                            <label for="especialidad_${
                                                $(".especialidad-item").length
                                            }" class="lb-especialidad">Especialidad ${$(".especialidad-item").length + 1}:</label>
                                            <select id="especialidad_${
                                                $(".especialidad-item").length
                                            }" name="especialidad[]" class="form-control">
                                                ${especialidadesOptions}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-4">
                                        <button type="button" class="btn btn-outline-danger quitar-especialidad mb-3">Quitar</button>
                                    </div>
                                </div>`);
    });

    $("body").on("click", ".quitar-especialidad", function (e) {
        $(this).closest(".especialidad-item").remove();

        $(".especialidad-item").each(function (i, especialidadItem) {
            $(especialidadItem)
                .find(".lb-especialidad")
                .html(`Especialidad ${i + 1}`);
            $(especialidadItem).find(".lb-especialidad").prop("for", `especialidad_${i}`);
            $(especialidadItem).find(".form-control").prop("id", `especialidad_${i}`);
        });
    });
});