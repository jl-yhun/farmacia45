@component('componentes.modal-detalle')
    @slot('titulo')
        Conteo de monedas y billetes
    @endslot
    @slot('modalBody')
        <div class="row">
            <div class="col-sm-4">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <h4>Billetes</h4>
                        </div>
                    </div>
                    @foreach ($valores['billetes'] as $valor)
                        <div class="row">
                            <div class="col">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">$ {{ $valor }}</span>
                                    </div>
                                    <input type="text" name="_{{ $valor }}" value="0"
                                        class="form-control onlynumbers conteo text-right">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-sm-4">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <h4>Monedas</h4>
                        </div>
                    </div>
                    @foreach ($valores['monedas'] as $valor)
                        <div class="row">
                            <div class="col">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">$ {{ $valor }}</span>
                                    </div>
                                    <input type="text" name="_{{ $valor }}" value="0"
                                        class="form-control onlynumbers conteo text-right">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-sm-4">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <h4>Centavos</h4>
                        </div>
                    </div>
                    @foreach ($valores['centavos'] as $valor)
                        <div class="row">
                            <div class="col">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">$ {{ $valor }}</span>
                                    </div>
                                    <input type="text" name="_{{ $valor }}" value="0"
                                        class="form-control onlynumbers conteo text-right">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-8">
                <h2>Total</h2>
            </div>
            <div class="col-4 text-right">
                <h2 id="total">$0</h2>
            </div>
        </div>
    @endslot
@endcomponent
<script src="{{mix("/js/conteo.js")}}"></script>
