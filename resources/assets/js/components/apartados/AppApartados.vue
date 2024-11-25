<script setup>
import { onMounted, ref, reactive } from 'vue';

onMounted(() => {
    getListaApartados()
})

const apartados = ref([])

const getListaApartados = async () => {
    try {
        const result = await axios.get('/api/apartados')

        if (!result.data.estado)
            showAlert('Error al obtener la lista de apartados.', 'danger')
        else
            apartados.value = result.data.data
    } catch (error) {
        showAlert('Error al obtener la lista de apartados.', 'danger')
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
    <Notification @onClose="alert.shown = false" v-if="alert.shown" :text="alert.text" :type="alert.type">
    </Notification>
    <table class="table" id="tabla-apartados">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Concepto</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="apartado of apartados" :key="apartado.id">
                <td>{{ apartado.created_at_formatted }}</td>
                <td>{{ apartado.monto }}</td>
                <td>{{ apartado.concepto }}</td>
                <td>{{ apartado.usuario.name }}</td>
            </tr>
        </tbody>
    </table>
</template>
<style>
#tabla-apartados {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    overflow-y: auto;
    max-height: 74vh;
}

#tabla-apartados thead,
#tabla-apartados tbody,
#tabla-apartados tr {
    display: contents;
}

#tabla-apartados>thead tr th,
#tabla-apartados>tfoot tr th {
    position: sticky;
    background-color: white;
    z-index: 1000;
}

#tabla-apartados>thead tr th {
    top: 0;
}
</style>