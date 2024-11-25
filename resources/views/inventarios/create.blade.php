@extends('layouts.main')
@section('titulo', 'Re-inventario físico')
@section('contenido')
    <div id="reinventario-app"></div>
@endsection
@section('js')
    <script src="{{ mix('js/reinventario-app.js') }}"></script>
@endsection
