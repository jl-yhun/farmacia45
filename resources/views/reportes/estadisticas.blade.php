@extends('layouts.main')
@section('titulo', 'Estadísticas')
@section('css')
    <link rel="stylesheet" href="{{ mix('/css/estadisticas.css') }}">
@endsection
@section('contenido')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <h5 class="col-10">
                            Estadísticas
                        </h5>
                        <div class="col d-flex align-items-center">
                            <label for="" class="mb-0"><strong>Año:</strong>&nbsp;</label>
                            <select name="" id="years" class="form-control">
                                <option>2021</option>
                                <option>2022</option>
                                <option>2023</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tiempo" role="tab">
                                Ventas en el tiempo
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#vendedor" role="tab">
                                Ventas por vendedor
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#producto" role="tab">
                                Ventas por producto
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tiempo" role="tabpanel">
                            @include('reportes.partials.ventas-tiempo')
                        </div>
                        <div class="tab-pane fade" id="vendedor" role="tabpanel">
                            @include('reportes.partials.ventas-vendedor')
                        </div>
                        <div class="tab-pane fade" id="producto" role="tabpanel">
                            @include('reportes.partials.ventas-producto')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ mix('/js/estadisticas.js') }}"></script>
@endsection
