@if (Agent::isMobile() && !Agent::isTablet())
    <div class="modal-header">
        <h5 class="modal-title">Productos que coinciden {{ $busqueda }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <table class="table table-striped tabla-productos">
            <thead>
                <tr>
                    <th scope="col">Código</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $producto)
                    <tr class="producto" data-producto="{{ json_encode($producto) }}">
                        <th scope="row">{{ $producto->codigo_barras }}</th>
                        <td>{{ $producto->nombre }}
                            <br>
                            <sub>{{ $producto->descripcion }}</sub>
                        </td>
                        <td>{{ $producto->venta }}</td>
                        <td>{{ $producto->stock }}</td>
                    </tr>
                @empty
                    <tr>
                        <th>Ningún producto encontrado</th>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
<button type="button" class="btn btn-primary">Save changes</button> --}}
    </div>
@else
    @forelse ($productos as $producto)
        <tr class="producto" data-producto="{{ json_encode($producto) }}">
            <th scope="row">{{ $producto->codigo_barras }}</th>
            <td>{{ $producto->nombre }}
                <br>
                <sub>{{ $producto->descripcion }}</sub>
            </td>
            <td>{{ $producto->venta }}</td>
            <td>{{ $producto->stock }}</td>
        </tr>
    @empty
        <tr>
            <th colspan="4">Ningún producto encontrado</th>
        </tr>
    @endforelse
@endif
