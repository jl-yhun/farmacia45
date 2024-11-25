$(document).ready(function () {
    const from_date = moment().startOf('month');
    const to_date = moment().endOf('month');
    var rango = $('input[name="fechas"]');
    crearReporte(from_date.format("YYYY-MM-DD"), to_date.format("YYYY-MM-DD"));
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
            "weekLabel": "MES",
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
            'Actual': [from_date, to_date],
            '-1 mes': [moment().startOf("month").subtract(1, 'months'), moment().endOf("month").subtract(1, 'months')],
            '-2 meses': [moment().startOf("month").subtract(2, 'months'), moment().endOf("month").subtract(2, 'months')],
            '-3 meses': [moment().startOf("month").subtract(3, 'months'), moment().endOf("month").subtract(3, 'months')],
            '-4 meses': [moment().startOf("month").subtract(4, 'months'), moment().endOf("month").subtract(4, 'months')],
            '-5 meses': [moment().startOf("month").subtract(4, 'months'), moment().endOf("month").subtract(5, 'months')],
            '-6 meses': [moment().startOf("month").subtract(6, 'months'), moment().endOf("month").subtract(6, 'months')]
        }
    });
    rango.on("apply.daterangepicker", function (e, p) {
        var startDate = p.startDate.format("YYYY-MM-DD");
        var endDate = p.endDate.format("YYYY-MM-DD");

        crearReporte(startDate, endDate);
    });
});
var crearReporte = function (startDate, endDate) {
    var reporte = $("#reporte");
    reporte.find(".cuerpo").html("");
    var ventas = $("#ventas");
    var reparaciones = $("#reparaciones");
    var utilidades = $("#utilidad");
    ventas.val(0);
    reparaciones.val(0);
    utilidades.val(0);
    $.ajax({
        type: "POST",
        url: $("#urlReportar").val(),
        data: {
            startDate: startDate,
            endDate: endDate
        },
        success: function (registros) {
            var cuerpo = reporte.find(".cuerpo");
            var ventas_ = 0;
            var reparaciones_ = 0;
            var utilidades_ = 0;
            for (var f = 0; f < registros.length; f++) {
                var venta = registros[f];
                var fila = $("<tr></tr>");
                fila.append("<td>$" + venta.monto_inicio + "</td>");
                fila.append("<td>$" + venta.ventas_finales + "</td>");
                fila.append("<td>$" + venta.utilidades + "</td>");
                fila.append("<td>$" + venta.reparaciones_finales + "</td>");
                fila.append("<td>" + venta.created_at + "</td>");
                fila.append("<td>" + venta.fecha_hora_cierre + "</td>");
                cuerpo.append(fila);

                reparaciones_ += parseFloat(venta.reparaciones_finales);
                ventas_ += parseFloat(venta.ventas_finales);
                utilidades_ += parseFloat(venta.utilidades + venta.reparaciones_finales);
            }
            ventas.val("$" + ventas_.toFixed(2));
            reparaciones.val("$" + reparaciones_.toFixed(2));
            utilidades.val("$" + utilidades_.toFixed(2));
        }
    });
}
