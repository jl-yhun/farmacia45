<script setup>
import { onMounted, reactive } from 'vue'

const props = defineProps(['producto'])

onMounted(() => {
    fetchListOfDealers();
    fetchListOfProductsDealers(props.producto?.id)
})

const emit = defineEmits(['onClose', 'onAdded', 'onDeleted'])

const state = reactive({
    producto_id: props.producto?.id,
    proveedor_id: '',
    codigo: props.producto?.codigo_barras,
    precio: '',
    default: 0,
    disponible: 1
})

const secondaryState = reactive({
    listaProveedores: [],
    listaProveedoresProductos: [],
    processing: false,
    editing: false
})

const alert = reactive({
    shown: false,
    text: '',
    type: 'info'
})

const fetchListOfDealers = async () => {
    try {
        const result = await axios.get('/api/proveedores')

        if (result.data.estado)
            secondaryState.listaProveedores = result.data.data
        else
            showAlert('Error al obtener la lista de proveedores.', 'danger')
    } catch (error) {
        showAlert('Error al obtener la lista de proveedores.', 'danger')
    }
}

const fetchListOfProductsDealers = async (productoId) => {
    try {
        const result = await axios.get(`/api/productos/${productoId}/proveedores`)

        if (result.data.estado)
            secondaryState.listaProveedoresProductos = result.data.data
        else
            showAlert('Error al obtener la lista de registros.', 'danger')
    } catch (error) {
        showAlert('Error al obtener la lista de registros.', 'danger')
    }
}

const saveProductoProveedor = async () => {
    if (secondaryState.editing)
        putProductoProveedor()
    else
        postProductoProveedor()
}

const eliminarRecord = async (productoProveedor) => {
    try {
        const result = await axios.delete(`/api/productos/${productoProveedor.pivot.producto_id}/proveedores/${productoProveedor.pivot.proveedor_id}`)
        if (!result.data.estado)
            showAlert('Error al eliminar registro, consulte con el administrador.', 'danger')
        else {
            const index = secondaryState.listaProveedoresProductos
                .findIndex(c => c.pivot.proveedor_id == productoProveedor.pivot.proveedor_id)

            secondaryState.listaProveedoresProductos.splice(index, 1)
            emit('onDeleted', productoProveedor.pivot.producto_id)
        }
    } catch (error) {
        if (error.response?.status == 403) {
            showAlert('No autorizado.', 'danger')
            return;
        } else if (error.response?.status == 422) {
            showAlert(error.response.data.message, 'danger')
            return;
        }
        console.error(error);
        showAlert('Error al eliminar registro, consulte con el administrador.', 'danger')
    }
}

const postProductoProveedor = async () => {
    try {
        const result = await axios.post(`/api/productos/${state.producto_id}/proveedores`, state)

        if (!result.data.estado)
            showAlert('Error al ligar el proveedor con este producto.', 'danger')
        else {
            secondaryState.listaProveedoresProductos.push(result.data.data)
            emit('onAdded', result.data.data)
            fillState(null)
        }

    } catch (error) {
        if (error.response.status == 403) {
            showAlert('No autorizado.', 'danger')
            return;
        } else if (error.response.status == 422) {
            showAlert(error.response.data.message, 'danger')
            return;
        }
        console.error(error);
        showAlert('Error al ligar el proveedor con este producto.', 'danger')
    }
}

const putProductoProveedor = async () => {
    try {
        const result = await axios.put(`/api/productos/${state.producto_id}/proveedores/${state.proveedor_id}`, state)

        if (!result.data.estado)
            showAlert('Error al modificar registro, consulte con el administrador.', 'danger')
        else {
            const index = secondaryState.listaProveedoresProductos
                .findIndex(c => c.pivot.proveedor_id == state.proveedor_id)

            secondaryState.listaProveedoresProductos[index] = result.data.data
            setProductoProveedorEditing(null, false)
        }

    } catch (error) {
        if (error.response.status == 403) {
            showAlert('No autorizado.', 'danger')
            return;
        } else if (error.response.status == 422) {
            showAlert(error.response.data.message, 'danger')
            return;
        }
        console.error(error);
        showAlert('Error al modificar registro, consulte con el administrador.', 'danger')
    }
}

const showAlert = (text, type = 'success') => {
    alert.shown = true
    alert.text = text
    alert.type = type
}

const setProductoProveedorEditing = (item, editing = true) => {
    fillState(item)
    secondaryState.editing = editing
}

const fillState = (item) => {
    state.producto_id = item?.pivot.producto_id ?? props.producto?.id
    state.proveedor_id = item?.id ?? ''
    state.codigo = item?.pivot.codigo ?? ''
    state.precio = item?.pivot.precio ?? ''
    state.default = item?.pivot.default ?? 0
    state.disponible = item?.pivot.disponible ?? 1
}

