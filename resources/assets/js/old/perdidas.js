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