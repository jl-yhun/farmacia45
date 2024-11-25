@extends('layouts.main')
@section('titulo', 'Pérdidas')
@section('contenido')
    @component('componentes.crud-index')
        @slot('titulo')
            Pérdidas
        @endslot
        @if (_c('ESTADO_CAJA') == 'abierta')
            @slot('urlAgregar')
                {{ route('perdidas.create') }}
            @endslot
        @endif
        @slot('permisoAgregar')
            perdidas.creation
        @endslot
        @slot('modalSize')
            md
        @endslot
        @slot('tableHead')
            <th>Folio</th>
            <th>Producto</th>
            <th>Motivo</th>
            {{-- <th>Acciones</th> --}}
        @endslot
        @slot('tableBody')
            @foreach ($perdidas as $dato)
                <tr data-id="{{ $dato->id }}">
                    <td>{{ $dato->id }}</td>
                    <td>{{ $dato->producto->nombre }}</td>
                    <td>
                        {{-- @if ($dato->garantia)
                            <a href="{{ route('garantias.resolucion', $dato->garantia) }}" class="modal-link" size="lg"
                                data-toggle="tooltip" title="Ver resolución">
                                <i class="fa fa-eye"></i>
                            </a>
                        @endif --}}
                        {{ $dato->motivo }}
                    </td>
                </tr>
            @endforeach
        @endslot
    @endcomponent
    <input type="hidden" id="ruta-productos" value="{{ route('perdidas.productos') }}">
@endsection
@section('js')
    <script src="{{ mix('/js/perdidas.js') }}"></script>
@endsection
