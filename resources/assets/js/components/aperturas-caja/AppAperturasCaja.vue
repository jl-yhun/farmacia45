<script setup>
import { onMounted, reactive } from 'vue';

const endpoint = 'api/aperturas-caja'
onMounted(async () => {
    await getList()
})

const state = reactive({
    aperturas: []
})

const getList = async () => {
    try {
        const result = await axios.get(endpoint)
        if (result.data.estado) {
            state.aperturas = result.data.data
        } else {
            showAlert('Error al obtener la lista de aperturas de caja.', 'danger')
        }
    } catch (error) {
        showAlert('Error al obtener la lista de aperturas de caja.', 'danger')
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
</script>
<template>
    <Notification @onClose="alert.shown = false" v-if="alert.shown" :text="alert.text" :type="alert.type"></Notification>
    <table class="table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Inicial</th>
                <th>Apartados</th>
                <th>Ventas</th>
                <th>Serv/Recargas</th>
                <th>Comisiones</th>
                <th>Gastos</th>
                <th>Transferencias</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="apertura of state.aperturas" :key="apertura.id">
                <td>{{ apertura.created_at_formatted }}</td>
                <td>
                    {{ apertura.inicial_efe }}
                    <sub>{{ apertura.inicial_ele }}</sub><br>
                </td>
                <td>{{ apertura.inicial_apartados }}
                    <sub>{{ apertura.subtotal_apartados }}</sub>
                </td>
                <td>
                    {{ apertura.ventas_efe }}
                    <sub>{{ apertura.ventas_ele }}</sub>
                </td>
                <td>
                    {{ apertura.servicios_recargas_efe }}
                    <sub>{{ apertura.servicios_recargas_ele }}</sub>
                </td>
                <td>
                    {{ apertura.comisiones_efe }}
                    <sub>{{ apertura.comisiones_ele }}</sub>
                </td>
                <td>{{ apertura.gastos_efe }}
                    <sub>{{ apertura.gastos_ele }}</sub>
                </td>
                <td>{{ apertura.transferencias }}</td>
                <td>{{ apertura.subtotal_efe }}
                    <sub>{{ apertura.subtotal_ele }}</sub><br>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th>Fecha</th>
                <th>Inicial</th>
                <th>Apartados</th>
                <th>Ventas</th>
                <th>Serv/Recargas</th>
                <th>Comisiones</th>
                <th>Gastos</th>
                <th>Transferencias</th>
                <th>Total</th>
            </tr>
        </tfoot>
    </table>
</template>
<style>
.table {
    display: grid;
    grid-template-columns: repeat(9, minmax(80px, 1fr));
    max-height: 84vh;
    overflow-y: auto;
}

thead,
tfoot,
tbody,
tr {
    display: contents;
}

thead tr th,
tfoot tr th {
    position: sticky;
    background-color: white;
    z-index: 1000;
}

thead tr th {
    top: 0;
}

tfoot tr th {
    bottom: 0;
}

@media (max-height: 790px) {
    .table {
        max-height: 78vh;
    }
}

@media (max-height: 600px) {
    .table {
        max-height: 75vh;
    }
}
</style>