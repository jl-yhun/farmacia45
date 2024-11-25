<script setup>
import { nextTick } from 'process';
import { onMounted, reactive, computed } from 'vue';
import { useQueryBuilder } from '../../composables/query-builder';

const endpoint = '/api/ventas'

const props = defineProps(['params'])

const { buildQueryParams, buildUrlWithNewParams } = useQueryBuilder();

onMounted(async () => {
    await getListOfVentas()
    nextTick(() => {
        state.busqueda.startDate = queryParams.value.startDate
        state.busqueda.endDate = queryParams.value.endDate
    })
})

const queryParams = computed(() => {
    return JSON.parse(atob(props.params))
})

const state = reactive({
    ventas: [],
    busqueda: {},
    canceling: false,
    cancelingItem: false,
    venta_id: null,
    producto_id: null
})

const alert = reactive({
    shown: false,
    text: '',
    type: 'info'
})

const showAlert = (text, type = 'success') => {
    alert.shown = true
    alert.text = text
    alert.type = type
}

const total = computed(() => {
    var total = 0.0;
    state.ventas.forEach((v) => {
        total += parseFloat(v.total)
    })
    return total.toFixed(2)
})

const cancelar = (venta) => {
    state.venta_id = venta.id
    state.canceling = true
}

const cancelarItem = (venta, producto) => {
    state.venta_id = venta.id
    state.producto_id = producto.id
    state.cancelingItem = true
}

const buildUrlWithBusqueda = () => {
    var newParams = buildQueryParams(location.search, state.busqueda)
    history.pushState('', '', buildUrlWithNewParams(newParams))
}

// Requests

const getListOfVentas = async () => {
    try {
        buildUrlWithBusqueda()
        const result = await axios.get(`${endpoint}/reportar${window.location.search}`)

        if (result.data.estado) {
            state.ventas = result.data.data
        } else {
            showAlert('Error al obtener la lista de ventas. Contacte al administrador.', 'danger')
        }
    } catch (error) {
        showAlert(error.response.data.message, 'danger')
    }
}

const doCancelation = async () => {
    try {
        const result = await axios.delete(`${endpoint}/${state.venta_id}`)
        if (result.data.estado) {
            showAlert('Eliminado correctamente')
            state.venta_id = null
            state.canceling = false
            getListOfVentas();
        } else {
            showAlert('Error al cancelar la venta. Consulte al Administrador.', 'danger')
        }
    } catch (error) {
        var status = error.response.status;
        if (status == 403) {
            showAlert('No autorizado.', 'danger')
            return
        }
        console.error(error)
        showAlert('Error al cancelar la venta. Consulte al Administrador.', 'danger')
    }
}

const doCancelationItem = async () => {
    try {
        const result = await axios.delete(`${endpoint}/${state.venta_id}/productos/${state.producto_id}`)
        if (result.data.estado) {
            showAlert('Eliminado correctamente')
            state.venta_id = null
            state.producto_id = null
            state.cancelingItem = false
            getListOfVentas();
        } else {
            showAlert('Error al cancelar este item. Consulte al Administrador.', 'danger')
        }
    } catch (error) {
        var status = error.response.status;
        if (status == 403) {
            showAlert('No autorizado.', 'danger')
            return
        }
        console.error(error)
        showAlert('Error al cancelar este item. Consulte al Administrador.', 'danger')
    }
}

