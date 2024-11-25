let modales = [];
$(function () {
    $(document).off("click", ".modal-link").on("click", ".modal-link", function (e) {
        e.preventDefault();
        var url = $(this).attr("href");
        var method = $(this).attr("method") ?? "GET";
        var size = $(this).attr("size") ?? "sm";
        var lov = $(this).hasClass("lov");
        abrirModal(url, method, size, lov);
    });
    $(document).on("hidden.bs.modal", function (e) {
        // Quitar de la lista de modales y remover el HTML del body
        let modal = e.target.id;
        if (modal == "" || modal == "modal-general") return; // Necesario por bootbox
        modales.splice(modales.findIndex(c => c == modal), 1);
        $("#" + modal).remove();
        if (modales.length > 0)
            $("body").addClass("modal-open");
    });
    $(document).off("click", "form .modal-footer button[type='submit']").on("click", "form .modal-footer button[type='submit']", function (event) {
        event.preventDefault();
        // toggleLoader();
        if (event.target.localName == "i")
            event.target = event.target.parentElement;
        var form = $(event.target.form);
        var modal = form.closest(".modal").attr("id");
        var values = form.serialize();
        var action = form.attr("action");
        var method = form.attr("method");
        if ($(`#${modal}`).find(".modal-dialog").hasClass("lov")) {
            if (form.find("input[name='selection']:checked").val() == undefined)
                return;
            $(document).trigger("on-lov-selection", {
                selected: JSON.parse(decodeURIComponent(form.find("input[name='selection']:checked").val().replace(/\+/g, ' '))),
                tipo: $(`#${modal}`).find(".modal-dialog").attr("tipo")
            });
            $(`#${modal}`).modal("hide");
            return;
        }
        $(event.target).attr("disabled", true);
        $.ajax({
            type: method,
            url: action,
            data: values,
            success: function (res) {
                if (res.estado) {
                    if (res.auth_token)
                        localStorage.setItem('_t', res.auth_token);
                    location.reload();
                } else if (res.callback) {
                    var fn = window[res.callback];
                    if (typeof fn === "function") fn(res.params);
                    $(event.target).attr("disabled", true);
                } else if (res.estado == false) {
                    var me = "<ul>";
                    for (var f = 0; f < res.errors.length; f++) {
                        me += "<li>" + res.errors[f] + "</li>";
                    }
                    me += "</ul>";
                    alerta(me, "danger");
                    $(event.target).attr("disabled", false);
                } else {
                    // Se muestran las notificaciones que haya
                    $('.alert.notificacion').fadeIn();
                    // toggleLoader();
                    $("#" + modal).find(".modal-content").html(res);
                    $(event.target).attr("disabled", false);
                }
            },
            complete: function (a) {
                if (a.status == 422) { // Laravel validation error
                    var res = a.responseJSON;
                    alerta(res.message, 'danger');
                    $(event.target).attr('disabled', false);
                }
            },
            error: function (err) {
            }
        });
    });
});

var abrirModal = function (url, method, size, lov = false, tipo = "", callback = undefined) {
    $("body").attr("style", "pointer-events:none;");
    let modalId = modales.length + 1;
    // Se copea el template del modal
    $('body').append($('#modal-general').clone().attr("id", "modal-" + modalId));
    let modal = $("#modal-" + modalId);
    // Se agrega a la lista
    modales.push(modalId);
    modal.find(".modal-dialog").addClass("modal-" + size);
    if (lov) {
        modal.find(".modal-dialog").addClass("lov");
        modal.find(".modal-dialog").attr("tipo", tipo);
    }

    $.ajax({
        type: method,
        url: url,
        success: function (res) {
            modal.find(".modal-content").html(res);
            modal.modal("show");
            var zIndex = 1040 + (10 * modales.length);
            modal.css('z-index', zIndex);
            setTimeout(function () {
                $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
            }, 0);
            setTimeout(function () {
                modal.find("#table").DataTable({ scrollY: undefined });
            }, 250);

            if (callback !== undefined)
                callback(modal);
            $("body").removeAttr("style");
        },
        error: function (err) {
            if (err.status == 404)
                alerta("No encontrado!", "danger");
        }
    });
};
var ocultarModal = function () {
    $("#modal-" + modales[modales.length - 1]).modal("hide");
}