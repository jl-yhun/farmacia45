@extends('layouts.main')
@section('titulo', 'Aperturas Caja')
@section('contenido')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <h5 class="col">Aperturas Caja</h5>
                {{-- @can('')
                    <a class="btn btn-success float-right modal-link" data-toggle="tooltip" id="btn-agregar" title="Añadir"
                        href="{{ $urlAgregar }}" size="{{ $modalSize ?? null }}" data-cy="btn-create">
                        <i class="fa fa-plus"></i>
                    </a>
                @endcan --}}
            </div>
        </div>

        <div class="card-body" id="aperturas-caja-app">

        </div>
    </div>
@endsection
@section('js')
    <script src="{{ mix('js/aperturas-caja-app.js') }}"></script>
@endsection
