window.isMobile = false;
let session = false;
var token = $('meta[name="csrf-token"]').attr('content');
var mobileCheck = function () {
    var check = /Android|webOS|iPhone|iPad|Mac|Macintosh|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    return check && (screen.width < 992);
};
var imprimirTicketReparacion = function (d) {
    var r = d.data;
    $.ajax({
        type: "POST",
        url: "http://localhost/tickets/example/ticket_reparacion.php",
        contentType: "application/json",
        data: JSON.stringify({
            "cliente": r.cliente,
            "folio": r.folio,
            "marca": r.marca,
            "modelo": r.modelo,
            "fecha_entrega": r.fecha_entrega,
            "costo": r.costo == -1 ? "Pendiente" : ("$" + parseInt(r.costo).toFixed(2)),
            "abono": r.abono == -1 ? "Ninguno" : ("$" + parseInt(r.abono).toFixed(2))
        }),
        success: function (res) {
            location.href = "/reparaciones";
        },
        error: function () {
            alerta("No se pudo imprimir el ticket.", "info");
            setTimeout(function () {
                location.href = "/reparaciones";
            }, 500);
        }
    });
}
var imprimirTicketVenta = function (params) {
    $.ajax({
        type: "GET",
        url: "/ventas/" + params.id + "/json",
        success: function (resp) {
            var venta = resp;
            var pr = [];
            for (var t = 0; t < venta.productos.length; t++) {
                pr.push({
                    "nombre": venta.productos[t].nombre,
                    "importe": "$" + (venta.productos[t].pivot.venta * venta.productos[t].pivot.cantidad).toFixed(0),
                    "cantidad": venta.productos[t].pivot.cantidad
                });
            }
            $.ajax({
                type: "POST",
                url: "http://localhost/tickets/ticket.php",
                contentType: "application/json",
                data: JSON.stringify({
                    "articulos": pr,
                    "total": "$" + parseFloat(venta.total).toFixed(0),
                    "denominacion": "$" + parseFloat(venta.denominacion).toFixed(0),
                    "cambio": "$" + (parseFloat(venta.denominacion) - parseFloat(venta.total)).toFixed(0),
                    "metodoPago": venta.metodo_pago,
                    "folio": venta.id,
                    "usuario": venta.usuario.name
                }),
                success: function () {
                    alerta("Listo, <b>no olvides dar ticket al cliente</b><br>Cambio: $" + (parseFloat(venta.cambio)).toFixed(2), "success");
                },
                error: function () {
                    alerta("No se pudo imprimir el ticket", "info");
                },
                complete: function () {
                    productos = [];
                    recargarCuenta();
                    ocultarModal();
                    // if (params.fromPtv !== false)
                    //     logout();
                }
            });
        }
    });
}
var imprimirTicketGarantia = function (params) {
    $.ajax({
        type: "GET",
        url: "/garantias/" + params.id + "/json",
        success: function (resp) {
            var garantia = resp.garantia;
            var pr = [];
            for (var t = 0; t < garantia.productos_nuevos.length; t++) {
                var prodNuevo = garantia.productos_nuevos[t];
                pr.push({
                    "nombre": prodNuevo.nombre,
                    "importe": "$" + (prodNuevo.venta * prodNuevo.pivot.cantidad).toFixed(0),
                    "cantidad": prodNuevo.pivot.cantidad
                });
            }
            $.ajax({
                type: "POST",
                url: "http://localhost/tickets/ticketGarantia.php",
                contentType: "application/json",
                data: JSON.stringify({
                    "nuevos": pr,
                    "producto": {
                        "nombre": garantia.producto_devuelto.nombre,
                        "importe": "$" + garantia.producto_devuelto.venta
                    },
                    "tipo": garantia.tipo,
                    "venta": garantia.venta_id,
                    "folio": garantia.id,
                    "diferencia": garantia.diferencia,
                    "usuario": garantia.usuario.name
                }),
                success: function () {
                    alerta("Impresión correcta.", "success");
                },
                error: function () {
                    alerta("No se pudo imprimir el ticket", "info");
                }
            });
        }
    });
}
var imprimirTicketCorte = function (params) {
    $.ajax({
        type: "POST",
        url: "http://localhost/tickets/ticketCorte.php",
        contentType: "application/json",
        data: JSON.stringify(params),
        success: function () {
            alerta("Impresión correcta.", "success");
        },
        error: function () {
            alerta("No se pudo imprimir el ticket", "info");
        }
    });
}
var imprimirEtiqueta = function (params) {

    $.ajax({
        type: "POST",
        url: "http://localhost/tickets/etiqueta.php",
        contentType: "application/json",
        data: JSON.stringify({
            "codigo": params.id,
            "nombre": params.nombre,
            "cantidad": params.cantidad,
            "precio": params.precio
        }),
        success: function () {
            console.log("Ok");
        },
        error: function () {
            alerta("No se pudieron imprimir las etiquetas", "info");
        },
        complete: function () {
            ocultarModal();
        }
    });
}
var alerta = function (mensaje, tipo = "info") {
    $(".js-generated").fadeOut(function () {
        $(this).remove();
    });
    var alert = '<div class="js-generated notificacion alert alert-' + tipo + ' alert-dismissible" role="alert">' +
        '<span class="mensaje">' + mensaje + '</span>' +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span>' +
        '</button>' +
        '</div>';
    $("body").append(alert);
    $(".js-generated").fadeIn(1000, function () {
        // setTimeout(function () {
        //     $(".js-generated").fadeOut();
        // }, 5000);
    });
}
var ocultarOtros = function () {
    // Search for any elements
    $(".inline").each(function (i, ele) {
        var input = $(ele);
        var tagName = input.prop('tagName');
        // Set the old value, any changes have been saved
        var val;
        if (tagName.toLowerCase() == 'select') {
            val = input.find('option:selected').text();
        } else {
            val = input.val();
        }

        input.parent().text(val);
        input.parent().html("");
    });
}

