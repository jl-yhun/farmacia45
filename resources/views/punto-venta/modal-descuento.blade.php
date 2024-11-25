@component('componentes.modal-form')
    @slot('titulo')
        Descuento
    @endslot
    @slot('modalBody')
        <div class="row form-group">
            <div class="col-6">
                Tipo
            </div>
            <div class="col-6">
                <select name="tipo_descuento" class="form-control">
                    <option value="monto" {{ old('tipo_descuento', '') == 'monto' ? 'selected' : '' }}>Por monto</option>
                    <option value="porcentaje" {{ old('tipo_descuento', '') == 'porcentaje' ? 'selected' : '' }}>Por porcentaje
                    </option>
                </select>
            </div>
        </div>
        <div class="row form-group descuento_tipo descuento_monto">
            <div class="col-6">
                Monto a descontar
            </div>
            <div class="col-6">
                <div class="input-group mb-3">
                    <div class="input-group-append">
                        <span class="input-group-text pointer btn-search-venta" id="basic-addon2">
                            <i class="fa fa-dollar"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control onlynumbers" autocomplete="off" name="descuento_valor"
                        value="{{ old('descuento_valor', '') }}">
                </div>
            </div>
        </div>
        <div class="row form-group descuento_tipo descuento_porcentaje d-none">
            <div class="col-6">
                Porcentaje a descontar
            </div>
            <div class="col-6">
                <div class="input-group mb-3">
                    <input type="text" class="form-control onlynumbers" autocomplete="off" name="descuento_valor"
                        value="{{ old('descuento_valor', '') }}">
                    <div class="input-group-append">
                        <span class="input-group-text pointer btn-search-venta" id="basic-addon2">
                            <i class="fa fa-percent"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-6">
                Total
            </div>
            <div class="col-6">
                <div class="input-group mb-3">
                    <div class="input-group-append">
                        <span class="input-group-text pointer btn-search-venta" id="basic-addon2">
                            <i class="fa fa-dollar"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control onlynumbers" readonly name="total"
                        value="{{ old('total', $producto->venta) }}">
                    <input type="hidden" name="producto" value="{{ urlencode(json_encode($producto)) }}">
                </div>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-12">
                Motivo del descuento
            </div>
            <div class="col-12">
                <div class="input-group mb-3">
                    <textarea name="motivo" class="form-control" rows="5">{{ old('motivo', '') }}</textarea>
                </div>
            </div>
        </div>
    @endslot
    @slot('botonOk')
        Realizar descuento
    @endslot
    @slot('customAction')
        btn-realizar-descuento
    @endslot
@endcomponent
