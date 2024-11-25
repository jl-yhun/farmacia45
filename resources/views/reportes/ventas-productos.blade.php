@extends('layouts.main')
@section('titulo', 'Reporte de ventas')
@section('css')
    <link rel="stylesheet" href="/css/reportes.css">
@endsection
@section('contenido')
    <div class="row">
        <div class="col-12">
            @component('componentes.crud-index')
                @slot('titulo')
                    Reporte de ventas por producto
                @endslot
                @slot('tools')
                    <div class="form-inline">
                        <button class="btn btn-primary" id="btnExport">
                            <span class="material-icons">file_download</span>                            
                        </button>
                        &nbsp;&nbsp;
                        <label for="">Fecha</label><input type="text" name="fechas" class="ml-sm-2 w-75 form-control">
                    </div>
                @endslot
                @slot('tableHead')
                    <th>Folio</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>U. Vendidas</th>
                    <th>Compra</th>
                    <th>Venta</th>
                    <th>Compra Total</th>
                    <th>Venta Total</th>
                    <th>Utilidades</th>
                @endslot
            @endcomponent
        </div>
    </div>
    <input type="hidden" id="urlReportar" value="{{ route('ventas-productos.reportar') }}">
@endsection
@section('js')
    <script src="{{ mix('/js/reporte-ventas-productos.js') }}"></script>
@endsection