var replaceWithInput = function (el, callback = undefined) {
    // Get product Id
    var id = el.parent().attr("data-id");
    if (el.has('.form-control.inline').length)
        return;
    // Get value based on attr
    var value = el.text();
    // Get List of classes via native JavaScript
    var classes = el[0].classList;
    // Get attr, compra, venta, stock or nombre
    var attr = classes[0].split("-")[1];
    // Get type class
    var type = classes[1]?.split("-")[1] ?? 'text';
    // Hide any others
    ocultarOtros();
    // Set text empty
    el.text("");
    var input;
    if (type == 'select') {
        input = $(`<select class='form-control inline' data-id='${id}'>
                        ${categoriasOptions}
                   </select>`);
    } else {
        input = $(`<input type='${type}' class='form-control inline' data-id='${id}' value='${value}'/>`);
    }
    // Create input

    // Set focus and cursor at the end
    setTimeout(function () {
        input.trigger("focus");
        if (type == 'text')
            input[0].selectionStart = input[0].value.length;
    }, 0);
    // Add to DOM
    el.html(input);
    if (type == 'select') {
        $('select.inline[data-id="' + id + '"] option:contains("' + value + '")')
            .prop('selected', true);
        $('select.inline[data-id="' + id + '"] option:contains("TODAS")').remove();

        input.off('change').on('change', () => {
            var nuevoValue = input.val();
            var nuevoValueText = input.find('option:selected').text();
            patchItem(id, attr, nuevoValue, (r) => {
                if (r.estado) {
                    // Set with new value
                    input.parent().text(nuevoValueText);
                    input.parent().html("");
                } else {
                    // Set with old value and show alert
                    alerta(`No se pudo modificar el valor de ${attr}`, "danger");
                    input.parent().text(value);
                    input.parent().html("");

                }
            })
        });
    } else {
        // On Keydown, Enter and Esc control
        input.off("keydown").on("keydown", function (e) {
            if (e.which == 13) {
                var nuevoValue = input.val();
                if (id !== undefined) {
                    patchItem(id, attr, nuevoValue, (r) => {
                        if (r.estado) {
                            // Set with new value
                            input.parent().text(nuevoValue);
                            input.parent().html("");
                        } else {
                            // Set with old value and show alert
                            alerta(`No se pudo modificar el valor de ${attr}`, "danger");
                            input.parent().text(value);
                            input.parent().html("");

                        }
                    })
                } else
                    callback(el, nuevoValue);
            }
            // ESC
            if (e.which == 27) {
                // Set with old value, any changes have been saved
                input.parent().text(value);
                input.parent().html("");
            }
        });
    }
}

var patchItem = (id, attr, nuevoValue, callback) => {
    $.ajax({
        type: "PATCH",
        url: $("#ruta-editar-producto").val() + `/${id}`,
        data: {
            [attr]: nuevoValue
        },
        success: function (r) {
            callback(r);
        }
    });
}

var loadTooltips = function () {
    $("[data-toggle='tooltip']").tooltip();
}
var logout = function () {
    $.ajax({
        type: "POST",
        url: "/logout",
        complete: function () {
            localStorage.removeItem('_t');
            location.reload();
        }
    });
}
$(function () {
    bootbox.setDefaults({ locale: "es" });
    // Se agrega CSRF token al tag meta
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': token
        }
    });
    // Se activan los tooltips
    loadTooltips();
    // Campos que sólo deben admitir números
    $(document).on("keypress", ".onlynumbers", function (e) {
        if ($(this).attr("data-length") != undefined) {
            if ($(this).val().length >= $(this).attr("data-length"))
                return false;
        }
        return !((e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57));
    });
    // Revisar si estamos en mobile
    window.isMobile = mobileCheck();
    // Se muestran las notificaciones que haya
    $('.alert.notificacion').fadeIn(1000, function () {
        setTimeout(function () {
            $(".alert.notificacion").fadeOut();
        }, 5000);
    });
    if ($.fn.dataTable)
        $.extend(true, $.fn.dataTable.defaults, {
            language: es,
            scrollY: isMobile ? undefined : "50vh",
            scrollCollapse: false,
            order: [[0, "desc"]]
        });
    // CORTE
    $(document).on("click", ".btnCerrarCaja", function (e) {
        e.preventDefault();
        var href = $(this).attr("href");
        bootbox.confirm({
            message: "¿Seguro que desea cerrar la caja?",
            locale: "es",
            buttons: {
                confirm: {
                    className: 'btn btn-primary btn-ok'
                }
            },
            callback: function (result) {
                if (result)
                    location.href = href;
            }
        });
    });
    // CAMBIAR PERFIL
    $(document).on("click", ".panel-overflow .item-usuario", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        abrirModal(`/usuarios/cambiar/perfil/${id}`, "GET", "sm", false, "", function (modal) {
            setTimeout(function () {
                modal.find("input[name='password']").focus();
            }, 500);
        });
    });
    session = $("#session").val() == 'yes';
    if (!session) {
        // Show modal to login
        abrirModal('/login', 'GET', 'md', false, '', (modal) => {
            modal.find('[data-dismiss="modal"]').addClass('d-none');
            $("#isAdmin").on("click", function () {
                const val = $(this).prop("checked");
                if (val) {
                    $("#passwordBox").removeClass("d-none");
                } else {
                    $("#passwordBox").addClass("d-none");
                }
            });
        });
    }
});

var refresh = () => {
    location.reload();
};
