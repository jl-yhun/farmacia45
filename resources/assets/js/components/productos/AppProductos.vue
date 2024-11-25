<script setup>
import { nextTick } from 'process';
import { onMounted, reactive, computed } from 'vue';
import ModalProveedorSelector from '../ordenes-compra/ModalProveedorSelector.vue';
import ModalUpsert from './ModalUpsert.vue';
import AppSimilares from '../similares/AppSimilares.vue';
import ModalProveedores from './ModalProveedores.vue';
import { useQueryBuilder } from '../../composables/query-builder';

const endpoint = '/api/productos'

const props = defineProps(['params'])

const { buildQueryParams, buildUrlWithNewParams } = useQueryBuilder();

onMounted(async () => {
    await getListOfProducts()
    await getListOfCategories()
    nextTick(() => {
        // It  must be nextTicked due to when state init, 
        // queryParams stil not computed
        state.busqueda = { ...queryParams.value }
    })
})

const queryParams = computed(() => {
    return JSON.parse(atob(props.params))
})

const state = reactive({
    productos: [],
    categorias: [],
    busqueda: {},
    selectingDealer: false,
    producto: null,
    producto_id: null,
    deleting: false,
    adding: false
})

const comprarState = reactive({
    selectingDealer: false,
    producto_id: null
})

const alert = reactive({
    shown: false,
    text: '',
    type: 'info'
})

const similares = reactive({
    shown: false,
    baseProductoId: null
})

const proveedores = reactive({
    shown: false,
    producto: null
})


const showAlert = (text, type = 'success') => {
    alert.shown = true
    alert.text = text
    alert.type = type
}

const eliminar = (producto) => {
    state.producto_id = producto.id
    state.deleting = true
}

const deleteFromListOfProducts = (productoId) => {
    const index = state.productos.findIndex(c => c.id == productoId)

    state.productos.splice(index, 1)
}

const addToListOfProducts = (producto) => {
    const categoria = state.categorias.find(c => c.id == producto.categoria_id)
    producto.categoria = categoria.nombre
    state.productos.unshift(producto)
}

const setEditing = (producto, editing = true) => {
    state.adding = true
    state.producto = producto
    // producto.editing = editing
}

const updateFromListOfProducts = (producto) => {
    const index = state.productos.findIndex(c => c.id == producto.id)
    const categoria = state.categorias.find(c => c.id == producto.categoria_id)

    producto.categoria = categoria.nombre
    state.productos[index] = producto
}

const closeModalUpsert = () => {
    state.adding = false
    state.producto = null
}

const showSimilares = (producto) => {
    similares.baseProductoId = producto.id
    similares.shown = true
}

const hideSimilares = () => {
    similares.baseProductoId = null
    similares.shown = false
}

const showProveedores = (producto) => {
    proveedores.producto = producto
    proveedores.shown = true
}

const hideProveedores = () => {
    proveedores.producto = null
    proveedores.shown = false
}

const buildUrlWithBusqueda = () => {
    var newParams = buildQueryParams(location.search, state.busqueda)
    history.pushState('', '', buildUrlWithNewParams(newParams))
}

// Requests

const getListOfProducts = async () => {
    try {
        buildUrlWithBusqueda()
        const result = await axios.get(`${endpoint}${window.location.search}`)

        if (result.data.estado) {
            state.productos = result.data.data
        } else {
            showAlert('Error al obtener la lista de productos. Contacte al administrador.', 'danger')
        }
    } catch (error) {
        showAlert(error.response.data.message, 'danger')
    }
}

const getListOfCategories = async () => {
    try {
        const result = await axios.get('/api/categorias')

        if (result.data.estado) {
            state.categorias = result.data.data
        } else {
            showAlert('Error al obtener la lista de categorías. Contacte al administrador.', 'danger')
        }
    } catch (error) {
        showAlert(error.response.data.message, 'danger')
    }
}

const doDelete = async () => {
    try {
        const result = await axios.delete(`${endpoint}/${state.producto_id}`)
        if (result.data.estado) {
            deleteFromListOfProducts(state.producto_id)
            showAlert('Eliminado correctamente')
            state.producto_id = null
            state.deleting = false
        } else {
            showAlert('Error al eliminar el producto. Consulte al Administrador.', 'danger')
        }
    } catch (error) {
        var status = error.response.status;
        if (status == 403) {
            showAlert('No autorizado.', 'danger')
            return
        }
        console.error(error)
        showAlert('Error al eliminar el producto. Consulte al Administrador.', 'danger')
    }
}

