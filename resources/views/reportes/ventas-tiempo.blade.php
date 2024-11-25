@extends('layouts.main')
@section('titulo', "Reporte de ventas en el tiempo")
@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
    #main {
        height: auto;
        margin-top: 20px;
        margin-bottom: 50px;
    }
</style>
@endsection
@section('contenido')
<div class="row">
    <div class="col-4 offset-8">
        <input type="text" name="fechas" class="form-control">
    </div>
</div>
<div class="row">
    <div class="col-3">
        <label for="">Ventas</label>
        <input type="text" class="form-control" readonly id="ventas">
    </div>
    <div class="col-3">
        <label for="">Reparaciones</label>
        <input type="text" class="form-control" readonly id="reparaciones">
    </div>
    <div class="col-3">
        <label for="">Utilidad</label>
        <input type="text" class="form-control" readonly id="utilidad">
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-striped" id="reporte">
            <thead>
                <tr>
                    <th>Monto inicial</th>
                    <th>Ventas</th>
                    <th>Utilidades</th>
                    <th>Reparaciones</th>
                    <th>Apertura</th>
                    <th>Cierre</th>
                </tr>
            </thead>
            <tbody class="cuerpo">

            </tbody>
        </table>
    </div>
</div>
<input type="hidden" id="urlReportar" value="{{route("ventas-tiempo.reportar")}}">
<input type="hidden" id="urlVenta" value="{{route("ventas.show")}}">
@endsection
@section('js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="/js/reporte-ventas-tiempo.js?v={{config("app.version")}}"></script>
@endsection