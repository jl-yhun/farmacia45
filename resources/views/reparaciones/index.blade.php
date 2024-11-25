@extends('layouts.main')
@section('titulo', "Reparaciones")
@section('contenido')
@if(_c("ESTADO_CAJA") == "cerrada")
<div class="notificacion alert alert-warning alert-dismissible" role="alert">
    <span class="mensaje">No podrá abonar a ninguna reparación sin abrir la caja</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
<div class="container-fluid">
    <div class="row">
        <div class="col-3">
            <a class="btn btn-success btn-block" id="btnAgregar" href="{{route("reparaciones.create")}}">Agregar</a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-striped table-bordered" id="tabla-reparaciones" style="width:100%">
                <thead>
                    <tr>
                        <th scope="col">Folio</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Teléfono</th>
                        <th scope="col">Marca</th>
                        <th scope="col">Modelo</th>
                        <th scope="col">Costo</th>
                        <th scope="col">Abono</th>
                        <th scope="col">Fecha</th>
                        {{-- <th scope="col">Entrega</th> --}}
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reparaciones as $dato)
                    <tr>
                        <td>{{$dato->folio}}</td>
                        <td>{{$dato->cliente}}</td>
                        <td><a href="tel:{{$dato->telefono}}">{{$dato->telefono}}</a></td>
                        <td>{{$dato->marca}}</td>
                        <td>{{$dato->modelo}}</td>
                        <td>{{$dato->costo == -1 ? "Pendiente": "$". number_format($dato->costo, 2)}}</td>
                        <td>{{$dato->abono == -1 ? "Ninguno": "$". number_format($dato->abono, 2)}}</td>
                        <td data-sort="{{strtotime($dato->created_at->format("d-m-Y H:i"))}}">{{$dato->created_at->format("d-m-Y H:i")}}</td>
                        {{-- <td>{{$dato->fecha_entrega->format("d-m-Y")}}</td> --}}
                        <td>
                            <a class="btn btn-info" data-toggle="tooltip" title="Editar"
                                href="{{route('reparaciones.edit', ["id" => $dato->id])}}">
                                <i class="fa fa-edit"></i>
                            </a>
                            @if($dato->costo != -1 && $dato->costo != $dato->abono && _c("ESTADO_CAJA") == "abierta")
                            <a class="btn btn-danger btnRegistrarAbono" data-toggle="tooltip" title="Registrar abono"
                                href="{{route('reparaciones.abonar', ["id" => $dato->id])}}">
                                <i class="fa fa-money"></i>
                            </a>
                            @endif
                            @if($dato->costo == $dato->abono)
                            <a class="btn btn-success btnEntregar" data-toggle="tooltip" title="Marcar como entregado"
                                data-id="{{$dato->id}}" href="{{route('reparaciones.entregar', ["id" => $dato->id])}}">
                                <i class="fa fa-check"></i>
                            </a>
                            @endif
                            <a class="btn btn-warning btnReimprimir" data-toggle="tooltip" title="Reimprimir ticket"
                                data-id="{{$dato->id}}">
                                <i class="fa fa-print"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th scope="col">Folio</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Teléfono</th>
                        <th scope="col">Marca</th>
                        <th scope="col">Modelo</th>
                        <th scope="col">Costo</th>
                        <th scope="col">Abono</th>
                        <th scope="col">Fecha</th>
                        {{-- <th scope="col">Entrega</th> --}}
                        <th scope="col">Acciones</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<input type="hidden" id="urlReparacion" value="{{route('reparaciones.show')}}">
<input type="hidden" id="urlRealizarAbono" value="{{route('reparaciones.realizarAbono')}}">
{{-- <input type="hidden" id="ruta-editar-nombre" value="{{route('productos.editar-nombre')}}">
<input type="hidden" id="ruta-editar-stock" value="{{route('productos.editar-stock')}}"> --}}
@endsection
@section('js')
<script src="/js/es.js?v={{config("app.version")}}"></script>
<script src="/js/reparaciones.js?v={{config("app.version")}}"></script>
@endsection