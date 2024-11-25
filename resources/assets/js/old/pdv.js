var productos = [];
var descuentos = [];
var descuento = {};
var total = 0;
var busquedaTime;
var cobrando = false;

var recalcularDescuento = function (modalDescuento) {
    var originalInput = modalDescuento.find("input[name='total']");
    var prod = JSON.parse(decodeURIComponent(modalDescuento.find("input[name='producto']").val()));
    var tipo = modalDescuento.find(`select[name='tipo_descuento']`).val();
    var de = parseFloat(modalDescuento.find("input[name='descuento_valor']:visible").val());
    var motivo = modalDescuento.find("textarea[name='motivo']").val();
    de = isNaN(de) ? 0 : de;
    var n = 0;
    if (tipo == "monto") {
        n = parseFloat(prod.venta - de).toFixed(2);
    } else {
        n = parseFloat(Math.ceil(prod.venta * (1 - (de / 100)))).toFixed(2);
    }
    var esMenor = n < parseFloat(prod.compra);
    modalDescuento.find(".btn-realizar-descuento").attr("disabled", esMenor);
    if (esMenor) {
        alerta("No se puede hacer un descuento tan alto<br>Corrija para continuar", "warning");
    } else {
        $(".js-generated").fadeOut();
    }
    originalInput.val(n);
    // Se forma el obj descuento
    descuento = {
        "id": prod.id,
        "motivo": motivo,
        "descuento": de,
        "nuevo": n,
        "tipo": tipo
    };
}

var recargarCuenta = function () {
    $(".cuenta").html("");
    $("#busqueda").val("").trigger("focus");
    total = 0;
    for (var t = productos.length - 1; t >= 0; t--) {
        var tieneDescuento = descuentos.find(c => c.id == productos[t].id);
        var desc = "";
        if (tieneDescuento !== undefined) {
            if (tieneDescuento.tipo == "monto") {
                desc = `<span class='descuento'>
                            -$${tieneDescuento.descuento}
                        </span>`;
            } else {
                desc = `<span class='descuento'>
                            -${tieneDescuento.descuento}%
                        </span>`;
            }
        }
        $(".cuenta").append("<div class='row item'>" +
            `<div class='col-1 text-center'><i class='material-icons producto-eliminar' data-id='${productos[t].id}'>close</i></div>
            <div class='col-md-5 col-11 text-center text-md-left'>${productos[t].nombre}<br>
                <sub>${productos[t].descripcion}</sub>
            </div>
            <div class='col-md-2 col-4 producto-venta' data-id='${productos[t].id}'>
                $${productos[t].venta}
                ${desc}
            </div>
            <div class='col-md-2 col-4 producto-cantidad' data-id='${productos[t].id}'>${productos[t].cantidad}</div>
            <div class='col-md-2 col-4 text-right'>$${(productos[t].venta * productos[t].cantidad).toFixed(2)}</div>
            </div><hr>`);
        total += parseFloat(productos[t].venta * productos[t].cantidad);
    }

    $('.total-cuenta').find('b:nth-child(2)').text("$" + total.toFixed(2));

    $(document).off("dblclick").on("dblclick", ".producto-cantidad", function (e) {
        e.preventDefault();
        var cantidad = $(this).text();
        var id = $(this).attr("data-id");
        $(this).text("");
        var input = "<input type='text' class='form-control cantidad-input onlynumbers' value='" + cantidad + "'></input>";
        $(this).html(input);
        $(document).off("keypress", ".cantidad-input").on("keypress", ".cantidad-input", function (e) {
            if (e.which == 13) {
                var newCantidad = parseInt($(this).val());
                if (newCantidad == 0)
                    return;
                var index = productos.findIndex(c => c.id == id);
                if (!hasStock(productos[index], newCantidad))
                    newCantidad = productos[index].cantidad;

                productos[index].cantidad = newCantidad;
                recargarCuenta();
                $("#busqueda").focus();
            }
        });
    });
    $(document).off("dblclick", ".producto-venta").on("dblclick", ".producto-venta", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        abrirModal($('#ruta-descuento').val() + `/${id}`, "GET", "md", false, "", function (modalDescuento) {
            var existing = descuentos.findIndex(c => c.id == id);
            // Habilitar campo de monto o porcentaje
            modalDescuento.find("select[name='tipo_descuento']").off("change").on("change", function () {
                var tipo = $(this).val();
                $(".descuento_tipo").addClass("d-none");
                $(`.descuento_${tipo}`).removeClass("d-none");

                recalcularDescuento(modalDescuento);
            });
            // Si ya existe el descuento previamente
            if (existing >= 0) {
                var desc = descuentos[existing];
                modalDescuento.find(`select[name='tipo_descuento'] option[value='${desc.tipo}']`).attr("selected", true);
                modalDescuento.find(`select[name='tipo_descuento']`).trigger("change");
                modalDescuento.find(`input[name='descuento_valor']`).val(desc.descuento);
                modalDescuento.find(`input[name='total']`).val(desc.nuevo);
                modalDescuento.find(`textarea[name='motivo']`).val(desc.motivo);
            }
            // Cuando se edita el valor del descuento
            modalDescuento.off("keyup", "input[name='descuento_valor']").on("keyup", "input[name='descuento_valor']", function (e) {
                recalcularDescuento(modalDescuento);
            });
            // Cuando cambia algo en el motivo también se recalcula todo
            modalDescuento.find("textarea[name='motivo']").off("change").on("change", function (e) {
                recalcularDescuento(modalDescuento);
            });
            // Click Realizar descuento
            modalDescuento.find(".btn-realizar-descuento").off("click").on("click", function (ev) {
                ev.preventDefault();
                ev.stopPropagation();
                if (Object.keys(descuento).length === 0) {
                    alerta("Complete el formulario para continuar", "danger");
                    return;
                }
                if (descuento.motivo == "" || descuento.descuento == 0) {
                    alerta("Complete el formulario para continuar", "danger");
                    return;
                }
                if (existing >= 0) {
                    if (descuento.descuento == 0)
                        descuentos.splice(existing, 1);
                    else
                        descuentos[existing] = descuento;
                } else {
                    descuentos.push(descuento);
                }
                modalDescuento.modal("hide");
                var index = productos.findIndex(c => c.id == id);
                productos[index].venta = descuento.nuevo;
                descuento = {};
                recargarCuenta();
                $("#busqueda").focus();
            });
        });
    });
    $(document).off("click", ".producto-eliminar").on("click", ".producto-eliminar", function () {
        var id = $(this).attr("data-id");
        var index = productos.findIndex(c => c.id == id);
        productos.splice(index, 1);
        recargarCuenta();
    });
}

