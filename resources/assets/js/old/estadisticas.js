let tiempoChart, vendedorChart, productoChart;
$(function () {
    let year = moment().format('Y');
    $(`#years`).val(year);
    $('#years').on("change", (e) => {
        cargarInfo(e.target.value);
    });
    cargarInfo(year);
});

const cargarInfo = (year) => {
    $.ajax({
        url: "/ventas/tiempo",
        type: "POST",
        data: {
            year
        },
        success: function (data) {
            tiempoChart?.destroy();
            tiempoChart = new Chart(
                document.getElementById('tiempoChart'),
                {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: "Ventas",
                                backgroundColor: "#ff0",
                                borderColor: "#ff0",
                                data: data.datasets[0]
                            },
                            {
                                label: "Utilidades",
                                backgroundColor: "#0ff",
                                borderColor: "#0ff",
                                data: data.datasets[1]
                            }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                }
            );
        }
    });
    $.ajax({
        url: "/ventas/vendedor",
        type: "POST",
        data: { year },
        success: function (data) {
            vendedorChart?.destroy();
            vendedorChart = new Chart(
                document.getElementById('vendedorChart'),
                {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: data.datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                }
            );
        }
    });
    $.ajax({
        url: "/ventas/producto",
        type: "POST",
        data: { year },
        success: function (data) {
            productoChart?.destroy();
            productoChart = new Chart(
                document.getElementById('productoChart'),
                {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: "# Ventas",
                                backgroundColor: "#f56",
                                borderColor: "#f56",
                                data: data.datasets
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                }
            );
        }
    });
}