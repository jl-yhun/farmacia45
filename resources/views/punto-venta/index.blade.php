@extends('layouts.main')
@section('titulo', 'Punto de venta')
@section('css')
    <link rel="stylesheet" href="{{ mix('css/pdv.css') }}">
@endsection
@section('contenido')
    <div class="ptv-content">
        @if (_c('ESTADO_CAJA') == 'cerrada')
            <div class="alert-caja-cerrada alert alert-info mb-2 py-sm-2 py-1 align-self-center">
                <h6 class="text-center">La caja está cerrada y no podrá realizar ventas</h2>
            </div>
        @endif
        <div class="busqueda mx-4">
            <div class="d-flex mt-1">
                <div class="w-75">
                    <input type="text" autocomplete="off" data-cy="txt-busqueda-ptv" class="form-control h-100"
                        id="busqueda" placeholder="Búsqueda" autofocus>
                </div>
                <div class="w-25 ml-3">
                    <button data-cy="btn-buscar-ptv" class="btn btn-block btn-secondary h-100" id="btnBuscar">
                        <i class="material-icons">
                            search
                        </i>
                    </button>
                </div>
            </div>
            <div data-cy="div-resultados-ptv" class="resultados-container d-none d-md-block mt-1">
                <table class="table table-striped text-center" id="resultados">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div data-cy="div-cuenta-ptv" class="cuenta">
            {{-- @if ($cierre)
                @include('punto-venta.cierre-caja')
            @endif --}}
        </div>
        <div class="comandos">
            <button data-cy="btn-reimprimir-venta-ptv"
                class="btn btn-info d-flex flex-column justify-content-center btn-md rounded-0 btn-imprimir-ultima"
                data-placement="right" data-toggle="tooltip" title="Reimprimir último ticket de VENTA">
                <div class="d-flex justify-content-around w-100">
                    <i class="fa fa-print align-self-center mr-auto"></i> Venta
                </div>
            </button>
            <button data-cy="btn-reimprimir-corte-ptv"
                class="btn btn-warning d-flex flex-column justify-content-center btn-md rounded-0 btn-imprimir-ultimo-corte"
                data-placement="right" data-toggle="tooltip" title="Reimprimir último ticket de CORTE">
                <div class="d-flex justify-content-around w-100">
                    <i class="fa fa-print align-self-center mr-auto"></i> Corte
                </div>
            </button>
            <a data-cy="btn-registrar-movimiento-ptv" size="md" href="{{ route('movimientos.create') }}"
                class="btn btn-dark {{ _c('ESTADO_CAJA') == 'cerrada' ? 'disabled' : '' }} d-flex flex-column modal-link justify-content-center btn-md rounded-0"
                data-placement="right" data-toggle="tooltip" title="Registrar gasto">
                <div class="d-flex justify-content-around">
                    <i class="fa fa-exchange align-self-center mr-auto"></i> Movimiento
                </div>
            </a>
            {{-- <a data-cy="btn-registrar-transferencia-ptv" size="md" href="{{ route('transferencias.create') }}"
                class="btn btn-secondary {{ _c('ESTADO_CAJA') == 'cerrada' ? 'disabled' : '' }} d-flex flex-column modal-link justify-content-center btn-md rounded-0"
                data-placement="right" data-toggle="tooltip" title="Registrar transferencia">
                <div class="d-flex justify-content-around">
                    <i class="fa fa-exchange align-self-center mr-auto"></i> Transferencia
                </div>
            </a> --}}
            <a data-cy="btn-registrar-recarga-ptv" size="md" href="{{ route('pago-servicios.recargas') }}"
                class="btn btn-primary {{ _c('ESTADO_CAJA') == 'cerrada' ? 'disabled' : '' }} d-flex flex-column modal-link justify-content-center btn-md rounded-0"
                data-placement="right" data-toggle="tooltip" title="Registrar recarga telefónica">
                <div class="d-flex justify-content-around">
                    <i class="fa fa-volume-control-phone align-self-center mr-auto"></i> Recarga
                </div>
            </a>
            <a data-cy="btn-registrar-servicio-ptv" size="md" href="{{ route('pago-servicios.servicios') }}"
                class="btn btn-danger {{ _c('ESTADO_CAJA') == 'cerrada' ? 'disabled' : '' }} d-flex flex-column modal-link justify-content-center btn-md rounded-0"
                data-placement="right" data-toggle="tooltip" title="Registrar pago de servicio">
                <div class="d-flex justify-content-around">
                    <i class="fa fa-credit-card align-self-center mr-auto"></i> Servicio
                </div>
            </a>
            <a data-cy="btn-cobrar-ptv" href="#"
                class="btn btn-success {{ _c('ESTADO_CAJA') == 'cerrada' ? 'disabled' : '' }} d-flex flex-column justify-content-center btn-md rounded-0 btn-cobrar"
                data-placement="right" data-toggle="tooltip" title="Realizar cobro">
                <div class="d-flex justify-content-around">
                    <i class="fa fa-dollar align-self-center mr-auto"></i> Cobrar (F10)
                </div>
            </a>
            <div data-cy="div-total-cuenta-ptv" class="total-cuenta bg-dark d-flex flex-column justify-content-center">
                <b class="align-self-center">Total</b>
                <b class="align-self-center">$0.00</b>
            </div>
        </div>
    </div>
    <input type="hidden" id="ruta-buscar" value="{{ route('productos.buscar') }}">
    <input type="hidden" id="ruta-cobrar" value="{{ route('ventas.create') }}">
    <input type="hidden" id="ruta-descuento" value="{{ route('punto-venta.descuento') }}">
    <input type="hidden" id="ruta-reimprimir-ultimo-corte" value="{{ route('punto-venta.reprint-last') }}">
    <input type="hidden" id="ruta-reimprimir-ultima-venta" value="{{ route('ventas.reprint-last') }}">
    {{-- @include('layouts.modal-pago') --}}
@endsection
@section('js')
    <script src="{{ mix('js/pdv.js') }}"></script>
@endsection