</script>
<template>
    <Teleport to="body">
        <Notification @onClose="alert.shown = false" v-if="alert.shown" :text="alert.text" :type="alert.type">
        </Notification>
        <div class="modal show" tabindex="-1" role="dialog" aria-modal="true" style="display: block;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Proveedores</h5>
                        <div class="mx-auto">
                            <p>
                                {{ producto?.codigo_barras }}
                                <br>
                                <sub>{{ producto?.nombre }} {{ producto?.descripcion }}</sub>
                            </p>
                        </div>
                        <button @click="$emit('onClose')" type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="container d-flex flex-column">
                                <div class="mb-0 mb-sm-3 d-flex flex-column flex-sm-row">
                                    <div class="mt-3 mt-sm-0 mr-0 mr-sm-1 w-100">
                                        <label for="proveedor_id">Proveedor</label>
                                        <Select cy="sel-proveedor" id="proveedor_id" v-model="state.proveedor_id">
                                            <option value="">--Proveedor--</option>
                                            <option :value="proveedor.id"
                                                v-for="proveedor in secondaryState.listaProveedores" :key="proveedor.id">
                                                {{ proveedor.nombre }}
                                            </option>
                                        </Select>
                                    </div>
                                    <div class="mt-3 mt-sm-0 ml-0 ml-sm-1 w-100">
                                        <label for="codigo">Código</label>
                                        <TextInput cy="txt-codigo" id="codigo" v-model="state.codigo" :val="producto?.codigo_barras" readonly="readonly">
                                        </TextInput>
                                    </div>
                                </div>
                                <div class="mb-0 mb-sm-3 d-flex flex-column flex-sm-row">
                                    <div class="mt-3 mt-sm-0 mr-0 mr-sm-1 w-100">
                                        <label for="disponible">Disponible</label>
                                        <Select v-model="state.disponible" id="disponible">
                                            <option value="1">Si</option>
                                            <option value="0">No</option>
                                        </Select>
                                    </div>
                                    <div class="mt-3 mt-sm-0 ml-0 ml-sm-1 w-100">
                                        <label for="precio">Precio</label>
                                        <NumberInputGroup id="precio" cy="txt-precio" v-model="state.precio">
                                        </NumberInputGroup>
                                    </div>
                                </div>
                                <div class="mb-0 mb-sm-3 d-flex flex-column flex-sm-row">
                                    <div class="mt-3 mt-sm-0 mr-0 mr-sm-1 w-100">
                                        <label for="default">Default</label>
                                        <Select v-model="state.default" id="default">
                                            <option value="0">No</option>
                                            <option value="1">Si</option>
                                        </Select>
                                    </div>
                                    <Button cy="btn-agregar-proveedor" @click="saveProductoProveedor"
                                        class="mt-3 mt-sm-0 ml-0 ml-sm-1 w-100 align-self-end" type="success">
                                        <i class="fa" :class="secondaryState.editing ? 'fa-pencil' : 'fa-plus'"></i> {{
                                            secondaryState.editing ? 'Actualizar' : 'Agregar' }}
                                    </Button>
                                    <Button v-if="secondaryState.editing" cy="btn-cancelar-proveedor"
                                        @click="setProductoProveedorEditing(null, false)"
                                        class="mt-3 mt-sm-0 ml-0 ml-sm-1 w-100 align-self-end" type="secondary">
                                        <i class="fa fa-times"></i> Cancelar
                                    </Button>
                                </div>
                                <h4 class="mt-4">Lista de proveedores</h4>
                                <table class="table-productos_proveedores table" data-cy="tbl-productos-proveedores">
                                    <thead>
                                        <tr>
                                            <th>Proveedor</th>
                                            <th>Código</th>
                                            <th>Precio</th>
                                            <th>Default</th>
                                            <th>Disponible</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="pointer" @dblclick="setProductoProveedorEditing(proveedorProducto)"
                                            v-for="(proveedorProducto, i) of secondaryState.listaProveedoresProductos"
                                            :key="i">
                                            <td>{{ proveedorProducto.nombre }}</td>
                                            <td>{{ proveedorProducto.pivot.codigo }}</td>
                                            <td>{{ proveedorProducto.pivot.precio }}</td>
                                            <td>{{ proveedorProducto.pivot.default == '1' ? 'x' : '' }}</td>
                                            <td>{{ proveedorProducto.pivot.disponible == '1' ? 'Si' : 'No' }}</td>
                                            <td>
                                                <Button cy="btn-eliminar" type="danger" @click="eliminarRecord(proveedorProducto)">
                                                    <i class="fa fa-ban"></i>
                                                </Button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <Button cy="btn-cancelar" :disabled="secondaryState.processing" @click="$emit('onClose')"
                            data-cy="btn-cancelar" type="danger" data-dismiss="modal">
                            CERRAR
                        </Button>
                        <!-- <Button cy="btn-ok" @click="$emit('onConfirm', state)" :disabled="secondaryState.processing"
                                                                                                                                                                                        data-cy="btn-ok" type="success">
                                                                                                                                                                                        OK <i class="fa fa-check-circle"></i>
                                                                                                                                                                                    </Button> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop show"></div>
    </Teleport>
</template>
<style>
.table-productos_proveedores {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr repeat(3, .5fr);
    overflow-y: auto;
    max-height: 40vh;
    min-height: 200px;
}

.table-productos_proveedores thead,
.table-productos_proveedores tfoot,
.table-productos_proveedores tbody,
.table-productos_proveedores tr {
    display: contents;
}

.table-productos_proveedores>thead tr th,
.table-productos_proveedores>tfoot tr th {
    position: sticky;
    background-color: white;
    z-index: 1000;
}

.table-productos_proveedores>thead tr th {
    top: 0;
}

@media(max-height: 820px) {
    .table-productos_proveedores {
        max-height: 25vh;
    }
}

@media(max-height: 680px) {
    .table-productos_proveedores {
        max-height: 22vh;
    }
}
</style>
