<div class='row'>
    <div class="col-8">
        <h3>Monto Inicial (apertura)</h3>
    </div>
    <div class="col-4">
        <h3 class="text-right">$ {{ $cierre->monto_inicio }}</h3>
    </div>
</div>
<hr>
<div class='row'>
    <div class="col-8">
        <h3>Ventas en efectivo</h3>
    </div>
    <div class="col-4">
        <h3 class="text-right">$ {{ $cierre->efectivo }}</h3>
    </div>
</div>
<hr>
<div class='row'>
    <div class="col-8">
        <h3>Ventas con tarjeta (débito o crédito)</h3>
    </div>
    <div class="col-4">
        <h3 class="text-right">$ {{ $cierre->electronico }}</h3>
    </div>
</div>
<hr>
<div class='row'>
    <div class="col-8">
        <h3>Montos adicionales por garantías / cambios</h3>
    </div>
    <div class="col-4">
        <h3 class="text-right">$ {{ $cierre->diferencia_garantias }}</h3>
    </div>
</div>
<hr>
<div class='row text-danger'>
    <div class="col-8">
        <h3>Devoluciones de dinero</h3>
    </div>
    <div class="col-4">
        <h3 class="text-right">{{ $cierre->devoluciones > 0 ? '-' : '' }} $ {{ $cierre->devoluciones }}</h3>
    </div>
</div>
<hr>
<div class='row text-danger'>
    <div class="col-8">
        <h3>Gastos</h3>
    </div>
    <div class="col-4">
        <h3 class="text-right">$ {{ $cierre->gastos }}</h3>
    </div>
</div>
<hr>
<div class='row text-info'>
    <div class="col-8">
        <h3>Subtotal</h3>
    </div>
    <div class="col-4">
        <h3 class="text-right">$ {{ $cierre->subtotal }}
        </h3>
    </div>
</div>
<hr>
<div class='row text-info'>
    <div class="col-8">
        <h3>Total en caja</h3>
    </div>
    <div class="col-4">
        <h3 class="text-right">$
            {{ $cierre->en_caja }}
        </h3>
    </div>
</div>
<hr>
