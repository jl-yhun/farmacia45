@extends('layouts.main')
@section('titulo', 'Productos')
@section('contenido')
    <div id="productos-app" data-params="{{ base64_encode(json_encode(Request::query())) }}"></div>
@endsection
@section('js')
    <script src="{{ mix('js/productos-app.js') }}"></script>
@endsection
