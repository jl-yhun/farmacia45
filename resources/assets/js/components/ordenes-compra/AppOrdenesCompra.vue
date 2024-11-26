<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import ModalOrdenCompraDetalle from './ModalOrdenCompraDetalle.vue';
import ModalRecepcion from './ModalRecepcion.vue';
import { useAuth } from '../../composables/auth';
const endpoint = '/api/ordenes-compra'

const { user } = useAuth()

onMounted(async () => {
    await getList()
})

const rollbackAvailable = (orden) => {
    return orden.estado != 'Pendiente'
        && orden.estado != 'Aplicado'
        && state.user?.rol?.name == 'Admin'
}

const state = reactive({
    ordenes: [],
    seeingDetail: false,
    selectedOrdenCompraId: 0,
    processing: false,
    user: user
})

const alert = reactive({
    shown: false,
    text: '',
    type: 'info'
})

const confirm = reactive({
    shown: false,
    msg: ''
})

const receipt = reactive({
    shown: false,
    data: []
})

const selectedOc = reactive({
    ocId: null,
    nextEstado: ''
})

const resetConfirmState = () => {
    confirm.shown = false
    confirm.msg = ''
    selectedOc.state = ''
    selectedOc.ocId = null
}

const resetReceiptState = () => {
    receipt.shown = false
    receipt.data = []
    selectedOc.state = ''
    selectedOc.ocId = null
}

const getList = async () => {
    try {
        const result = await axios.get(endpoint)
        if (result.data.estado) {
            state.ordenes = result.data.data
        } else {
            showAlert('Error al obtener la lista de órdenes.', 'danger')
        }
    } catch (error) {
        showAlert('Error al obtener la lista de órdenes.', 'danger')
    }
}

const cambiarEstado = async () => {
  try {
    state.processing = true;
    const payload = {
      estado: selectedOc.state,
    };
    if (selectedOc.state === 'Recibido') {
      payload.recibidor_id = state.user.user.id;
    } else if (selectedOc.state === 'Aplicado') {
      payload.aplicador_id = state.user.user.id;
    } else if (selectedOc.state === 'Pedido') {
      payload.recibidor_id = null;
    }
    const result = await axios.patch(endpoint + '/' + selectedOc.ocId, payload);
    if (result.data.estado) {
      const ocIndex = state.ordenes.findIndex(c => c.id == selectedOc.ocId);

      state.ordenes[ocIndex].estado = selectedOc.state;
      if (selectedOc.state === 'Recibido') {
        state.ordenes[ocIndex].recibidor_id = user.id;
      } else if (selectedOc.state === 'Aplicado') {
        state.ordenes[ocIndex].aplicador_id = user.id;
      } else if (selectedOc.state === 'Pedido') {
        state.ordenes[ocIndex].recibidor_id = null;
      }

      resetConfirmState();
      resetReceiptState();
    } else {
      showAlert('Error al cambiar el estado.', 'danger');
    }
  } catch (error) {
    showAlert('Error al cambiar el estado.', 'danger');
  } finally {
    state.processing = false;
  }
};

const showAlert = (text, type = 'success') => {
    alert.shown = true
    alert.text = text
    alert.type = type
}

const showDetail = (oc) => {
    if (oc.estado == 'Recibido')
        showModalBasedOnNextState(oc)
    else {
        state.selectedOrdenCompraId = oc.id
        state.seeingDetail = true
    }
}

const showModalBasedOnNextState = (oc) => {
    let nextState = getNextEstado(oc.estado)

    switch (nextState) {
        case 'Pedido':
        case 'Recibido':
            openConfirmModal(oc, nextState)
            break;
        case 'Aplicado':
            openReceiptModal(oc, nextState)
            break;
        default:
            break;
    }
}

const showModalBasedOnPrevState = (oc) => {
    let prevState = getPrevEstado(oc.estado)

    switch (prevState) {
        case 'Pedido':
        case 'Pendiente':
            openConfirmModal(oc, prevState)
            break;
    }
}

