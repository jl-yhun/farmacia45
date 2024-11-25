<div class="modal-header">
    <h5 class="modal-title">Realizar abono a reparación {{$reparacion->folio}}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="container-fluid">
        <div class="row">
            <div class="form-group col-12">
                <label for="">Monto a abonar</label>
                <input type="text" class="form-control onlynumbers" placeholder="Escriba el monto a abonar" name="monto">
                <input type="hidden" name="id" value="{{$reparacion->id}}">
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-primary" id="btn-realizar-abono">Realizar abono</button>
</div>