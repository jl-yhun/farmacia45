@component('componentes.modal-form')
    @slot('urlAction')
        {{ route('categorias.store') }}
    @endslot
    @slot('titulo')
        Añadir categoría
    @endslot
    @slot('modalBody')
        <div class="row">
            <div class="form-group col-12">
                <label for="estado">Nombre</label>
                <input data-cy="txt-nombre" type="text" value="{{ old('nombre') }}" name="nombre"
                    class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-12">
                <label for="tasa_iva">Tasa IVA</label>
                <select data-cy="txt-tasa-iva" name="tasa_iva" id="tasa_iva" class="form-control">
                    <option value="0" {{ old('tasa_iva') == 0 ? 'selected' : '' }}>0%</option>
                    <option value=".16" {{ old('tasa_iva') == 0.16 ? 'selected' : '' }}>16%</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-12">
                <label for="permite_cambio">Admite</label>
                <select data-cy="txt-admite" name="admite" class="form-control">
                    <option {{ old('admite') == 'GARANTÍA' ? 'selected' : '' }}>
                        GARANTÍA
                    </option>
                    <option {{ old('admite') == 'CAMBIO' ? 'selected' : '' }}>
                        CAMBIO
                    </option>
                    <option {{ old('admite') == 'NINGUNO' ? 'selected' : '' }}>
                        NINGUNO
                    </option>
                </select>
            </div>
        </div>
    @endslot
@endcomponent
