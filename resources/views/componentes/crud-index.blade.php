{{-- ******************************************
     urlAgregar: route para agregar un nuevo valor
     modalSize:  tamaño del modal, default: null
     tableHead:  encabezado de la tabla
     tableBody:  cuerpo de la tabla, default: '' (Server Side)
     tableFoot:  pie de la tabla, default: tableHead 

     ****************************************** --}}
<div class="card">
    <div class="card-header">
        <div class="row">
            <h5 class="col">{{ $titulo }}</h5>
            @if ($tools ?? false)
                <div class="col-4 float-end">
                    {{ $tools }}
                </div>
            @endif
            @if ($urlAgregar ?? false)
                @can(isset($permisoAgregar) ? $permisoAgregar->toHtml() : null)
                    <a class="btn btn-success float-right modal-link" data-toggle="tooltip" id="btn-agregar" title="Añadir"
                        href="{{ $urlAgregar }}" 
                        size="{{ $modalSize ?? null }}"
                        data-cy="btn-create">
                        <i class="fa fa-plus"></i>
                    </a>
                @endcan
            @endif
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table display" id="table" style="width:100%">
                <thead>
                    <tr>
                        {{ $tableHead }}
                    </tr>
                </thead>
                <tbody>
                    {{ $tableBody ?? '' }}
                </tbody>
                <tfoot>
                    {{ $tableFoot ?? $tableHead }}
                </tfoot>
            </table>
        </div>
    </div>
</div>
{{-- Esta clase es para habilitar el scroll si estamos en un celular --}}
@section('body-class', 'lov')
