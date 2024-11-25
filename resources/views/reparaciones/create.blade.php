@extends('layouts.main')
@section('titulo', "Reparaciones")
@section('css')
    <link rel="stylesheet" href="/css/bootstrap-datepicker.min.css">
@endsection
@section('contenido')
<div class="container-fluid">
    <form class="col-6 offset-3" id="form-reparaciones" action="{{route("reparaciones.store")}}" method="POST">
        {{csrf_field()}}
        <div class="row">
            <div class="col-12 form-group">
                <label for="">Cliente</label>
            <input type="text" name="cliente" value="{{old('cliente')}}" placeholder="Nombre del cliente..." class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="">No. de Teléfono</label>
                <input type="tel" name="telefono" value="{{old('telefono')}}" placeholder="Teléfono del cliente..." class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="">Marca</label>
                <input type="text" name="marca" value="{{old('marca')}}" placeholder="Marca del equipo..." class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="">Modelo</label>
                <input type="text" name="modelo" value="{{old('modelo')}}" placeholder="Modelo del equipo..." class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="">Falla</label>
                <textarea id="falla" class="form-control" rows="10" placeholder="Describa la falla...">{{old('falla')}}</textarea>
                <input type="hidden" name="falla">
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="">Fecha entrega</label>
                <input type="text" autocomplete="off" name="fecha_entrega_view" value="{{old('fecha_entrega_view')}}" placeholder="Fecha entrega..." class="form-control">
                <input type="hidden" name="fecha_entrega" value="{{old('fecha_entrega')}}">
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="">Costo</label>
                <input type="number" name="costo" value="{{old('costo')}}" placeholder="Costo..." class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="">Abono</label>
                <input type="number" name="abono" value="{{old('abono')}}" placeholder="Abono..." class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="">Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="10" placeholder="Observaciones...">{{old('observaciones')}}</textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" id="btnRegistrar">Registrar</button>
        <a href="{{route('reparaciones.index')}}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
@section('js')
<script src="/js/moment.min.js"></script>
<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/bootstrap-datepicker.es.min.js"></script>
<script src="/ckeditor/ckeditor.js"></script>
<script>
    $(document).ready(function(){
        $('input[name="fecha_entrega_view"]').datepicker({
            language: "es",
            startDate: new Date(),
            format: "dd MM yyyy"
        }).on("changeDate", function(e){
            $("input[name='fecha_entrega']").val(moment(e.date).format("YYYY-MM-DD"));
        });
        CKEDITOR.replace( 'falla' );
        $("#btnRegistrar").on("click", function(e){
            e.preventDefault();
            var url =$("#form-reparaciones").attr("action"); 
            $("input[name='falla']").val(CKEDITOR.instances.falla.getData());
            var data = $("#form-reparaciones").serialize();
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                success: function(res){
                    if(res.estado){
                        imprimirTicketReparacion(res);
                    }else{
                        var err = "<ul>";
                        for(var f = 0; f < res.errs.length; f++){
                            err += "<li>" + res.errs[f] + "</li>";
                        }
                        err += "</ul>";
                        alerta(err, "danger");
                    }
                }
            });
            
        });
    });
</script>
@endsection