@component('componentes.modal-form')
    @slot('urlAction')
        {{ route('usuarios.update', $usuario) }}
    @endslot
    @slot('method')
        {{ method_field('PUT') }}
    @endslot
    @slot('titulo')
        Editar usuario
    @endslot
    @slot('modalBody')
        <div>
            <div class="row">
                <div class="form-group col-12">
                    <label for="estado">Nombre</label>
                    <input type="text" value="{{ old('name', $usuario->name) }}" name="name" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-12">
                    <label for="estado">Usuario</label>
                    <input type="text" value="{{ old('email', $usuario->email) }}" name="email" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-warning">
                        Sólo escriba una nueva contraseña si desea <b>CAMBIARLA</b>
                    </div>
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
                        <option value="">--Seleccione un rol--</option>
                        @foreach ($roles as $rol)
                            <option {{ $usuario->hasRole($rol->name) ? 'selected' : '' }} value="{{ $rol->id }}">
                                {{ $rol->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endslot
@endcomponent
