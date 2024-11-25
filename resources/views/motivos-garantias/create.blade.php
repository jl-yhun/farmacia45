@component('componentes.modal-form')
    @slot('urlAction')
        {{ route('motivos-garantias.store') }}
    @endslot
    @slot('titulo')
        Añadir motivo de garantía
    @endslot
    @slot('modalBody')
        <div class="row">
            <div class="form-group col-12">
                <label for="estado">Nombre</label>
                <input type="text" value="{{ old('nombre') }}" name="nombre" class="form-control">
            </div>
        </div>
    @endslot
@endcomponent
