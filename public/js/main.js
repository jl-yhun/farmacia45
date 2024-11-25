"use strict";var es={processing:"Procesando...",search:"Buscar:",lengthMenu:"Mostrar _MENU_ registros",info:"Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",infoEmpty:"Mostrando registros del 0 al 0 de un total de 0 registros",infoFiltered:"(filtrado de un total de _MAX_ registros)",infoPostFix:"",loadingRecords:"Cargando...",zeroRecords:"No se encontraron resultados",emptyTable:"Ningún dato disponible en esta tabla",paginate:{first:"Primero",previous:"Anterior",next:"Siguiente",last:"Último"}};let modales=[];$((function(){$(document).off("click",".modal-link").on("click",".modal-link",(function(t){t.preventDefault();var e=$(this).attr("href"),o=$(this).attr("method")??"GET",a=$(this).attr("size")??"sm",n=$(this).hasClass("lov");abrirModal(e,o,a,n)})),$(document).on("hidden.bs.modal",(function(t){let e=t.target.id;""!=e&&"modal-general"!=e&&(modales.splice(modales.findIndex((t=>t==e)),1),$("#"+e).remove(),modales.length>0&&$("body").addClass("modal-open"))})),$(document).off("click","form .modal-footer button[type='submit']").on("click","form .modal-footer button[type='submit']",(function(t){t.preventDefault(),"i"==t.target.localName&&(t.target=t.target.parentElement);var e=$(t.target.form),o=e.closest(".modal").attr("id"),a=e.serialize(),n=e.attr("action"),i=e.attr("method");if($(`#${o}`).find(".modal-dialog").hasClass("lov")){if(null==e.find("input[name='selection']:checked").val())return;return $(document).trigger("on-lov-selection",{selected:JSON.parse(decodeURIComponent(e.find("input[name='selection']:checked").val().replace(/\+/g," "))),tipo:$(`#${o}`).find(".modal-dialog").attr("tipo")}),void $(`#${o}`).modal("hide")}$(t.target).attr("disabled",!0),$.ajax({type:i,url:n,data:a,success:function(e){if(e.estado)e.auth_token&&localStorage.setItem("_t",e.auth_token),location.reload();else if(e.callback){var a=window[e.callback];"function"==typeof a&&a(e.params),$(t.target).attr("disabled",!0)}else if(0==e.estado){for(var n="<ul>",i=0;i<e.errors.length;i++)n+="<li>"+e.errors[i]+"</li>";alerta(n+="</ul>","danger"),$(t.target).attr("disabled",!1)}else $(".alert.notificacion").fadeIn(),$("#"+o).find(".modal-content").html(e),$(t.target).attr("disabled",!1)},complete:function(e){if(422==e.status){var o=e.responseJSON;alerta(o.message,"danger"),$(t.target).attr("disabled",!1)}},error:function(t){}})}))}));var abrirModal=function(t,e,o,a=!1,n="",i=void 0){$("body").attr("style","pointer-events:none;");let r=modales.length+1;$("body").append($("#modal-general").clone().attr("id","modal-"+r));let s=$("#modal-"+r);modales.push(r),s.find(".modal-dialog").addClass("modal-"+o),a&&(s.find(".modal-dialog").addClass("lov"),s.find(".modal-dialog").attr("tipo",n)),$.ajax({type:e,url:t,success:function(t){s.find(".modal-content").html(t),s.modal("show");var e=1040+10*modales.length;s.css("z-index",e),setTimeout((function(){$(".modal-backdrop").not(".modal-stack").css("z-index",e-1).addClass("modal-stack")}),0),setTimeout((function(){s.find("#table").DataTable({scrollY:void 0})}),250),void 0!==i&&i(s),$("body").removeAttr("style")},error:function(t){404==t.status&&alerta("No encontrado!","danger")}})},ocultarModal=function(){$("#modal-"+modales[modales.length-1]).modal("hide")};window.isMobile=!1;let session=!1;var token=$('meta[name="csrf-token"]').attr("content"),mobileCheck=function(){return/Android|webOS|iPhone|iPad|Mac|Macintosh|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)&&screen.width<992},imprimirTicketReparacion=function(t){var e=t.data;$.ajax({type:"POST",url:"http://localhost/tickets/example/ticket_reparacion.php",contentType:"application/json",data:JSON.stringify({cliente:e.cliente,folio:e.folio,marca:e.marca,modelo:e.modelo,fecha_entrega:e.fecha_entrega,costo:-1==e.costo?"Pendiente":"$"+parseInt(e.costo).toFixed(2),abono:-1==e.abono?"Ninguno":"$"+parseInt(e.abono).toFixed(2)}),success:function(t){location.href="/reparaciones"},error:function(){alerta("No se pudo imprimir el ticket.","info"),setTimeout((function(){location.href="/reparaciones"}),500)}})},imprimirTicketVenta=function(t){$.ajax({type:"GET",url:"/ventas/"+t.id+"/json",success:function(t){for(var e=t,o=[],a=0;a<e.productos.length;a++)o.push({nombre:e.productos[a].nombre,importe:"$"+(e.productos[a].pivot.venta*e.productos[a].pivot.cantidad).toFixed(0),cantidad:e.productos[a].pivot.cantidad});$.ajax({type:"POST",url:"http://localhost/tickets/ticket.php",contentType:"application/json",data:JSON.stringify({articulos:o,total:"$"+parseFloat(e.total).toFixed(0),denominacion:"$"+parseFloat(e.denominacion).toFixed(0),cambio:"$"+(parseFloat(e.denominacion)-parseFloat(e.total)).toFixed(0),metodoPago:e.metodo_pago,folio:e.id,usuario:e.usuario.name}),success:function(){alerta("Listo, <b>no olvides dar ticket al cliente</b><br>Cambio: $"+parseFloat(e.cambio).toFixed(2),"success")},error:function(){alerta("No se pudo imprimir el ticket","info")},complete:function(){productos=[],recargarCuenta(),ocultarModal()}})}})},imprimirTicketGarantia=function(t){$.ajax({type:"GET",url:"/garantias/"+t.id+"/json",success:function(t){for(var e=t.garantia,o=[],a=0;a<e.productos_nuevos.length;a++){var n=e.productos_nuevos[a];o.push({nombre:n.nombre,importe:"$"+(n.venta*n.pivot.cantidad).toFixed(0),cantidad:n.pivot.cantidad})}$.ajax({type:"POST",url:"http://localhost/tickets/ticketGarantia.php",contentType:"application/json",data:JSON.stringify({nuevos:o,producto:{nombre:e.producto_devuelto.nombre,importe:"$"+e.producto_devuelto.venta},tipo:e.tipo,venta:e.venta_id,folio:e.id,diferencia:e.diferencia,usuario:e.usuario.name}),success:function(){alerta("Impresión correcta.","success")},error:function(){alerta("No se pudo imprimir el ticket","info")}})}})},imprimirTicketCorte=function(t){$.ajax({type:"POST",url:"http://localhost/tickets/ticketCorte.php",contentType:"application/json",data:JSON.stringify(t),success:function(){alerta("Impresión correcta.","success")},error:function(){alerta("No se pudo imprimir el ticket","info")}})},imprimirEtiqueta=function(t){$.ajax({type:"POST",url:"http://localhost/tickets/etiqueta.php",contentType:"application/json",data:JSON.stringify({codigo:t.id,nombre:t.nombre,cantidad:t.cantidad,precio:t.precio}),success:function(){console.log("Ok")},error:function(){alerta("No se pudieron imprimir las etiquetas","info")},complete:function(){ocultarModal()}})},alerta=function(t,e="info"){$(".js-generated").fadeOut((function(){$(this).remove()}));var o='<div class="js-generated notificacion alert alert-'+e+' alert-dismissible" role="alert"><span class="mensaje">'+t+'</span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';$("body").append(o),$(".js-generated").fadeIn(1e3,(function(){}))},ocultarOtros=function(){$(".inline").each((function(t,e){var o,a=$(e);o="select"==a.prop("tagName").toLowerCase()?a.find("option:selected").text():a.val(),a.parent().text(o),a.parent().html("")}))},replaceWithInput=function(t,e=void 0){var o=t.parent().attr("data-id");if(!t.has(".form-control.inline").length){var a,n=t.text(),i=t[0].classList,r=i[0].split("-")[1],s=i[1]?.split("-")[1]??"text";ocultarOtros(),t.text(""),a="select"==s?$(`<select class='form-control inline' data-id='${o}'>\n                        ${categoriasOptions}\n                   </select>`):$(`<input type='${s}' class='form-control inline' data-id='${o}' value='${n}'/>`),setTimeout((function(){a.trigger("focus"),"text"==s&&(a[0].selectionStart=a[0].value.length)}),0),t.html(a),"select"==s?($('select.inline[data-id="'+o+'"] option:contains("'+n+'")').prop("selected",!0),$('select.inline[data-id="'+o+'"] option:contains("TODAS")').remove(),a.off("change").on("change",(()=>{var t=a.val(),e=a.find("option:selected").text();patchItem(o,r,t,(t=>{t.estado?(a.parent().text(e),a.parent().html("")):(alerta(`No se pudo modificar el valor de ${r}`,"danger"),a.parent().text(n),a.parent().html(""))}))}))):a.off("keydown").on("keydown",(function(i){if(13==i.which){var s=a.val();void 0!==o?patchItem(o,r,s,(t=>{t.estado?(a.parent().text(s),a.parent().html("")):(alerta(`No se pudo modificar el valor de ${r}`,"danger"),a.parent().text(n),a.parent().html(""))})):e(t,s)}27==i.which&&(a.parent().text(n),a.parent().html(""))}))}},patchItem=(t,e,o,a)=>{$.ajax({type:"PATCH",url:$("#ruta-editar-producto").val()+`/${t}`,data:{[e]:o},success:function(t){a(t)}})},loadTooltips=function(){$("[data-toggle='tooltip']").tooltip()},logout=function(){$.ajax({type:"POST",url:"/logout",complete:function(){localStorage.removeItem("_t"),location.reload()}})};$((function(){bootbox.setDefaults({locale:"es"}),$.ajaxSetup({headers:{"X-CSRF-TOKEN":token}}),loadTooltips(),$(document).on("keypress",".onlynumbers",(function(t){return!(null!=$(this).attr("data-length")&&$(this).val().length>=$(this).attr("data-length"))&&!((46!=t.which||-1!=$(this).val().indexOf("."))&&(t.which<48||t.which>57))})),window.isMobile=mobileCheck(),$(".alert.notificacion").fadeIn(1e3,(function(){setTimeout((function(){$(".alert.notificacion").fadeOut()}),5e3)})),$.fn.dataTable&&$.extend(!0,$.fn.dataTable.defaults,{language:es,scrollY:isMobile?void 0:"50vh",scrollCollapse:!1,order:[[0,"desc"]]}),$(document).on("click",".btnCerrarCaja",(function(t){t.preventDefault();var e=$(this).attr("href");bootbox.confirm({message:"¿Seguro que desea cerrar la caja?",locale:"es",buttons:{confirm:{className:"btn btn-primary btn-ok"}},callback:function(t){t&&(location.href=e)}})})),$(document).on("click",".panel-overflow .item-usuario",(function(t){t.preventDefault();var e=$(this).attr("data-id");abrirModal(`/usuarios/cambiar/perfil/${e}`,"GET","sm",!1,"",(function(t){setTimeout((function(){t.find("input[name='password']").focus()}),500)}))})),session="yes"==$("#session").val(),session||abrirModal("/login","GET","md",!1,"",(t=>{t.find('[data-dismiss="modal"]').addClass("d-none"),$("#isAdmin").on("click",(function(){$(this).prop("checked")?$("#passwordBox").removeClass("d-none"):$("#passwordBox").addClass("d-none")}))}))}));var refresh=()=>{location.reload()};