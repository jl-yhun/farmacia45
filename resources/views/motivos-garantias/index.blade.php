@extends('layouts.main')
@section('titulo', 'Motivos Garantías')
@section('contenido')
    @component('componentes.crud-index')
        @slot('titulo')
            Motivos Garantías
        @endslot
        @slot('urlAgregar')
            {{ route('motivos-garantias.create') }}
        @endslot
        @slot('tableHead')
            <th>Id</th>
            <th>Nombre</th>
            <th>Acciones</th>
        @endslot
        @slot('tableBody')
            @foreach ($motivos_garantias as $dato)
                <tr data-id="{{ $dato->id }}">
                    <td>{{ $dato->id }}</td>
                    <td>{{ $dato->nombre }}</td>
                    <td>
                        <a class="btn btn-warning btnEditar modal-link" href="{{ route('motivos-garantias.edit', $dato) }}"
                            size="md">
                            <i class="fa fa-edit"></i>
                        </a>
                        @can('motivos-garantias.delete')
                            <a class="btn btn-danger btnEliminar">
                                <i class="fa fa-close"></i>
                                <form class="delete-form" action="{{ route('motivos-garantias.destroy', $dato) }}" method="post"
                                    size="lg">
                                    {{ method_field('delete') }}
                                    {{ csrf_field() }}
                                </form>
                            </a>
                        @endcan
                    </td>
                </tr>
            @endforeach
        @endslot
    @endcomponent
@endsection
@section('js')
    <script src="{{mix("js/motivos-garantias.js")}}"></script>
@endsection
