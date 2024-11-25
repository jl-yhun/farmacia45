@extends('layouts.main')
@section('titulo', 'Gastos')
@section('contenido')
    @component('componentes.crud-index')
        @slot('titulo')
            Gastos
        @endslot
        @slot('modalSize')
            md
        @endslot
        @slot('tableHead')
            <th>Fecha</th>
            <th>Concepto</th>
            <th>Monto</th>
            <th>Fuente</th>
            @can('gastos.delete')
                <th>Acciones</th>
            @endcan
        @endslot
        @slot('tableBody')
            @foreach ($gastos as $dato)
                <tr data-id="{{ $dato->id }}">
                    <td>{{ $dato->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $dato->concepto }}</td>
                    <td>$ {{ $dato->monto }}</td>
                    <td>{{ $dato->fuente }}</td>
                    @can('gastos.delete')
                        <td>
                            <a data-cy="btn-destroy" class="btn btn-danger btnEliminar">
                                <i class="fa fa-close"></i>
                                <form class="delete-form" action="{{ route('gastos.destroy', $dato) }}" method="post" size="lg">
                                    {{ method_field('delete') }}
                                    {{ csrf_field() }}
                                </form>
                            </a>
                        </td>
                    @endcan
                </tr>
            @endforeach
        @endslot
    @endcomponent
@endsection
@section('js')
    <script src="{{ mix('js/gastos.js') }}"></script>
@endsection
