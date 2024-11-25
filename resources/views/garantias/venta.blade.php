@component('componentes.modal-form')
    @slot('titulo')
        Productos de la venta # {{ $venta->id }}
        {{ $venta->garantiaAplicada ? '(garantía aplicada previamente)' : ($venta->hasGarantia ? '' : '(garantía expirada)') }}
    @endslot
    @slot('modalBody')
        <table id="table" class="table display nowrap" style="width: 100%;">
            <thead>
                <tr>
                    <th>Seleccione</th>
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
                        <td class="text-center">
                            <input type="radio" value="{{ urlencode($producto->toJson()) }}" name="selection"
                                {{ ($venta->hasGarantia 
                                   && !$venta->garantiaAplicada 
                                   && !in_array($producto->id, $venta->productosReclamados)) ? '' : 'disabled' }}>
                        </td>
                        <td>{{ $producto->id }}</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->pivot->cantidad }}</td>
                        <td>$ {{ $producto->pivot->venta }}</td>
                        <td>$ {{ $producto->pivot->cantidad * $producto->pivot->venta }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endslot
@endcomponent
