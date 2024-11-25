class Modal {
    modal;
    constructor(modales, ele) {
        this.modales = modales;
        this.url = ele.attr("href");
        this.method = ele.attr("method") ?? "GET";
        this.size = ele.attr("size") ?? "sm";
        this.lov = ele.hasClass("lov");
    }
    show() {
        this.modalId = this.modales.length + 1;
        $('body').append($('#modal-general').clone().attr("id", "modal-" + this.modalId));
        this.modal = $("#modal-" + this.modalId);
        this.modales.push(this.modalId);
        this.modal.find(".modal-dialog").addClass("modal-" + this.size);
        if (this.lov)
            this.modal.find(".modal-dialog").addClass("lov");
        $.ajax({
            type: this.method,
            url: this.url,
            success: function (res) {
                this.modal.find(".modal-content").html(res);
                this.modal.modal("show");
                setTimeout(function () {
                    this.modal.find("#table").DataTable({ scrollY: undefined });
                }, 250);

                // if (callback !== undefined)
                //     callback(modal);
            }
        });
    }
}