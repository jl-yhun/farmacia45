@component('componentes.modal-detalle')
    @slot('titulo')
        Detalle de la venta #{{ $venta->id }}
    @endslot
    @slot('modalBody')
        <div class="container-fluid">
            <div class="row">
                <div class="col-4">
                    Vendedor
                </div>
                <div class="col-8">
                    {{ $venta->usuario->name }}
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    Fecha
                </div>
                <div class="col-8">
                    {{ $venta->created_at->format('Y-m-d H:i') }}
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    Total
                </div>
                <div class="col-8">
                    $ {{ $venta->total }}
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    Se pagó con
                </div>
                <div class="col-8">
                    $ {{ $venta->denominacion }}
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="text-center">Productos</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <table id="table" class="table display nowrap" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Nombre</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($venta->productos as $producto)
                                <tr>
                                    <td>{{ $producto->id }}</td>
                                    <td>{{ $producto->nombre }}</td>
                                    <td>{{ $producto->pivot->cantidad }}</td>
                                    <td>$ {{ $producto->pivot->venta }}</td>
                                    <td>$ {{ $producto->pivot->cantidad * $producto->pivot->venta }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endslot
@endcomponent
