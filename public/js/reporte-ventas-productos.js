var table;
$(document).ready(function () {
    const from_date = moment().startOf("year");
    const to_date = moment().endOf("year");
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
            'Este año': [from_date, to_date],
            'Este mes': [moment().startOf("month"), moment().endOf("month")],
            '-1 mes': [moment().startOf("month").subtract(1, 'month'), moment().endOf("month").subtract(1, 'month')],
            '-2 mes': [moment().startOf("month").subtract(2, 'month'), moment().endOf("month").subtract(2, 'month')],
            '-3 mes': [moment().startOf("month").subtract(3, 'month'), moment().endOf("month").subtract(3, 'month')],
            '-4 mes': [moment().startOf("month").subtract(4, 'month'), moment().endOf("month").subtract(4, 'month')],
            '-5 mes': [moment().startOf("month").subtract(5, 'month'), moment().endOf("month").subtract(5, 'month')],
            '-6 mes': [moment().startOf("month").subtract(6, 'month'), moment().endOf("month").subtract(6, 'month')],
        }
    });
    
    $("#btnExport").on("click", function(){
        var drp = rango.data('daterangepicker');
        var startDate = drp.startDate.format("YYYY-MM-DD");
        var endDate = drp.endDate.format("YYYY-MM-DD");
        location.href = `/excel/ventas-x-producto/${startDate}/${endDate}`;
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
        searching: false,
        order: [[3, 'desc']],
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'categoria' },
            { data: 'veces' },
            { data: 'compra' },
            { data: 'venta' },
            { data: 'compra_total' },
            { data: 'venta_total' },
            { data: 'utilidad' }
        ]
    });
});