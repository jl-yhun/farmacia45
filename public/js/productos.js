$((function(){var a="1"===$("#permisoEdicion").val(),t="1"===$("#permisoEliminar").val(),e="1"===$("#permisoSimilares").val(),o=["nombre","stock","caducidad","descripcion","codigo_barras","compra","venta","categoria_id"];$(".btnEliminar").on("click",(function(a){a.preventDefault();var t=$(this).find(".delete-form");bootbox.confirm("¿Segur@?",(function(a){t.submit()}))})),$(document).on("click",".btnSimilares",(function(a){var t=$(this).attr("data-id");abrirModal($("#ruta-similares").val()+"/"+t,"GET","md")})),$(document).on("dblclick","td.producto-compra,td.producto-venta,td.producto-nombre,td.producto-stock,td.producto-caducidad,td.producto-descripcion,td.producto-codigo_barras,td.producto-categoria_id",(function(t){if(a){var e=$(this)[0].classList[0].trim();-1!==o.findIndex((a=>"producto-"+a==e))&&replaceWithInput($(this))}})),$("#table tfoot th").each((function(){var a=$(this).text(),t=$(this).attr("class")??"";$(this).html('<input type="text" class="form-control '+t+'" placeholder="'+a+'" />')})),categoriasOptions="<option value=''>TODAS</option>",JSON.parse(decodeURIComponent($("#categorias").val()).replace(/\+/g," ")).forEach((function(a,t){categoriasOptions+=`<option value='${a.id}'>${a.nombre}</option>`})),$("#table tfoot th.categoria").html(`<select class='form-control'>${categoriasOptions}</select>`);$(document).off("click",".btnImprimirEtiqueta").on("click",".btnImprimirEtiqueta",(function(a){a.preventDefault();var t=$(this).closest("form"),e=JSON.parse(decodeURIComponent(t.find("input[name='producto']").val()).replace(/\+/g," ")),o=t.find("input[name='cantidad']").val();imprimirEtiqueta({id:e.id,nombre:e.nombre,cantidad:o,precio:e.venta})})),$("#table").DataTable({order:[[0,"desc"]],processing:!0,serverSide:!0,ajax:"/productos/datatable",searchDelay:2e3,deferRender:!0,responsive:{details:{renderer:function(a,t,e){var o=$.map(e,(function(e,o){return e.hidden?`<tr data-id="${$(a.row(t).node()).attr("data-id")}" data-dt-row="${e.rowIndex}" data-dt-column="${e.columnIndex}">\n                                <th>${e.title}:</th>\n                                <td class="${a.cell(t,o).node().classList[0]}">${e.data}</td>\n                            </tr>`:""})).join("");return!!o&&$("<table/>").append(o)}}},columns:[{data:"id",name:"id"},{data:"codigo_barras",name:"codigo_barras",className:"producto-codigo_barras"},{data:"categoria.nombre",name:"categoria_id",className:"producto-categoria_id type-select"},{data:"nombre",name:"nombre",className:"producto-nombre"},{data:"descripcion",name:"descripcion",className:"producto-descripcion"},{data:"caducidad",name:"caducidad",className:"producto-caducidad type-date"},{data:"compra",name:"compra",className:"producto-compra"},{data:"venta",name:"venta",className:"producto-venta"},{data:"stock",name:"stock",className:"producto-stock"},{data:"categoria.tasa_iva_formatted",name:"tasa_iva"},{data:null,render:function(a){var o="";return t&&(o+=`\n            <a class='btn btn-danger mx-1 btnEliminar' \n                data-toggle="tooltip" title="Eliminar">\n                <i class='fa fa-close'></i>\n                <form class='delete-form' size='lg' \n                      action="/productos/${a.id}" id="formEliminar${a.id}" method="POST">\n                    <input type='hidden' name='_method' value='DELETE'>\n                    <input type='hidden' name='_token' value='${token}'>\n                </form>\n            </a>`),e&&(o+=`\n            <a data-id="${a.id}" class='btn btn-warning mx-1 btnSimilares' \n                data-toggle="tooltip" title="Ver similares">\n                <i class='fa fa-eye'></i>\n            </a>`),o},targets:[-1]}],initComplete:function(){var t=this.api();a||t.column("compra:name").visible(!1)},drawCallback:function(a){loadTooltips(),setTimeout((()=>{new $.fn.dataTable.Api(a).columns.adjust().responsive.recalc()}))},createdRow:function(a,t,e){0==t.activado&&$(a).addClass("bg-danger").addClass("text-white"),$(a).attr("data-id",t.id)}}).columns().every((function(){var a=this;$("input",this.footer()).on("keyup change clear",(function(t){if(("Enter"==t.key||""==this.value)&&a.search()!==this.value){let t=this.value.replace(/^0+(?!$)/,"");a.search(""==t?"":$(this).hasClass("folio")?"(^"+t+"$)":this.value,!0,!1).draw()}})),$("select",this.footer()).on("change",(function(){a.search()!==this.value&&a.search(""==this.value?"":"(^"+this.value+"$)",!0,!1).draw()}))}))})),$((function(){try{$("#table").DataTable(),$("#table_filter input").attr("data-cy","txt-busqueda"),$("#table").attr("data-cy","tbl"),$("#table_length select").attr("data-cy","select-length"),$("#table_paginate").attr("data-cy","paginacion"),$("#table_info").attr("data-cy","paginacion-info")}catch(a){console.error(a)}$(document).off("click",".btnEliminar").on("click",".btnEliminar",(function(a){a.preventDefault(),bootbox.confirm("¿Seguro que desea eliminar este registro?",(function(t){var e;t&&(e=$(a.target).find("form"),"i"==a.target.localName&&(e=$(a.target).parent().find("form")),e.trigger("submit"))}))})),$("body").on("keydown",(function(a){"F4"==a.key&&(a.preventDefault(),$("#btn-agregar").trigger("click"))}))}));