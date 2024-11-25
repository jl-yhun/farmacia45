@component('componentes.modal-form')
    @slot('urlAction')
        {{ route('login') }}
    @endslot
    @slot('titulo')
        Acceder como:
    @endslot
    @slot('modalBody')
        <div class="form-group row">
            <label for="email" class="col-md-4 col-form-label text-md-right">Usuario</label>

            <div class="col-md-6">
                <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}" required
                    autocomplete="email" autofocus>
            </div>
        </div>
        {{-- <div class="form-group row">
            <label class="form-check-label col-md-4 text-md-right" for="isAdmin">
                Soy ADMIN
            </label>
            <div class="form-check col-md-6">
                <input class="form-check-input ml-1" name="isAdmin" type="checkbox" value="1" id="isAdmin">
            </div>
        </div> --}}
        <div class="form-group row">
            <label for="password" class="col-md-4 col-form-label text-md-right">Contraseña</label>

            <div class="col-md-6">
                <input id="password" type="password" class="form-control" name="password" required
                    autocomplete="current-password">
            </div>
        </div>
        {{-- <div class="form-group row mb-0">
            <div class="col-md-8 offset-md-4">
                <button type="submit" class="btn btn-primary">
                    Iniciar
                </button>
                <a href="{{route("register")}}">No tengo cuenta</a>
            </div>
        </div> --}}
    @endslot
@endcomponent
