{{-- ******************************************
     urlAction: route para cuando se hace submit del form
     method:  PUT or DELETE, method_field, default: ''
     titulo:  Título del Modal
     modalBody:  Cuerpo del modal (formulario)

     ****************************************** --}}
@if ($errors->any())
    <div class="notificacion alert alert-danger alert-dismissible" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
<form action="{{ $urlAction ?? '' }}" method="POST">
    {{ csrf_field() }}
    {{ $method ?? '' }}
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">{{ $titulo }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="container">
            {{ $modalBody }}
        </div>
    </div>
    <div class="modal-footer">
        <button data-cy="btn-cancelar" type="button" class="btn btn-danger" data-dismiss="modal">{{strtoupper("Cancelar")}}</button>
        <button data-cy="btn-ok" type="submit" class="btn btn-success {{ $customAction ?? 'btn-ok' }}">{{ strtoupper($botonOk ?? 'Ok') }}<i
                class="fa fa-check-circle"></i> </button>
    </div>
</form>
