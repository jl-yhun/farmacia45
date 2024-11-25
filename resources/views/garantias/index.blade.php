@extends('layouts.main')
@section('titulo', 'Garantías')
@section('contenido')
    @component('componentes.crud-index')
        @slot('titulo')
            Garantías / Devoluciones / Cancelaciones
        @endslot
        @if (_c('ESTADO_CAJA') == 'abierta')
            @slot('urlAgregar')
                {{ route('garantias.create') }}
            @endslot
        @endif
        @slot('modalSize')
            lg
        @endslot
        @slot('tableHead')
            <th>Folio</th>
            <th>Tipo</th>
            <th>Producto devuelto</th>
            <th>Resolución</th>
            <th>Diferencia</th>
            <th>Acciones</th>
        @endslot
        @slot('tableBody')
            @foreach ($garantias as $dato)
                <tr data-id="{{ $dato->id }}">
                    <td>{{ $dato->id }}</td>
                    <td>{{ $dato->tipo }}</td>
                    <td>
                        @if ($dato->tipo == 'CANCELACIÓN')
                            <ul class="g-0">
                                @foreach ($dato->venta->productos as $producto)
                                    <li>{{ $producto->pivot->cantidad . ' ' . $producto->nombre }}</li>
                                @endforeach
                            </ul>
                        @else
                            {{ $dato->producto_devuelto->nombre }}
                        @endif
                    </td>
                    <td class="text-center">
                        {{-- <a href="{{ route('garantias.resolucion', $dato) }}" class="modal-link" size="lg" data-toggle="tooltip"
                            title="Ver resolución">
                            <i class="fa fa-eye"></i>
                        </a> --}}
                    </td>
                    <td>{{ $dato->diferenciaStr }}
                    </td>
                    <td>
                        <button class="btn btn-info btn-imprimir" data-toggle="tooltip" data-id="{{ $dato->id }}"
                            title="Reimprimir ticket">
                            <i class="fa fa-print"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        @endslot
    @endcomponent
@endsection
@section('js')
    <script src="{{ mix('/js/garantias.js') }}"></script>
@endsection
