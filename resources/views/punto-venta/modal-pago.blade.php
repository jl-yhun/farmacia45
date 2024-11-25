@component('componentes.modal-form')
    @slot('urlAction')
        {{ route('ventas.create') }}
    @endslot
    @slot('titulo')
        Cobro
    @endslot
    @slot('modalBody')
        <div class="row form-group">
            <div class="col-6">
                Total
            </div>
            <div class="col-6">
                <b data-cy="lbl-total-ptv" class="total">${{ number_format(old('total', 0), 2) }}</b>
                <input type="hidden" value="{{ old('total') }}" name="total">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-6">
                Método
            </div>
            <div class="col-6">
                <select data-cy="sel-metodo-pago-ptv" name="metodo_pago" class="form-control">
                    <option>Efectivo</option>
                    <option>Tarjeta de crédito</option>
                    <option>Tarjeta de débito</option>
                    <option>Transferencia</option>
                </select>
            </div>
        </div>
        <div id="controles-efectivo">
            <div class="row form-group">
                <div class="col-6">
                    Se recibe
                </div>
                <div class="col-6">
                    <input data-cy="txt-se-recibe-ptv" type="number" class="form-control se-recibe" value="{{ old('se-recibe') }}" name="se-recibe">
                </div>
            </div>
            <div class="row form-group">
                <div class="col-6">
                    Cambio
                </div>
                <div class="col-6">
                    <b class="cambio" data-cy="lbl-cambio-ptv">${{ number_format(old('se-recibe', 0) - old('total', 0), 2) }}</b>
                </div>
            </div>
        </div>
        @foreach (old('productos', []) as $key => $producto)
            <input type="hidden" name="productos[{{ $key }}][cantidad]" value="{{ $producto['cantidad'] }}">
            <input type="hidden" name="productos[{{ $key }}][venta]" value="{{ $producto['venta'] }}">
            <input type="hidden" name="productos[{{ $key }}][id]" value="{{ $producto['id'] }}">
        @endforeach
    @endslot
    @slot('botonOk')
        Cobrar
    @endslot
    @slot('customAction')
        btn-realizar-cobro
    @endslot
@endcomponent