</script>
<template>
    <Notification @onClose="alert.shown = false" v-if="alert.shown" :text="alert.text" :type="alert.type" />

    <ConfirmModal v-if="state.canceling" @on-close="state.canceling = false" @on-confirm="doCancelation()"
        msg="¿Seguro de cancelar esta venta?">
    </ConfirmModal>

    <ConfirmModal v-if="state.cancelingItem" @on-close="state.cancelingItem = false" @on-confirm="doCancelationItem()"
        msg="¿Seguro de cancelar esta item?">
    </ConfirmModal>

    <div class="card">
        <div class="card-header d-flex flex-column flex-sm-row justify-content-between">
            <h6 class="align-self-center my-1 my-sm-0 mr-0 mr-sm-5">Ventas {{ state.ventas.length }}
            </h6>
            <h6 class="align-self-center my-1 my-sm-0 mr-0 mr-sm-5">
                Total: ${{ total }}
            </h6>
            <span class="align-self-center">
                Desde
            </span>
            <DateInput v-model="state.busqueda.startDate" placeholder="Inicio" @keyup.enter="getListOfVentas"
                class="my-1 my-sm-0 mx-0 mx-sm-5 align-self-center" cy="txt-busqueda-startDate">
            </DateInput>
            <span class="align-self-center">
                Hasta
            </span>
            <DateInput v-model="state.busqueda.endDate" placeholder="Fin" @keyup.enter="getListOfVentas"
                class="my-1 my-sm-0 mx-0 mx-sm-5 align-self-center" cy="txt-busqueda-endDate">
            </DateInput>
            <Button cy="btn-buscar" @click="getListOfVentas" title="Buscar" type="primary"
                class="my-1 my-sm-0 mx-0 mx-sm-2">
                <i class="fa fa-search"></i>
            </Button>
        </div>

        <div class="card-body">
            <table class="tabla-ventas table" data-cy="tbl-ventas">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Productos</th>
                        <th>Total</th>
                        <th>Denominación</th>
                        <th>Cambio</th>
                        <th>Utilidad</th>
                        <th>Fecha/Hr</th>
                        <th>Usuario</th>
                        <th class="d-flex flex-column">
                            Acciones
                            <div class="d-flex">
                                <span class="mx-1">cancelar</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="pointer" v-for="venta in state.ventas" :key="venta.id">
                        <td>{{ venta.id }}</td>
                        <td>
                            <div class="flex flex-column my-1" v-for="producto in venta.productos">
                                <Popper arrow placement="left">
                                    <Button type="info" title="Detalle" class="btn-sm mx-1">
                                        <i class="fa fa-info"></i>
                                    </Button>
                                    <template #content>
                                        Stock: {{ producto.stock }}<br>
                                        Min Stock: {{ producto.min_stock }}<br>
                                        Max Stock: {{ producto.max_stock }}<br>
                                    </template>
                                </Popper>
                                <Button cy="btn-delete-item" title="Eliminar" @click="cancelarItem(venta, producto)"
                                    type="danger" class="btn-sm mx-1">
                                    <i class="fa fa-times"></i>
                                </Button>
                                <a :href="'/productos?term=' + producto.codigo_barras">
                                    {{ producto.cantidad }} {{ producto.nombre }}
                                    <sub>{{ producto.descripcion }}</sub>
                                </a>
                            </div>
                        </td>
                        <td>{{ venta.total }}</td>
                        <td>{{ venta.denominacion }}</td>
                        <td>{{ venta.cambio }}</td>
                        <td>{{ venta.utilidad }}</td>
                        <td>{{ venta.created_at }}</td>
                        <td>{{ venta.usuario }}</td>
                        <td>
                            <Button cy="btn-delete" type="danger" class="mx-1" @click="cancelar(venta)">
                                <i class="fa fa-ban"></i>
                            </Button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<style>
.tabla-ventas {
    display: grid;
    grid-template-columns: .5fr 3fr .5fr 1fr .5fr .5fr 2fr 1fr 2fr;
    max-height: 80vh;
    overflow: auto;
    font-size: .8em;
}

.tabla-ventas td,
.tabla-ventas th {
    padding: .75rem .2rem;
}

.tabla-ventas thead,
.tabla-ventas tbody,
.tabla-ventas tr {
    display: contents;
}

.tabla-ventas thead tr th {
    position: sticky;
    top: 0;
    background-color: white;
    z-index: 1000;
}

@media (max-height:660px) {
    .tabla-ventas {
        max-height: 70vh;
    }
}

@media (max-height:464px) {
    .tabla-ventas {
        max-height: 60vh;
    }
}
</style>
