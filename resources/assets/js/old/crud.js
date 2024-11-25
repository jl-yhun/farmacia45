$(function () {
    try {
        $("#table").DataTable();
        $('#table_filter input').attr('data-cy', 'txt-busqueda')
        $('#table').attr('data-cy', 'tbl')
        $('#table_length select').attr('data-cy', 'select-length')
        $('#table_paginate').attr('data-cy', 'paginacion')
        $('#table_info').attr('data-cy', 'paginacion-info')

    } catch (e) {
        console.error(e);
    }
    $(document).off("click", ".btnEliminar").on("click", ".btnEliminar", function (e) {
        e.preventDefault();
        bootbox.confirm("¿Seguro que desea eliminar este registro?", function (result) {
            if (!result) return;
            var form;
            form = $(e.target).find("form");
            if (e.target.localName == "i") {
                form = $(e.target).parent().find("form");
            }

            form.trigger("submit");
        });
    });
    $("body").on("keydown", function (e) {
        if (e.key == "F4") {
            e.preventDefault();
            $("#btn-agregar").trigger("click");
        }
    });
});