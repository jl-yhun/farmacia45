@component('componentes.modal-form')
    @slot('urlAction')
        {{ route('garantias.store') }}
    @endslot
    @slot('titulo')
        Aplicar Garantía / Devolución
    @endslot
    @slot('modalBody')
        <input type="hidden" name="usuario_id" value="{{ auth()->user()->id }}">
        <div class="row">
            <div class="form-group col-12 col-sm-4">
                <label for="tipo">¿Qué es lo que aplica?</label>
                <select name="tipo" class="form-control">
                    <option value="">--Seleccione--</option>
                    <option {{ old('tipo', '') == 'GARANTÍA' ? 'selected' : '' }}>GARANTÍA</option>
                    <option {{ old('tipo', '') == 'CAMBIO' ? 'selected' : '' }}>CAMBIO</option>
                    <option {{ old('tipo', '') == 'CANCELACIÓN' ? 'selected' : '' }}>CANCELACIÓN</option>
                    <option {{ old('tipo', '') == 'DEVOLUCIÓN DE DINERO' ? 'selected' : '' }}>DEVOLUCIÓN DE DINERO</option>
                </select>
            </div>
            {{-- Se elimina campo "motivo garantía", lo que aparece aquí 
                 deberá ir en observaciones  --}}
            <input type="hidden" name="perdida" value="{{ old('perdida', '0') }}">
            {{-- Ahora esto se llenará de forma automática --}}
        </div>
        <div class="row">
            <div class="form-group col-sm" id="folio_venta">
                <label for="venta_id">Folio de la venta</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" {{ old('tipo', '') != '' ? '' : 'readonly' }}
                        value="{{ old('venta_id', '') }}" name="venta_id" autocomplete="off">
                    <div class="input-group-append {{ old('tipo', '') == 'CANCELACIÓN' ? 'd-none' : '' }}">
                        <span class="input-group-text pointer btn-search-venta" id="basic-addon2">
                            <i class="fa fa-search"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group col-12 col-sm-5 {{ old('tipo', '') == 'CANCELACIÓN' ? 'd-none' : '' }}" id="producto">
                <label for="venta_id">Producto a devolver / cambiar / cancelar</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" value="{{ old('producto_nombre', '') }}" name="producto_nombre"
                        readonly>
                    <input type="hidden" name="producto_id" value="{{ old('producto_id', '') }}">
                    <input type="hidden" name="producto_admite" value="{{ old('producto_admite', '') }}">
                    <input type="hidden" name="diferencia" value="{{ old('diferencia', 0) }}">
                    <input type="hidden" name="apertura_caja_id" value="{{ getAperturaCajaIfExist() }}">
                </div>
            </div>
            <div class="form-group col-12 col-sm-4 {{ old('tipo', '') == 'CANCELACIÓN' ? 'd-none' : '' }}" id="producto_monto">
                <label for="venta_id">Monto gastado</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text pointer" id="basic-addon2">
                            <i class="fa fa-dollar"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control" name="producto_monto" value="{{ old('producto_monto', '') }}"
                        readonly>
                </div>
            </div>
        </div>
        <div class="{{ old('tipo', '') == 'GARANTÍA' || old('tipo', '') == 'CAMBIO' ? '' : 'd-none' }}"
            id="cambios_garantias">
            <div class="row">
                <div class="form-group col-sm-6">
                    <label for="producto_id">Nuevo(s) producto(s)</label>
                    <div class="input-group mb-3">
                        <input type="text" name="nuevo_producto" value="{{ old('nuevo_producto', '') }}" autocomplete="off"
                            {{ old('venta_id', '') != '' ? '' : 'readonly' }} class="form-control">
                        <div class="input-group-prepend">
                            <span class="input-group-text pointer btn-search-producto" id="basic-addon2">
                                <i class="fa fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <table id="tabla_nuevos_productos" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Monto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (old('productos'))
                                @for ($r = 0; $r < count(old('productos')); $r++)
                                    <tr producto-id="{{ old('productos')[$r]['id'] }}">
                                        <input type='hidden' name='productos[{{ $r }}][id]'
                                            value='{{ old('productos')[$r]['id'] }}' />
                                        <td>{{ old('productos')[$r]['nombre'] }}</td>
                                        <input type='hidden' name='productos[{{ $r }}][nombre]'
                                            value='{{ old('productos')[$r]['nombre'] }}' />
                                        <td class='nuevo_producto_cantidad'>{{ old('productos')[$r]['cantidad'] }}</td>
                                        <input type='hidden' name='productos[{{ $r }}][cantidad]'
                                            value='{{ old('productos')[$r]['cantidad'] }}' />
                                        <td>$ {{ old('productos')[$r]['venta'] }}</td>
                                        <input type='hidden' name='productos[{{ $r }}][venta]'
                                            value='{{ old('productos')[$r]['venta'] }}' />
                                        <td>$ {{ old('productos')[$r]['venta'] * old('productos')[$r]['cantidad'] }}</td>
                                        <td>
                                            <button type='button' data-toggle='tooltip' title='Eliminar'
                                                class='btn btn-danger btn-eliminar'>
                                                <i class='fa fa-ban'></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endfor
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="observaciones">Observaciones</label>
                <textarea name="observaciones" rows="7" class="form-control">{{ old('observaciones') }}</textarea>
            </div>
        </div>
    @endslot
@endcomponent
