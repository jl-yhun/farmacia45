$(function () {
    var canEdit = $("#permisoEdicion").val() === "1";
    var canDelete = $("#permisoEliminar").val() === "1";
    var canSimilares = $("#permisoSimilares").val() === "1";

    var editables = [
        'nombre',
        'stock',
        'caducidad',
        'descripcion',
        'codigo_barras',
        'compra',
        'venta',
        'categoria_id'
    ];
    $(".btnEliminar").on("click", function (e) {
        e.preventDefault();
        var form = $(this).find('.delete-form');
        bootbox.confirm("¿Segur@?", function (result) {
            form.submit();
        });
    });

    $(document).on("click", ".btnSimilares", function (e) {
        var id = $(this).attr('data-id');
        abrirModal($('#ruta-similares').val() + '/' + id, "GET", "md")
    });

    $(document).on("dblclick",
        "td.producto-compra,td.producto-venta,td.producto-nombre,td.producto-stock,td.producto-caducidad,td.producto-descripcion,td.producto-codigo_barras,td.producto-categoria_id",
        function (e) {
            if (canEdit) {
                var className = $(this)[0].classList[0].trim();

                if (editables.findIndex(c => 'producto-' + c == className) !== -1) {
                    replaceWithInput($(this))
                }
            }
        });
    $('#table tfoot th').each(function () {
        var title = $(this).text();
        var cla = $(this).attr("class") ?? "";
        $(this).html('<input type="text" class="form-control ' + cla + '" placeholder="' + title + '" />');
    });
    categoriasOptions = "<option value=''>TODAS</option>";
    var categorias = JSON.parse(decodeURIComponent($("#categorias").val()).replace(/\+/g, ' '));
    categorias.forEach(function (v, i) {
        categoriasOptions += `<option value='${v.id}'>${v.nombre}</option>`;
    });
    $("#table tfoot th.categoria").html(`<select class='form-control'>${categoriasOptions}</select>`);

    var crearAcciones = function (r) {
        // var acciones = `<a class='btn btn-primary mx-1 modal-link' data-toggle="tooltip" title="Etiquetar" href='/productos/imprimir-etiqueta/${r.id}'>
        //                     <i class='fa fa-print'></i>
        //                 </a>`;
        var acciones = '';

        if (canDelete) {
            acciones += `
            <a class='btn btn-danger mx-1 btnEliminar' 
                data-toggle="tooltip" title="Eliminar">
                <i class='fa fa-close'></i>
                <form class='delete-form' size='lg' 
                      action="/productos/${r.id}" id="formEliminar${r.id}" method="POST">
                    <input type='hidden' name='_method' value='DELETE'>
                    <input type='hidden' name='_token' value='${token}'>
                </form>
            </a>`;
        }

        if (canSimilares) {
            acciones += `
            <a data-id="${r.id}" class='btn btn-warning mx-1 btnSimilares' 
                data-toggle="tooltip" title="Ver similares">
                <i class='fa fa-eye'></i>
            </a>`;
        }

        return acciones;
    }
    $(document).off("click", ".btnImprimirEtiqueta").on("click", ".btnImprimirEtiqueta", function (e) {
        e.preventDefault();
        var form = $(this).closest("form");
        var producto = JSON.parse(decodeURIComponent(form.find("input[name='producto']").val()).replace(/\+/g, " "));
        var cantidad = form.find("input[name='cantidad']").val();
        imprimirEtiqueta({
            "id": producto.id,
            "nombre": producto.nombre,
            "cantidad": cantidad,
            "precio": producto.venta
        });
    });
    var tab = $("#table").DataTable({
        order: [[0, "desc"]],
        processing: true,
        serverSide: true,
        ajax: '/productos/datatable',
        searchDelay: 2000,
        deferRender: true,
        responsive: {
            details: {
                renderer: function (api, rowIdx, columns) {
                    var data = $.map(columns, function (col, i) {
                        return col.hidden ?
                            `<tr data-id="${$(api.row(rowIdx).node()).attr('data-id')}" data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                                <th>${col.title}:</th>
                                <td class="${api.cell(rowIdx, i).node().classList[0]}">${col.data}</td>
                            </tr>` :
                            '';
                    }).join('');

                    return data ?
                        $('<table/>').append(data) :
                        false;
                }
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'codigo_barras', name: 'codigo_barras', className: 'producto-codigo_barras' },
            { data: 'categoria.nombre', name: 'categoria_id', className: 'producto-categoria_id type-select' },
            { data: 'nombre', name: 'nombre', className: 'producto-nombre' },
            { data: 'descripcion', name: 'descripcion', className: 'producto-descripcion' },
            { data: 'caducidad', name: 'caducidad', className: 'producto-caducidad type-date' },
            { data: 'compra', name: 'compra', className: 'producto-compra' },
            { data: 'venta', name: 'venta', className: 'producto-venta' },
            { data: 'stock', name: 'stock', className: 'producto-stock' },
            { data: 'categoria.tasa_iva_formatted', name: 'tasa_iva' },
            { data: null, render: crearAcciones, targets: [-1] }
        ],
        // columnDefs:[
        //     { targets: 3, className: 'producto-nombre' },
        // ],
        initComplete: function () {
            var api = this.api();

            if (!canEdit)
                // Hide Office column
                api.column('compra:name').visible(false);
        },
        drawCallback: function (settings) {
            loadTooltips();
            setTimeout(() => {
                var api = new $.fn.dataTable.Api(settings);
                api.columns.adjust().responsive.recalc();
            });
        },
        createdRow: function (row, data, dataIndex) {
            if (data.activado == 0)
                $(row).addClass("bg-danger").addClass("text-white");

            // Set the data-status attribute, and add a class
            $(row).attr('data-id', data.id);
        }
    });
    // Apply the search
    tab.columns().every(function () {
        var that = this;

        $('input', this.footer()).on('keyup change clear', function (e) {
            if (e.key == "Enter" || this.value == "") {
                if (that.search() !== this.value) {
                    let q = this.value.replace(/^0+(?!$)/, "");
                    that
                        .search(q == "" ? "" : $(this).hasClass("folio") ? "(^" + q + "$)" : this.value, true, false)
                        .draw();
                }
            }
        });
        $('select', this.footer()).on('change', function () {
            if (that.search() !== this.value) {
                that
                    .search(this.value == "" ? "" : "(^" + this.value + "$)", true, false)
                    .draw();
            }
        });
    });
});

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