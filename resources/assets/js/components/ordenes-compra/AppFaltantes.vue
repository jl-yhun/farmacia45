<script setup>
import { onMounted, reactive } from 'vue';
import TableSuggested from './TableSuggested.vue';
import TableNotAvailable from './TableNotAvailable.vue';
import ModalProveedorSelector from './ModalProveedorSelector.vue';

const endpoint = '/api/ordenes-compra'
onMounted(async () => {
    await getList()
})

const state = reactive({
    aperturaCajaId: 0,
    aperturas: [],
    processing: false,
    selectingDealer: false,
    producto_id: null
})

const getList = async () => {
    try {
        const result = await axios.get('/api/aperturas-caja')
        state.aperturas = result.data.data
    } catch (error) {
        showAlert('Error al obtener la lista de aperturas.', 'danger')
    }
}

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

const addToOc = async (attrs) => {
    try {
        state.processing = true
        const result = await axios.post(`${endpoint}/items/add`, {
            ...attrs,
            'producto_id': state.producto_id
        })

        if (!result.data.estado) {
            showAlert('Error al guardar este item en la orden de compra. Contacte al administrador.', 'danger')
            return
        }

        showAlert(`Se agregó correctamente a la orden de compra vigente para el proveedor seleccionado.`)
        state.selectingDealer = false
        state.producto_id = null

    } catch (error) {
        showAlert(error.response.data.message, 'danger')
    } finally {
        state.processing = false
    }
}

const makeSelection = (attr) => {
    state.selectingDealer = true
    state.producto_id = attr
}

const cancel = () => {
    state.selectingDealer = false
    state.producto_id = null
}

</script>
<template>
    <div class="my-2">
        <Select class="col col-md-3" v-model="state.aperturaCajaId">
            <option value="0">-- Seleccione apertura --</option>
            <option v-for="apertura in state.aperturas" :key="apertura.id" :value="apertura.id">
                {{ `${apertura.id} - ${apertura.created_at_formatted}` }}
            </option>
        </Select>
    </div>
    <div class="d-flex flex-column flex-md-row main-cont">
        <TableSuggested @on-selected="makeSelection($event)" :aperturaCajaId="state.aperturaCajaId" class="w-100">
        </TableSuggested>
        <TableNotAvailable @on-selected="makeSelection($event)" :aperturaCajaId="state.aperturaCajaId" class="w-100">
        </TableNotAvailable>
        <ModalProveedorSelector :productoId="state.producto_id" v-if="state.selectingDealer" @on-confirm="addToOc($event)"
            @on-close="cancel()">
        </ModalProveedorSelector>
    </div>
</template>
<style>
.main-cont {
    height: 78vh;
}
</style>