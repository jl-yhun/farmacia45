$(".producto-compra,.producto-venta").on("dblclick", function () {
    var id = $(this).parent().attr("data-id");
    var tipo = $(this).attr("data-tipo");
    var precio_ = $(this).text();
    $(this).text("");
    $(this).html("<input type='number' class='form-control nuevo-precio' data-id='" + id + "' data-tipo='" + tipo + "' value='" + parseInt(precio_) + "'></input>");
    $(document).off("keydown", ".nuevo-precio").on("keydown", ".nuevo-precio", function (e) {
        var input = $(this);
        if (e.which == 13) {
            var id = input.attr("data-id");
            var tipo = input.attr("data-tipo");
            var precio = input.val();
            $.ajax({
                type: "PUT",
                url: $("#ruta-editar-precio").val(),
                data: {
                    id: id,
                    tipo: tipo,
                    precio: precio
                },
                success: function (r) {
                    if (r.estado) {
                        input.parent().text(parseFloat(precio).toFixed(2));
                        input.parent().html("");
                    } else {
                        alerta("No se pudo modificar el precio", "danger");
                        input.parent().text(precio_);
                        input.parent().html("");

                    }
                }
            });
        }
        if (e.which == 27) {
            input.parent().text(precio_);
            input.parent().html("");
        }
    });
});
$(".btnEliminar").on("click", function (e) {
    e.preventDefault();
    var form = $(this).find('.delete-form');
    bootbox.confirm("¿Segur@?", function (result) {
        form.submit();
    });
});
$(".producto-nombre").on("dblclick", function () {
    var id = $(this).parent().attr("data-id");
    var nombre_ = $(this).text();
    $(this).text("");
    $(this).html("<input type='text' class='form-control nuevo-nombre' data-id='" + id + "' value='" + nombre_ + "'></input>");
    $(document).off("keydown", ".nuevo-nombre").on("keydown", ".nuevo-nombre", function (e) {
        var input = $(this);
        if (e.which == 13) {

            var id = input.attr("data-id");
            var nombre = input.val();
            $.ajax({
                type: "PUT",
                url: $("#ruta-editar-nombre").val(),
                data: {
                    id: id,
                    nombre: nombre
                },
                success: function (r) {
                    if (r.estado) {
                        input.parent().text(nombre);
                        input.parent().html("");
                    } else {
                        alerta("No se pudo modificar el nombre", "danger");
                        input.parent().text(nombre_);
                        input.parent().html("");

                    }
                }
            });
        }
        if (e.which == 27) {
            input.parent().text(nombre_);
            input.parent().html("");
        }
    });
});
$(".producto-stock").on("dblclick", function () {
    var id = $(this).parent().attr("data-id");
    var stock_ = $(this).text();
    $(this).text("");
    $(this).html("<input type='number' class='form-control nuevo-stock' data-id='" + id + "' value='" + stock_ + "'></input>");
    $(document).off("keydown", ".nuevo-stock").on("keydown", ".nuevo-stock", function (e) {
        var input = $(this);
        if (e.which == 13) {

            var id = input.attr("data-id");
            var stock = input.val();
            $.ajax({
                type: "PUT",
                url: $("#ruta-editar-stock").val(),
                data: {
                    id: id,
                    stock: stock
                },
                success: function (r) {
                    if (r.estado) {
                        input.parent().text(stock);
                        input.parent().html("");
                    } else {
                        alerta("No se pudo modificar el stock", "danger");
                        input.parent().text(stock_);
                        input.parent().html("");

                    }
                }
            });
        }
        if (e.which == 27) {
            input.parent().text(stock_);
            input.parent().html("");
        }
    });
});
$('#tabla-reparaciones tfoot th').each(function () {
    var title = $(this).text();
    $(this).html('<input type="text" class="form-control" placeholder="' + title + '" />');
});
var tab = $("#tabla-reparaciones").DataTable({
    order: [[7, "desc"]],
    language: es,
    responsive: true
});
// Apply the search
tab.columns().every(function () {
    var that = this;

    $('input', this.footer()).on('keyup change clear', function () {
        if (that.search() !== this.value) {
            that
                .search(this.value)
                .draw();
        }
    });
});
$(document).on("click", ".btnReimprimir", function (e) {
    e.preventDefault();
    var id = $(this).attr("data-id");
    $.ajax({
        type: "GET",
        url: $("#urlReparacion").val() + "/" + id,
        success: function (d) {
            imprimirTicketReparacion(d);
        }
    });
});
$(document).on("click", ".btnRegistrarAbono", function (e) {
    e.preventDefault();
    // var id = $(this).attr("data-id");
    var href = $(this).attr("href");
    $.ajax({
        type: "GET",
        url: href,
        success: function (d) {
            $("#modal-general").find(".modal-content").html(d);
            $("#modal-general").modal("show");
            setTimeout(function () {
                $("#modal-general").find("input[name='monto']").focus().val("");
            }, 500);
            $("#modal-general").find("input[name='monto']").off("keyup").on("keyup", function (e) {
                if (e.which == 13)
                    $("#btn-realizar-abono").trigger("click");
            });
        }
    });
});
$(document).on("click", "#btn-realizar-abono", function (e) {
    var btn = $(this);
    e.preventDefault();
    btn.attr("disabled", true).text("Espere...");
    var monto = $("input[name='monto']").val();
    var id = $("input[name='id']").val();
    if (monto == "" || monto == 0) {
        alerta("El monto no es válido");
        btn.attr("disabled", false).text("Realizar abono");
        return;
    }
    $.ajax({
        type: "POST",
        url: $("#urlRealizarAbono").val(),
        data: {
            "monto": monto,
            "id": id
        },
        success: function (data) {
            if (data.estado)
                imprimirTicketReparacion(data);
            else {
                var me = "<ul>";
                for (var f = 0; f < data._m.length; f++) {
                    me += "<li>" + data._m[f] + "</li>";
                }
                me += "</ul>";
                alerta(me, "danger");
                btn.attr("disabled", false).text("Realizar abono");
            }
        },
        error: function () {
            alerta("Error al registrar el abono, anótelo alternativamente en el ticket del cliente", "danger");
            btn.attr("disabled", false).text("Realizar abono");
        }
    });
});
$(document).on("click", ".btnEntregar", function (e) {
    e.preventDefault();
    var href = $(this).attr("href");
    $.ajax({
        type: "GET",
        url: href,
        success: function (d) {
            if(d.estado){
                location.reload();
            }else{
                var me = "<ul>";
                for (var f = 0; f < data._m.length; f++) {
                    me += "<li>" + data._m[f] + "</li>";
                }
                me += "</ul>";
                alerta(me, "danger");
            }
        }
    });
});
