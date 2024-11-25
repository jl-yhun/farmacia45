@component('componentes.modal-form')
    @slot('urlAction')
        {{ route('permisos-temp.store') }}
    @endslot
    @slot('titulo')
        Conceder permiso a <strong class="text-danger">{{ $usuario->name }}</strong> temporalmente
    @endslot
    @slot('modalBody')
        <input type="hidden" name="usuario_id" value="{{ $usuario->id }}">
        <div class="row">
            <div class="form-group col-12">
                <label for="permiso_id">Permiso</label>
                <select name="permiso_id" class="form-control">
                    @foreach ($permisos as $permiso)
                        <option value="{{ $permiso->id }}" {{ $permiso->id == old('permiso_id', '') ? 'selected' : '' }}>
                            {{ $permiso->friendly_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-12">
                <label for="permiso_id">Tiempo</label>
                <div class="input-group mb-3">
                    <input type="text" name="tiempo" value="{{ old('tiempo', '') }}" autocomplete="off"
                        class="form-control onlynumbers">
                    <div class="input-group-prepend">
                        <span class="input-group-text pointer btn-search-producto">
                            Minutos
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endslot
@endcomponent
