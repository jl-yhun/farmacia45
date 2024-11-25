@component('componentes.modal-form')
    @slot('titulo')
        Productos que coinciden
    @endslot
    @slot('modalBody')
        <table id="table" class="table display nowrap" style="width: 100%;">
            <thead>
                <tr>
                    <th>Seleccione</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productos as $producto)
                    <tr>
                        <td class="text-center"><input type="radio" value="{{ urlencode($producto->toJson()) }}"
                                name="selection"></td>
                        <td>{{ $producto->codigo_barras }}</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->descripcion }}</td>
                        <td>$ {{ $producto->venta }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endslot
@endcomponent
