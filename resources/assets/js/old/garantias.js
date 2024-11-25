var nuevosProductos = [];
var montoFaltante = 0;
$(function () {
    var buscarVenta = function () {
        if ($("select[name='tipo']").val() == "" ||
            $("select[name='tipo']").val() == "CANCELACIÓN") return;
        var ventaId = $("input[name='venta_id']").val();
        abrirModal(`/garantia-venta/${ventaId}`, "GET", "lg", true, "venta");
    }
    var buscarProductos = function (ele) {
        if (ele.is("[readonly]")) return;
        if (montoFaltante <= 0) {
            alerta(`Ya se cubrió el monto del producto devuelto.`, "info");
            return;
        }
        var criteria = ele.val();
        var productoId = $("input[name='producto_id']").val();
        abrirModal(`/garantia-productos/${criteria}/${productoId}`, "GET", "lg", true, "productos");
    }
    var actualizarTablaProductos = function () {
        var tabla = $("#tabla_nuevos_productos");
        var montoOrig = parseFloat($("input[name='producto_monto']").val());
        tabla.find("tbody").html("");
        var subtotalNuevos = 0;
        for (var g = 0; g < nuevosProductos.length; g++) {
            subtotalNuevos += nuevosProductos[g].venta * nuevosProductos[g].cantidad;
            tabla.find("tbody").append(`<tr producto-id='${nuevosProductos[g].id}'>
                <input type='hidden' name='productos[${g}][id]' value='${nuevosProductos[g].id}'/>
                <td>${nuevosProductos[g].nombre}</td>
                <input type='hidden' name='productos[${g}][nombre]' value='${nuevosProductos[g].nombre}'/>
                <td class='nuevo_producto_cantidad'>${nuevosProductos[g].cantidad}</td>
                <input type='hidden' name='productos[${g}][cantidad]' value='${nuevosProductos[g].cantidad}'/>
                <td>$ ${nuevosProductos[g].venta}</td>
                <input type='hidden' name='productos[${g}][venta]' value='${nuevosProductos[g].venta}'/>
                <td>$ ${nuevosProductos[g].venta * nuevosProductos[g].cantidad}</td>
                <td>
                    <button type='button' data-toggle='tooltip' title='Eliminar' class='btn btn-danger btn-eliminar'>
                        <i class='fa fa-ban'></i>
                    </button>
                </td>
            </tr>`);
        }
        montoFaltante = montoOrig - subtotalNuevos;
        $("input[name='diferencia']").val(montoFaltante);
        if (subtotalNuevos > montoOrig) {
            alerta(`El cliente deberá pagar una diferencia de $ ${subtotalNuevos - montoOrig}`, "info");
        }
    }
    $(document).off("dblclick", ".nuevo_producto_cantidad").on("dblclick", ".nuevo_producto_cantidad", function (e) {
        var oldVal = parseInt($(this).text());
        replaceWithInput($(this), function (el, nuevo) {
            // Set with new value
            el.html("");
            el.text(oldVal);
            if (oldVal < nuevo && montoFaltante <= 0) {
                alerta(`Ya se cubrió el monto del producto devuelto.`, "info");
                return;
            }
            // Set with new value
            el.html("");
            el.text(nuevo);
            var id = el.parent().attr("producto-id");
            var index = nuevosProductos.findIndex(c => c.id == id);
            nuevosProductos[index].cantidad = nuevo;
            nuevosProductos[index].monto = nuevo * nuevosProductos[index].venta;
            actualizarTablaProductos();
        });
    });

    $(document).off("on-lov-selection").on("on-lov-selection", function (e, data) {
        switch (data.tipo) {
            case "venta":
                var venta = data.selected;
                $("input[name='producto_nombre']").val(venta.nombre);
                $("input[name='producto_id']").val(venta.id);
                $("input[name='producto_monto']").val(venta.pivot.venta);
                $("input[name='producto_admite']").val(venta.categoria.admite);
                montoFaltante = parseFloat(venta.pivot.venta);
                if (venta.categoria.admite == "NINGUNO") {
                    alerta(`Se necesitará <b>permisos de administrador</b> para 
                            procesar esta devolución ya que este producto <b>NO ADMITE</b> cambios ni devoluciones`, "warning");
                } else if (venta.categoria.admite !== $("select[name='tipo']").val()) {
                    alerta(`Este producto no admite <b>${$("select[name='tipo']").val()}.</b><br>
                            Seleccione ${venta.categoria.admite} para este producto.`, "warning");
                    return;
                }
                $("input[name='nuevo_producto']").removeAttr("readonly");
                break;
            case "productos":
                var nuevoProducto = data.selected;
                var index = nuevosProductos.findIndex(c => c.id == nuevoProducto.id);
                if (index == -1) {
                    nuevosProductos.push({
                        "id": nuevoProducto.id,
                        "nombre": nuevoProducto.nombre,
                        "venta": parseFloat(nuevoProducto.venta),
                        "cantidad": 1,
                        "monto": parseFloat(nuevoProducto.venta)
                    });
                } else
                    nuevosProductos[index].cantidad++;
                actualizarTablaProductos();
                break;
        }
    });
    $(document).off("keydown", "input[name='venta_id'], input[name='nuevo_producto']")
        .on("keydown", "input[name='venta_id'], input[name='nuevo_producto']", function (e) {
            if (e.code === "Enter") {
                e.preventDefault();
                switch (e.target.name) {
                    case "venta_id":
                        buscarVenta();
                        break;
                    case "nuevo_producto":
                        buscarProductos($(this));
                        break;
                }
            }
            return true;
        });
    $(document).off("change", "select[name='tipo']").on("change", "select[name='tipo']", function () {
        var val = $(this).val();
        $("#producto").removeClass("d-none");
        $("#producto_monto").removeClass("d-none");
        $("#cambios_garantias").removeClass("d-none");
        $("#folio_venta .input-group-append").removeClass("d-none");

        if (val == "DEVOLUCIÓN DE DINERO") {
            alerta(`Se necesitará <b>permisos de administrador</b> para 
            procesar esta devolución.`, "warning");
            $("#cambios_garantias").addClass("d-none");
        } else if (val == 'CANCELACIÓN') {
            $("#cambios_garantias").addClass("d-none");
            $("#producto").addClass("d-none");
            $("#producto_monto").addClass("d-none");
            $("#folio_venta .input-group-append").addClass("d-none");
        } else if (val == "GARANTÍA" || val == "CAMBIO") {
            var producto_admite = $("input[name='producto_admite']").val();
            if (producto_admite == val) {
                $("input[name='nuevo_producto']").removeAttr("readonly");
            } else if (producto_admite !== "") {
                alerta(`Este producto no admite <b>${val}.</b><br>
                            Seleccione ${producto_admite} para este producto.`, "warning");
                $("input[name='nuevo_producto']").attr("readonly", "readonly");
                return;
            }
        }
        if (val == "GARANTÍA")
            $("input[name='perdida']").val(1);
        else
            $("input[name='perdida']").val(0);

        $("input[name='venta_id']").removeAttr("readonly");
    });
    $(document).off("click", ".btn-eliminar").on("click", ".btn-eliminar", function (e) {
        e.preventDefault();
        var id = $(this).parent().parent().attr("producto-id");
        var index = nuevosProductos.findIndex(c => c.id == id);
        nuevosProductos.splice(index, 1);
        actualizarTablaProductos();
    });
    $(document).off("click", ".btn-search-venta").on("click", ".btn-search-venta", function (e) {
        e.preventDefault();
        buscarVenta();
    });
    $(document).off("click", ".btn-search-producto").on("click", ".btn-search-producto", function (e) {
        e.preventDefault();
        buscarProductos($(this).parent().parent().find("input"));
    });
    $(document).off("click", ".btn-imprimir").on("click", ".btn-imprimir", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        imprimirTicketGarantia({ "id": id });
    });
});