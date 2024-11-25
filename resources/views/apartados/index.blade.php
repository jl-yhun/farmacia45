@extends('layouts.main')
@section('titulo', 'Apartados')
@section('contenido')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <h5 class="col">Apartados</h5>
            </div>
        </div>

        <div class="card-body" id="apartados-app">
            
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ mix('js/apartados-app.js') }}"></script>
@endsection
