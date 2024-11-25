@component('componentes.modal-form')
    @slot('urlAction')
        {{ route('usuarios.do-cambiar-perfil') }}
    @endslot
    @slot('titulo')
        Ingrese contraseña
    @endslot
    @slot('modalBody')
        <div class="row">
            <div class="col-12 form-group">
                <label for="">Usuario</label>
                <input type="text" readonly value="{{ $usuario->email }}" class="form-control">
                <input type="hidden" name="id" value="{{$usuario->id}}">
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="">Contraseña</label>
                <input type="password" name="password" class="form-control">
            </div>
        </div>
    @endslot
@endcomponent
