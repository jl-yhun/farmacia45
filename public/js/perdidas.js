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
$(function () {
    var buscarProductos = function (ele) {
        if (ele.is("[readonly]")) return;
        var criteria = ele.val();
        abrirModal($('#ruta-productos').val() + `/${criteria}`, "GET", "xl", true, "productos");
    }

    $(document).off("on-lov-selection").on("on-lov-selection", function (e, data) {
        switch (data.tipo) {
            case "productos":
                var nuevoProducto = data.selected;
                $("input[name='producto_nombre']").val(nuevoProducto.nombre);
                $("input[name='producto_id']").val(nuevoProducto.id);
                break;
        }
    });
    $(document).off("keydown", "input[name='producto_nombre']")
        .on("keydown", "input[name='producto_nombre']", function (e) {
            if (e.code === "Enter") {
                e.preventDefault();
                buscarProductos($(this));
            }
            return true;
        });
    $(document).off("click", ".btn-search-producto").on("click", ".btn-search-producto", function (e) {
        e.preventDefault();
        buscarProductos($(this).parent().parent().find("input"));
    });
});