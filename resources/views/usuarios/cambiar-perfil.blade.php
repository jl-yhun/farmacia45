@component('componentes.modal-detalle')
    @slot('titulo')
        Cambiar perfil
    @endslot
    @slot('modalBody')
        <div class="container-fluid panel-overflow" style="cursor: pointer;">
            @foreach ($usuarios as $usuario)
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body item-usuario" data-id="{{ $usuario->id }}">
                                {{ $usuario->name }} ({{ $usuario->email }})
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endslot
@endcomponent
