$(function () {
    $("#admin").on("click", function () {
        var admin = $(this).prop("checked");
        var checkboxes = $(".permisos-cont").find("input[type='checkbox']");
        checkboxes.attr("checked", admin);
        checkboxes.attr("disabled", admin);
    });
});