const openConfirmModal = (oc, state) => {
    selectedOc.state = state
    confirm.msg = `Esto cambiará el estado 
                   de la orden de compra a <b>${state}</b>. 
                   <br><br>¿Está seguro?`
    confirm.shown = true
    selectedOc.ocId = oc.id
}

const openReceiptModal = (oc, nextState) => {
    receipt.shown = true
    selectedOc.ocId = oc.id
    selectedOc.state = nextState
}

const getNextEstado = (estado) => {
    switch (estado) {
        case 'Pendiente':
            return 'Pedido'
        case 'Pedido':
            return 'Recibido'
        case 'Recibido':
            return 'Aplicado'
    }
}

const getPrevEstado = (estado) => {
    switch (estado) {
        case 'Pedido':
            return 'Pendiente'
        case 'Recibido':
            return 'Pedido'
    }
}

const updateTotalOrdenCompra = (orden) => {
    const ocIndex = state.ordenes.findIndex(c => c.id == orden.id)

    state.ordenes[ocIndex] = orden
    state.ordenes[ocIndex].total = 0

    state.ordenes[ocIndex].productos.map(c => {
        state.ordenes[ocIndex].total += c.pivot.cantidad * c.pivot.compra
    })

    state.ordenes[ocIndex].total = state.ordenes[ocIndex].total.toFixed(2)
}

</script>
<template>
    <ConfirmModal :processing="state.processing" @onClose="resetConfirmState()" @onConfirm="cambiarEstado()"
        v-if="confirm.shown" :msg="confirm.msg">
    </ConfirmModal>

    <Notification @onClose="alert.shown = false" v-if="alert.shown" :text="alert.text" :type="alert.type"></Notification>

    <ModalOrdenCompraDetalle v-if="state.seeingDetail" :ordenCompraId="state.selectedOrdenCompraId"
        @onClose="state.seeingDetail = false" @onItemsUpdate="updateTotalOrdenCompra($event)"></ModalOrdenCompraDetalle>

    <ModalRecepcion :processing="state.processing" @onItemsUpdate="updateTotalOrdenCompra($event)"
        @onClose="receipt.shown = false" @onConfirm="cambiarEstado()" v-if="receipt.shown"
        :orden-compra-id="selectedOc.ocId"></ModalRecepcion>

    <table class="tabla-ordenes table w-100">
        <thead>
            <tr>
                <th>Folio</th>
                <th>Proveedor</th>
                <th>Estado</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr @dblclick="showDetail(orden)" v-for="orden of state.ordenes" :key="orden.id">
                <td>{{ orden.id }}</td>
                <td>{{ orden.proveedor.nombre }}</td>
                <td>{{ orden.estado }}</td>
                <td>{{ orden.total }}</td>
                <td>
                    <Button v-if="orden.estado != 'Aplicado'" @click="showModalBasedOnNextState(orden)" class="mx-1"
                        type="success" title="Cambiar estado">
                        <i class="fa fa-refresh"></i>
                    </Button>
                    <Button v-if="rollbackAvailable(orden)" @click="showModalBasedOnPrevState(orden)" type="info"
                        title="Regresar estado" class="mx-1">
                        <i class="fa fa-undo"></i>
                    </Button>
                </td>
            </tr>
        </tbody>
    </table>
</template>
<style>
.tabla-ordenes {
    display: grid;
    grid-template-columns: repeat(5, minmax(80px, 1fr));
    max-height: 80vh;
    overflow: auto;
}

.tabla-ordenes thead,
.tabla-ordenes tfoot,
.tabla-ordenes tbody,
.tabla-ordenes tr {
    display: contents;
}

.tabla-ordenes thead tr th,
.tabla-ordenes tfoot tr th {
    position: sticky;
    background-color: white;
    z-index: 1000;
}

.tabla-ordenes thead tr th {
    top: 0;
}

.tabla-ordenes tfoot tr th {
    bottom: 0;
}
</style>