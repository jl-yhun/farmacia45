@component('componentes.modal-form')
    @slot('urlAction')
        {{ route('perdidas.store') }}
    @endslot
    @slot('titulo')
        Declarar pérdida
    @endslot
    @slot('modalBody')
        <input type="hidden" name="usuario_id" value="{{ auth()->user()->id }}">
        <div class="row">
            <div class="form-group col-sm-12">
                <label for="producto_id">Producto a declarar</label>
                <div class="input-group mb-3">
                    <input data-cy="txt-producto" type="text" name="producto_nombre" value="{{ old('producto_nombre', '') }}" autocomplete="off"
                        class="form-control">
                    <input type="hidden" name="producto_id" value="{{ old('producto_id', '') }}">
                    <div class="input-group-prepend">
                        <span class="input-group-text pointer btn-search-producto" id="basic-addon2">
                            <i class="fa fa-search"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="motivo">Motivo</label>
                <textarea data-cy="txt-motivo" name="motivo" rows="7" class="form-control">{{ old('motivo') }}</textarea>
            </div>
        </div>
    @endslot
@endcomponent
