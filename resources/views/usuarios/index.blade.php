@extends('layouts.main')
@section('titulo', 'Usuarios')
@section('contenido')
    @component('componentes.crud-index')
        @slot('titulo')
            Usuarios
        @endslot
        @slot('urlAgregar')
            {{ route('usuarios.create') }}
        @endslot
        @slot('tableHead')
            <th>Folio</th>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Acciones</th>
        @endslot
        @slot('tableBody')
            @foreach ($usuarios as $dato)
                <tr data-id="{{ $dato->id }}">
                    <td>{{ $dato->id }}</td>
                    <td>{{ $dato->name }}</td>
                    <td>{{ $dato->email }}</td>
                    <td>
                        <a class="btn btn-warning btnEditar modal-link" data-toggle="tooltip" title="Editar"
                            href="{{ route('usuarios.edit', $dato) }}" size="md">
                            <i class="fa fa-edit"></i>
                        </a>
                        @can('usuarios.delete')
                            <a class="btn btn-danger btnEliminar" data-toggle="tooltip" title="Eliminar">
                                <i class="fa fa-close"></i>
                                <form class="delete-form" action="{{ route('usuarios.destroy', $dato) }}" method="post" size="lg">
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
    <script src="{{mix("/js/usuarios.js")}}"></script>
@endsection
