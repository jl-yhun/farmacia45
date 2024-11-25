@component('componentes.modal-form')
    @slot('urlAction')
        {{ route('productos.store') }}
    @endslot
    @slot('titulo')
        Añadir producto
    @endslot
    @slot('modalBody')
        <div class="row">
            <div class="col-12 form-group">
                <label for="">Nombre</label>
                <input type="text" value="{{ old('nombre') }}" name="nombre" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-12 form-group">
                <label for="">Código de barras</label>
                <input type="text" value="{{ old('codigo_barras') }}" name="codigo_barras" class="form-control">
            </div>
            <div class="col-md-6 col-12 form-group">
                <label for="">Caducidad</label>
                <input type="date" value="{{ old('caducidad') }}" name="caducidad" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="">Descripción</label>
                <textarea class="form-control" name="descripcion">{{ old('descripcion') }}</textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-12 form-group">
                <label for="">Precio compra</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">$</span>
                    </div>
                    <input type="text" value="{{ old('compra') }}" name="compra" class="form-control onlynumbers">
                </div>
            </div>
            <div class="col-md-6 col-12 form-group">
                <label for="">Precio venta</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">$</span>
                    </div>
                    <input type="text" value="{{ old('venta') }}" name="venta" class="form-control onlynumbers">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-12 form-group">
                <label for="">Categoría</label>
                <select name="categoria_id" class="form-control">
                    <option value="">-- Seleccione --</option>
                    @foreach ($categorias as $categoria)
                        <option {{ old('categoria_id') == $categoria->id ? 'selected' : '' }} value="{{ $categoria->id }}">
                            {{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 col-12 form-group">
                <label for="">Stock</label>
                <input type="number" value="{{ old('stock') }}" name="stock" class="form-control">
            </div>
        </div>
    @endslot
@endcomponent
