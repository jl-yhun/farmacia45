@extends('layouts.main')
@section('titulo', 'Reporte de ventas')
@section('contenido')
    <div id="ventas-app" data-params="{{ base64_encode(json_encode(Request::query())) }}"></div>
@endsection
@section('js')
    <script src="{{ mix('js/ventas-app.js') }}"></script>
@endsection