const hasStock = (producto, cantidad = 0) => {
    const hasStockAvailable = (producto.stock - cantidad) >= 0;
    if (!hasStockAvailable)
        alerta("Producto sin stock disponible", "danger");
    return hasStockAvailable;
}

$(function () {
    $("body").on("keydown", function (e) {
        if (e.which == 121) { // F2
            e.preventDefault();
            $(".btn-cobrar").trigger("click");
            cobrando = true;
        } else if (e.which == 27) {// ESC
            $(".js-generated").fadeOut(function () {
                $(this).remove();
            });
        }
    });
    $("#busqueda").on("keydown", function (e) {
        // Si es la flecha arriba
        if (e.which == 38 || e.which == 13) {
            e.preventDefault();
            if ($(this).val() == "")
                return;
            buscarProducto($(this).val());
        }
    });
    $("#btnBuscar").on("click", function (e) {
        e.preventDefault();
        buscarProducto($("#busqueda").val());
    });
    var buscarProducto = function (b) {
        $.ajax({
            type: "POST",
            url: $("#ruta-buscar").val(),
            data: {
                "busqueda": b
            },
            success: function (res) {
                if (isMobile) {
                    var modal = $("#modal-general");
                    modal.find(".modal-content").html("").html(res);
                    modal.modal("show");
                    modal.off("hidden.bs.modal").on("hidden.bs.modal", function () {
                        $("#busqueda").focus();
                    });
                    modal.find(".tabla-productos .producto").on("click", function () {
                        agregarProducto(this);
                        modal.modal("hide");
                        $("#busqueda").val("");
                    });
                } else {
                    $("#resultados tbody").html(res);
                    if ($("#resultados tbody tr").length == 1) {
                        agregarProducto($("#resultados tbody tr").first());
                    }
                    $("#resultados tbody").find(".producto").on("click", function () {
                        agregarProducto(this);
                    });
                }
            }
        });
    }
    var agregarProducto = function (element) {
        var producto = JSON.parse($(element).attr("data-producto"));
        producto.cantidad = 1;
        var existe = productos.findIndex(c => c.id == producto.id);

        if (existe != -1) {
            if (hasStock(producto, productos[existe].cantidad + 1))
                productos[existe].cantidad += 1;
            else
                return;
        }
        else {
            if (hasStock(producto, producto.cantidad))
                productos.push(producto);
            else
                return;
        }
        recargarCuenta();

    }

    $(".btn-cobrar").off("click").on("click", function () {
        // Si no hay productos que cobrar
        if (productos.length == 0) {
            alerta("Debe ingresar al menos 1 producto", "danger");
            return;
        }
        if (cobrando) return;
        cobrando = true;
        abrirModal("/caja/cobro", "GET", "md", false, "", function (modalCobro) {
            modalCobro.off("hidden.bs.modal").on("hidden.bs.modal", function () {
                cobrando = false;
            });
            modalCobro.find(".total").text("$" + total.toFixed(2));
            modalCobro.find("input[name='total']").val(total.toFixed(2));
            for (var g = 0; g < productos.length; g++) {
                modalCobro.find("form").append("<input type='hidden' name='productos[" + g + "][cantidad]' value='" + productos[g].cantidad + "'>");
                modalCobro.find("form").append("<input type='hidden' name='productos[" + g + "][venta]' value='" + productos[g].venta + "'>");
                modalCobro.find("form").append("<input type='hidden' name='productos[" + g + "][id]' value='" + productos[g].id + "'>");
            }
            for (var g = 0; g < descuentos.length; g++) {
                modalCobro.find("form").append(`<input type='hidden' name='descuentos[${g}][motivo]' value='${descuentos[g].motivo}'>`);
                modalCobro.find("form").append(`<input type='hidden' name='descuentos[${g}][id]' value='${descuentos[g].id}'>`);
                modalCobro.find("form").append(`<input type='hidden' name='descuentos[${g}][descuento]' value='${descuentos[g].descuento}'>`);
                modalCobro.find("form").append(`<input type='hidden' name='descuentos[${g}][nuevo]' value='${descuentos[g].nuevo}'>`);
                modalCobro.find("form").append(`<input type='hidden' name='descuentos[${g}][tipo]' value='${descuentos[g].tipo}'>`);
            }
            modalCobro.modal("show");
            modalCobro.find(".se-recibe").val("");
            setTimeout(function () {
                modalCobro.find(".se-recibe").trigger("focus");
            }, 500);
            modalCobro.find(".cambio").text("$0.00");
            $(document).off("keyup", ".se-recibe").on("keyup", ".se-recibe", function (e) {
                if (e.which == 13)
                    $(".btn-realizar-cobro").trigger("click");
                var de = $(this).val();
                modalCobro.find(".cambio").text("$" + (de - total).toFixed(2));
            });
        });
    });

    $(".btn-imprimir-ultima").off("click").on("click", function () {
        $.ajax({
            type: "GET",
            url: $('#ruta-reimprimir-ultima-venta').val(),
            success: function (r) {
                var id = r;
                imprimirTicketVenta({ "id": id }, function () {
                    alerta("Se ha impreso el ticket", "success");
                }, function () {
                    alerta("No se pudo imprimir el ticket, consulte con soporte", "danger");
                });
            }
        })
    });

    $('.btn-imprimir-ultimo-corte').off('click').on('click', function () {
        // SI ES CIERRE
        $.ajax({
            type: "GET",
            url: $('#ruta-reimprimir-ultimo-corte').val(),
            success: function (cierre) {
                imprimirTicketCorte(cierre);
            },
            complete: function () {
                setTimeout(function () {
                    location.reload();
                }, 2000);
            }
        });
    });

    $(document).off("change", "select[name='metodo_pago']").on("change", "select[name='metodo_pago']", function () {
        if ($(this).val() !== "Efectivo") {
            $("#controles-efectivo").addClass("d-none");
        } else {
            $("#controles-efectivo").removeClass("d-none");
        }
    });
});
