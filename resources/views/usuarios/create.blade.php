@component('componentes.modal-form')
    @slot('urlAction')
        {{ route('usuarios.store') }}
    @endslot
    @slot('titulo')
        Añadir usuario
    @endslot
    @slot('modalBody')
        <div>
            <div class="row">
                <div class="form-group col-12">
                    <label for="estado">Nombre</label>
                    <input type="text" value="{{ old('name') }}" name="name" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-12">
                    <label for="estado">Usuario</label>
                    <input type="text" value="{{ old('email') }}" name="email" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-12">
                    <label for="estado">Contraseña</label>
                    <input type="password" value="{{ old('password') }}" name="password" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-12">
                    <label for="estado">Rol</label>
                    <select class="form-control" name="role_id">
                        @foreach ($roles as $rol)
                            <option value="{{ $rol->id }}">
                                {{ $rol->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endslot
@endcomponent
