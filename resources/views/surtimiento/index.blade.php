@extends('layouts.main')
@section('titulo', "Resurtimiento")
@section('css')
@endsection
@section('contenido')
<div class="row">
    <div class="col-4">
        <button class="btn btn-success" id="btn-guardar">Aceptar</button>
        <button class="btn btn-warning" id="btn-readjuntar">Adjuntar otra vez</button>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-striped tabla-resurtimiento">
            <thead>
                <tr>
                    <th scope="col">Folio</th>
                    <th scope="col">Nombre</th>
                    @auth
                    <th>Compra</th>
                    <th>Venta</th>
                    @endauth
                    <th scope="col">Cantidad entrada</th>
                    <th scope="col">Stock final</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datos as $dato)
                <tr>
                    <td>{{$dato["folio"] ?? "Nuevo"}}</td>
                    <td>{{$dato["nombre"]}}</td>
                    @auth
                    <td>{{$dato["compra"]}}</td>
                    <td>{{$dato["venta"]}}</td>
                    @endauth
                    <td>{{$dato["cantidad"]}}</td>
                    <td>{{$dato["stock"]}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<input type="hidden" value="{{json_encode($datos)}}" id="productos">
@endsection
@section('js')
<script>
    $("#btn-guardar").on("click", function(){
        $.ajax({
            type: "post",
            url: '{{route("surtimiento-guardar")}}',
            data: JSON.stringify({
                "productos": JSON.parse($("#productos").val())
            }),
            contentType: "application/json",
            success: function(res){
                location.href = '{{route("punto-venta")}}';
            }
        });
    });
</script>
@endsection
