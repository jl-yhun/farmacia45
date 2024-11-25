@extends('errors::minimal')

@section('title', 'No autorizado.')
@section('message')
    No autorizado |
    <a href="{{ route('punto-venta') }}">Ir al login</a>
@endsection
