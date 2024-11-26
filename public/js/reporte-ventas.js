var table;
$(document).ready(function () {
    const from_date = moment().startOf("day");
    const to_date = moment().endOf("day");
    var rango = $('input[name="fechas"]');
    rango.daterangepicker({
        startDate: from_date,
        endDate: to_date,
        locale: {
            "format": "YYYY-MM-DD",
            "separator": " / ",
            "applyLabel": "Aplicar",
            "cancelLabel": "Cancelar",
            "fromLabel": "De",
            "toLabel": "a",
            "customRangeLabel": "Otro",
            "weekLabel": "SEMANA",
            "daysOfWeek": [
                "Do",
                "Lu",
                "Ma",
                "Mi",
                "Ju",
                "Vi",
                "Sa"
            ],
            "monthNames": [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ],
            "firstDay": 0
        },
        ranges: {
            'Hoy': [from_date, to_date],
            'Esta semana': [moment().startOf("week"), moment().endOf("week")],
            '-1 semana': [moment().startOf("week").subtract(7, 'days'), moment().endOf("week").subtract(7, 'days')],
            '-2 semanas': [moment().startOf("week").subtract(14, 'days'), moment().endOf("week").subtract(14, 'days')],
            '-3 semanas': [moment().startOf("week").subtract(21, 'days'), moment().endOf("week").subtract(21, 'days')],
            '-4 semanas': [moment().startOf("week").subtract(28, 'days'), moment().endOf("week").subtract(28, 'days')],
            '-5 semanas': [moment().startOf("week").subtract(35, 'days'), moment().endOf("week").subtract(35, 'days')],
        }
    });
    rango.on("apply.daterangepicker", function (e, p) {
        table.ajax.reload();
    });

    table = $("#table").DataTable({
        ajax: {
            url: $("#urlReportar").val(),
            type: "POST",
            data: function (d) {
                var drp = rango.data('daterangepicker');
                d.startDate = drp.startDate.format("YYYY-MM-DD");
                d.endDate = drp.endDate.format("YYYY-MM-DD");
            },
            dataSrc: ""
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: null, render: crearProductos, name: 'productos' },
            { data: 'total', name: 'total' },
            { data: 'denominacion', name: 'denominacion' },
            { data: 'cambio', name: 'cambio' },
            { data: 'utilidad', name: 'utilidad' },
            { data: 'created_at_formated', name: 'created_at' },
            { data: null, render: crearAcciones, targets: [-1] }
        ],
        createdRow: function (row, data, dataIndex) {
            if (data.garantias.findIndex(c => c.tipo == 'CANCELACIÓN') != -1) {
                $(row).addClass("bg-danger").addClass("text-white");
            }
        }
    });
    $("#btnExport").on("click", function(){
        var drp = rango.data('daterangepicker');
        var startDate = drp.startDate.format("YYYY-MM-DD");
        var endDate = drp.endDate.format("YYYY-MM-DD");
        location.href = `/t/${startDate}/${endDate}`;
    });
});
$(document).on("click", ".btnReimprimir", function (e) {
    e.preventDefault();
    var id = $(this).attr("data-id");
    imprimirTicketVenta({ "id": id, 'fromPtv': false}, function () {
        alerta("Se ha impreso el ticket", "success");
    }, function () {
        alerta("No se pudo imprimir el ticket, consulte con soporte", "danger");
    });
});
var crearProductos = function (r) {
    var productosList = "";
    for (var g = 0; g < r.productos.length; g++) {
        var producto = r.productos[g];
        productosList += `<li><a href="/productos?q=${producto.codigo_barras}">${producto.pivot.cantidad} ${producto.nombre} <br><sup>${producto.descripcion}</sup></a></li>`;
    }
    return `<ul class="px-0">${productosList}</ul>`;
}
var crearAcciones = function (r) {
    if (r.garantias.findIndex(c => c.tipo == 'CANCELACIÓN') != -1)
        return "CANCELADA";
    return `<a class='btn btn-warning btnReimprimir' data-id='${r.id}'><i class='fa fa-print'></i></a>`;
}