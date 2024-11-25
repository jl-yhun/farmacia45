@component('componentes.modal-form')
    @slot('urlAction')
        {{ route('punto-venta.open') }}
    @endslot
    @slot('titulo')
        APERTURA DE CAJA
    @endslot
    @slot('modalBody')
        <div class="row">
            <div class="col-12 form-group">
                <label for=""><b>Efectivo</b> inicial</label>
                <input data-cy="txt-monto-inicial-efe" type="text" name="inicial_efe" value="{{ old('inicial_efe', '') }}"
                    placeholder="Dinero inicial en caja" class="form-control onlynumbers">
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for=""><b>Electrónico</b> inicial</label>
                <input data-cy="txt-monto-inicial-ele" type="text" name="inicial_ele" value="{{ old('inicial_ele', '') }}"
                    placeholder="Dinero en Mercado Pago" class="form-control onlynumbers">
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="">Dinero en <b>apartados</b></label>
                <input data-cy="txt-monto-inicial-apartados" type="text" name="inicial_apartados" value="{{ old('inicial_apartados', '') }}"
                    placeholder="Dinero inicial en apartados" class="form-control onlynumbers">
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="">Dinero en <b>recargas/servicios</b></label>
                <input data-cy="txt-monto-inicial-recargas_servicios" type="text" name="inicial_recargas_servicios" value="{{ old('inicial_recargas_servicios', '') }}"
                    placeholder="Dinero inicial en recargas/servicios" class="form-control onlynumbers">
            </div>
        </div>
        {{-- <div class="row">
            <div class="col-12 form-group">
                <label for="">Observaciones</label>
                <textarea data-cy="txt-observaciones" id="observaciones" name="observaciones" class="form-control" rows="6"
                    placeholder="Escriba alguna observación para comenzar..."></textarea>
            </div>
        </div> --}}
    @endslot
@endcomponent
