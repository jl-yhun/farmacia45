@extends('layouts.main')
@section('titulo', 'Órdenes de compra')
@section('contenido')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <h5 class="col">Órdenes de compra</h5>
            </div>
        </div>

        <div class="card-body p-0" id="ordenes-compra-app">

        </div>
    </div>
@endsection
@section('js')
    <script src="{{ mix('js/ordenes-compra-app.js') }}"></script>
@endsection