const patchProduct = async (producto) => {
    try {
        const result = await axios.patch(`${endpoint}/${producto.id}`, { ...producto })

        if (result.data.estado) {
            updateFromListOfProducts(producto)
            state.producto = null
            state.adding = false
        } else {
            showAlert('Error al editar el producto. Consulte al Administrador.', 'danger')
        }
    } catch (error) {
        showAlert(error.response.data.message, 'danger')
    }
}

const doCreate = async (producto) => {
    // Update
    if (state.producto != null) {
        producto.id = state.producto.id
        patchProduct(producto)
        return
    }
    try {
        // Insert
        const result = await axios.post(`${endpoint}`, producto)

        if (result.data.estado) {
            addToListOfProducts(result.data.data)
            state.adding = false
        } else {
            showAlert('Error al agregar el producto. Consulte al Administrador.', 'danger')
        }
    } catch (error) {
        showAlert(error.response.data.message, 'danger')
    }
}

const increaseNumProveedores = (data) => {
    var index = state.productos.findIndex(c => c.id == data.pivot.producto_id)

    state.productos[index].num_proveedores++
}

const reduceNumProveedores = (productoId) => {
    var index = state.productos.findIndex(c => c.id == productoId)

    state.productos[index].num_proveedores--
}

const modifyNumSimilares = (data) => {
    var index = state.productos.findIndex(c => c.id == data)

    state.productos[index].num_similares++
}

const comprar = (id) => {
    comprarState.selectingDealer = true
    comprarState.producto_id = id
}

const addToOc = async (attrs) => {
    try {
        state.processing = true
        const result = await axios.post(`/api/ordenes-compra/items/add`, {
            ...attrs,
            'producto_id': comprarState.producto_id
        })

        if (!result.data.estado) {
            showAlert('Error al guardar este item en la orden de compra. Contacte al administrador.', 'danger')
            return
        }

        showAlert(`Se agregó correctamente a la orden de compra vigente para el proveedor seleccionado.`)
        comprarState.selectingDealer = false
        comprarState.producto_id = null

    } catch (error) {
        showAlert(error.response.data.message, 'danger')
    } finally {
        state.processing = false
    }
}

const cancelCompra = () => {
    comprarState.selectingDealer = false
    comprarState.producto_id = null
}

const updateTagsFromProduct = ($event) => {
    var productIndex = state.productos.findIndex(c => c.id == $event.productoId)
    state.productos[productIndex].tags = $event.tags
}

