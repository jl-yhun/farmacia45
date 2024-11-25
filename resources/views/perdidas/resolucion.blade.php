@component('componentes.modal-detalle')
    @slot('titulo')
        Resolución de garantía / devolución
    @endslot
    @slot('modalBody')
        <table class="table table-striped">
            <tbody>
                <tr>
                    <th>Se aplicó</th>
                    <td>{{ $garantia->tipo }}</td>
                </tr>
                <tr>
                    <th>Motivo</th>
                    <td>{{ $garantia->motivo->nombre ?? 'DESCONOCIDO' }}</td>
                </tr>
                <tr>
                    <th>Venta</th>
                    <td>
                        <a href="{{ route('ventas.show', $garantia->venta) }}" class="modal-link" size='lg'>
                            # {{ $garantia->venta_id }} el {{ $garantia->venta->created_at }}
                        </a>
                    </td>
                </tr>
                <tr>
                    <th>Producto devuelto</th>
                    <td>
                        {{ $garantia->producto_devuelto->nombre }}
                    </td>
                </tr>
                @if ($garantia->tipo != 'DEVOLUCIÓN DE DINERO')
                    <tr>
                        <th>Productos nuevos</th>
                        <td>
                            <div class="row">
                                <div class="col-6 text-center">
                                    <b>Cantidad</b>
                                </div>
                                <div class="col-6 text-center">
                                    <b>Producto</b>
                                </div>
                            </div>
                            @foreach ($garantia->productos_nuevos as $prod)
                                <div class="row">
                                    <div class="col-6 text-center">
                                        {{ $prod->pivot->cantidad }}
                                    </div>
                                    <div class="col-6 text-center">
                                        {{ $prod->nombre }}
                                    </div>
                                </div>
                            @endforeach
                        </td>
                    </tr>
                @endif
                <tr>
                    <th>Fecha / Hora</th>
                    <td>
                        {{ $garantia->created_at->format('Y-m-d H:i') }}
                    </td>
                </tr>
            </tbody>
        </table>
    @endslot
@endcomponent
