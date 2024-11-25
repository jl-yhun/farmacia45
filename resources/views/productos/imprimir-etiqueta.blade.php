@component('componentes.modal-form')
    @slot('titulo')
        Cantidad de etiquetas a imprimir
    @endslot
    @slot('modalBody')
        <div class="row form-group">
            <div class="col-6">
                Cantidad
            </div>
            <div class="col-6">
                <input type="hidden" name="producto" value="{{ urlencode(json_encode($producto)) }}">
                <input type="text" class="form-control onlynumbers" name="cantidad" value="1">
            </div>
        </div>
    @endslot
    @slot('botonOk')
        Imprimir
    @endslot
    @slot('customAction')
        btnImprimirEtiqueta
    @endslot
@endcomponent
