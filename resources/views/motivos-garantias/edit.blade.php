@component('componentes.modal-form')
    @slot('urlAction')
        {{ route('motivos-garantias.update', $motivo_garantia) }}
    @endslot
    @slot('method')
        {{ method_field('PUT') }}
    @endslot
    @slot('titulo')
        Editar motivo de garantía
    @endslot
    @slot('modalBody')
        <div class="row">
            <div class="form-group col-12">
                <label for="estado">Nombre</label>
                <input type="text" value="{{ old('nombre', $motivo_garantia->nombre) }}" name="nombre" class="form-control">
            </div>
        </div>
    @endslot
@endcomponent
