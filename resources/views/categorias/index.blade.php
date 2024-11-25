@extends('layouts.main')
@section('titulo', 'Categorías')
@section('contenido')
    @component('componentes.crud-index')
        @slot('titulo')
            Categorías
        @endslot
        @slot('permisoAgregar')
            categorias.creation
        @endslot
        @slot('urlAgregar')
            {{ route('categorias.create') }}
        @endslot
        @slot('tableHead')
            <th>Id</th>
            <th>Nombre</th>
            <th>Admite</th>
            <th>Tasa IVA</th>
            <th>Acciones</th>
        @endslot
        @slot('tableBody')
            @foreach ($categorias as $dato)
                <tr data-id="{{ $dato->id }}">
                    <td>{{ $dato->id }}</td>
                    <td>{{ $dato->nombre }}</td>
                    <td>{{ $dato->admite }}</td>
                    <td>{{ $dato->tasa_iva * 100 }}%</td>
                    <td>
                        @can('categorias.update')
                            <a data-cy="btn-update" class="btn btn-warning btnEditar modal-link" href="{{ route('categorias.edit', $dato) }}" size="md">
                                <i class="fa fa-edit"></i>
                            </a>
                        @endcan
                        @if ($dato->is_destroyable)
                            @can('categorias.delete')
                                <a class="btn btn-danger btnEliminar" data-cy="btn-destroy">
                                    <i class="fa fa-close"></i>
                                    <form class="delete-form" action="{{ route('categorias.destroy', $dato) }}" method="post"
                                        size="lg">
                                        {{ method_field('delete') }}
                                        {{ csrf_field() }}
                                    </form>
                                </a>
                            @endcan
                        @endif
                    </td>
                </tr>
            @endforeach
        @endslot
    @endcomponent
@endsection
@section('js')
    <script src="{{ mix('js/categorias.js') }}"></script>
@endsection