</script>
<template>
    <Notification @onClose="alert.shown = false" v-if="alert.shown" :text="alert.text" :type="alert.type" />

    <ModalUpsert :producto="state.producto" v-if="state.adding" @on-close="closeModalUpsert" @on-confirm="doCreate($event)"
        @onTagsUpdated="updateTagsFromProduct($event)">
    </ModalUpsert>

    <ConfirmModal v-if="state.deleting" @on-close="state.deleting = false" @on-confirm="doDelete()"
        msg="¿Seguro de eliminar este registro?">
    </ConfirmModal>

    <ModalProveedores @on-added="increaseNumProveedores($event)" @on-deleted="reduceNumProveedores($event)"
        @on-close="hideProveedores()" v-if="proveedores.shown" :producto="proveedores.producto">
    </ModalProveedores>

    <ModalProveedorSelector :productoId="comprarState.producto_id" v-if="comprarState.selectingDealer"
        @on-confirm="addToOc($event)" @on-close="cancelCompra()">
    </ModalProveedorSelector>

    <AppSimilares @on-added="modifyNumSimilares($event)" @on-close="hideSimilares()" v-if="similares.shown"
        :base-id="similares.baseProductoId"></AppSimilares>

    <div class="card">
        <div class="card-header d-flex flex-column flex-sm-row justify-content-between">
            <h6 class="align-self-center my-1 my-sm-0">{{ state.productos.length }} ({{ state.productos[0]?.total_productos
                ?? 0 }})
            </h6>
            <TextInput id="busqueda" v-model="state.busqueda.term" class="my-1 my-sm-0 mx-0 mx-sm-5 align-self-center"
                @keyup.enter="getListOfProducts" placeholder="Búsqueda" cy="txt-busqueda-term">
            </TextInput>
            <DateInput v-model="state.busqueda.caducidad" placeholder="Caducidad" @keyup.enter="getListOfProducts"
                class="my-1 my-sm-0 mx-0 mx-sm-5 align-self-center" cy="txt-busqueda-caducidad">
            </DateInput>
            <Button cy="btn-buscar" @click="getListOfProducts" title="Buscar" type="primary"
                class="my-1 my-sm-0 mx-0 mx-sm-2">
                <i class="fa fa-search"></i>
            </Button>
            <Button cy="btn-create" @click="state.adding = true" title="Agregar" type="success" class="my-1 my-sm-0">
                <i class="fa fa-plus"></i>
            </Button>
        </div>

        <div class="card-body">
            <table class="tabla-productos table" data-cy="tbl-productos">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Codigo</th>
                        <th>Categoría</th>
                        <th>Nombre</th>
                        <th>Caducidad</th>
                        <th>Compra</th>
                        <th>Venta</th>
                        <th>Stock</th>
                        <th class="d-flex flex-column">
                            Acciones
                            <div class="d-flex">
                                <span class="mx-1">Similares</span>
                                <span class="mx-1">Proveedores</span>
                                <span class="mx-1">Comprar</span>
                                <span class="mx-1">Eliminar</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="pointer" @dblclick="setEditing(producto)" v-for="producto in state.productos"
                        :key="producto.id">
                        <td>{{ producto.id }}</td>
                        <td>{{ producto.codigo_barras }}</td>
                        <td>{{ producto.categoria }}</td>
                        <td class="flex flex-column">
                            <div>
                                <span class="badge badge-primary mx-1" v-for="tag in producto.tags">
                                    {{ tag.nombre }}
                                </span>
                            </div>
                            {{ producto.nombre }}<br>
                            <sub>{{ producto.descripcion }}</sub>
                        </td>
                        <td>{{ producto.caducidad }}</td>
                        <td>{{ producto.compra }}</td>
                        <td>{{ producto.venta }}</td>
                        <td>{{ producto.stock }}</td>
                        <td>
                            <!-- <Popper :offsetDistance="0" content="Similares" :hover="true" arrow placement="bottom"> -->
                            <button data-cy="btn-similares" class="btn btn-info mr-1" @click="showSimilares(producto)">
                                <i class="fa fa-exchange"></i>&nbsp;
                                <span class="badge badge-light">{{ producto.num_similares }}</span>
                            </button>
                            <!-- </Popper> -->
                            <!-- <Popper :offsetDistance="0" content="Proveedores" :hover="true" arrow placement="bottom"> -->
                            <button data-cy="btn-proveedores" class="btn btn-success mx-1"
                                @click="showProveedores(producto)">
                                <i class="fa fa-users"></i>&nbsp;
                                <span class="badge badge-light">{{ producto.num_proveedores }}</span>
                            </button>
                            <!-- </Popper> -->
                            <!-- <Popper :offsetDistance="0" content="Eliminar" :hover="true" arrow placement="bottom"> -->
                            <button :disabled="producto.num_proveedores == 0" data-cy="btn-comprar"
                                class="btn btn-warning mx-1" @click="comprar(producto.id)">
                                <i class="fa fa-cart-plus"></i>
                            </button>
                            <button data-cy="btn-delete" class="btn btn-danger mx-1" @click="eliminar(producto)">
                                <i class="fa fa-ban"></i>
                            </button>
                            <!-- </Popper> -->

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<style>
.tabla-productos {
    display: grid;
    grid-template-columns: .5fr 1fr 1fr 4fr 1fr .5fr .5fr .5fr 2fr;
    max-height: 80vh;
    overflow: auto;
    font-size: .8em;
}

.tabla-productos td,
.tabla-productos th {
    padding: .75rem .2rem;
}

.tabla-productos thead,
.tabla-productos tbody,
.tabla-productos tr {
    display: contents;
}

.tabla-productos thead tr th {
    position: sticky;
    top: 0;
    background-color: white;
    z-index: 1000;
}

@media (max-height:660px) {
    .tabla-productos {
        max-height: 70vh;
    }
}

@media (max-height:464px) {
    .tabla-productos {
        max-height: 60vh;
    }
}
</style>
