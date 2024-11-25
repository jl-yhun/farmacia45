@extends('layouts.main')
@section('titulo', 'Faltantes')
@section('contenido')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <h5 class="col">Faltantes</h5>
            </div>
        </div>

        <div class="card-body p-0" id="ordenes-compra-faltantes-app">

        </div>
    </div>
@endsection
@section('js')
    <script src="{{ mix('js/ordenes-compra-faltantes-app.js') }}"></script>
@endsection
