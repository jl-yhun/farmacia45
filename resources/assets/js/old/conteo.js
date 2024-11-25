var total = 0;
$(function () {
    setTimeout(function () {
        $("input.conteo").eq(0).trigger("focus");
        $("input.conteo").eq(0)[0].selectionStart =  $("input.conteo").eq(0)[0].value.length;
    }, 500);
    $(document).off("keyup", "input.conteo").on("keyup", "input.conteo", function () {
        recalcular();
    });
});
var recalcular = function () {
    total = 0;
    $("input.conteo").each(function (i, v) {
        var input = $(v);
        var name = input.attr("name");
        var monto = name.replace("_", "");
        var value = input.val();
        if (value != "") {
            total += parseInt(value) * parseFloat(monto);
        }
    });
    $("#total").text("$" + total.toFixed(2));
}