<script setup>
import { onMounted, reactive, computed } from 'vue'
import Notification from '../UI/Notification'

onMounted(() => {
    getList()
})
const endpoint = '/api/inventario'

const state = reactive({
    codigo_barras: '',
    diff: {},
    processing: false
})

const objectsLength = computed(() => ({
    'diferencias': Object.keys(state.diff.diferencias ?? {}).length,
    'inexistencias': Object.keys(state.diff.inexistencias ?? {}).length
}))

const alert = reactive({
    shown: false,
    text: '',
    type: 'info'
})

const confirm = reactive({
    shown: false,
    msg: ''
})

const getList = async () => {
    const response = await axios.get(endpoint + '/diff')
    if (!response.data.estado)
        showAlert('Error al obtener la lista de diferencias, recargue e inténtelo de nuevo.', 'danger')
    else
        state.diff = response.data.data
}

const showAlert = (text, type = 'success') => {
    alert.shown = true
    alert.text = text
    alert.type = type
}

const finish = () => {
    confirm.msg = `¿Seguro que desea confirmar el re-inventario, 
        esto sobreescribirá la información actual en su inventario lógico?`
    confirm.shown = true;
    // href="/inventario/finish"
}

const doFinish = async () => {
    try {
        state.processing = true
        const result = await axios.get(endpoint + "/finish")

        if (result.data.estado) {
            showAlert('Se han aplicado los cambios, en un momento será redirigido', 'success')
            setTimeout(() => {
                location.href = '/productos'
            }, 3000)
        } else {
            showAlert('Error al aplicar cambios.', 'danger')
        }
    } catch (error) {
        showAlert('Error al aplicar cambios.', 'danger')
    } finally {
        state.processing = false
    }

}

</script>
<template>
    <ConfirmModal :processing="state.processing" @onClose="confirm.shown = false" @onConfirm="doFinish()"
        v-if="confirm.shown" :msg="confirm.msg">
    </ConfirmModal>
    <Notification @onClose="alert.shown = false" v-if="alert.shown" :text="alert.text" :type="alert.type"></Notification>
    <div class="container">
        <div class="d-flex flex-column mx-5">
            <div class="d-flex justify-content-between">
                <h1 class="my-4">Resultados</h1>
                <div class="align-self-center">
                    <ButtonLink href="/inventario/create" type="success" class="mx-2">
                        <i class="fa fa-eye"></i> Ver re-inventario
                    </ButtonLink>
                    <Button @click="finish()" type="danger" class="mx-2">
                        <i class="fa fa-check"></i> Finalizar re-inventario
                    </Button>
                </div>
            </div>
            <ul class="nav nav-tabs mb-1" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link" id="diferencias-tab" data-toggle="tab" href="#diferencias" role="tab"
                        aria-controls="diferencias" aria-selected="true">Diferencias ({{ objectsLength.diferencias }})</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" id="inexistencias-tab" data-toggle="tab" href="#inexistencias" role="tab"
                        aria-controls="inexistencias" aria-selected="false">Inexistencias ({{ objectsLength.inexistencias
                        }})</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade" id="diferencias" role="tabpanel" aria-labelledby="diferencias-tab">
                    <table class="tabla-diferencias table">
                        <thead>
                            <tr>
                                <th>Código barras</th>
                                <th>Nombre</th>
                                <th>Stock</th>
                                <th>Compra</th>
                                <th>Venta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, key) in state.diff.diferencias" :key="key">
                                <td>{{ item.codigo_barras }}</td>
                                <td>
                                    <Popper arrow placement="left">
                                        <Button type="info" title="Detalle" class="btn-sm mx-1">
                                            <i class="fa fa-info"></i>
                                        </Button>
                                        <template #content>
                                            Stock: {{ item.current_stock }}<br>
                                        </template>
                                    </Popper>
                                    &nbsp;
                                    <a :href="'/productos?term=' + item.codigo_barras">
                                        {{ item.nombre }}<br>
                                        <sub>{{ item.descripcion }}</sub>
                                    </a>
                                </td>
                                <td :class="{ 'bg-danger text-white': item.stock != 0 }">{{ item.stock }}</td>
                                <td :class="{ 'bg-danger text-white': item.compra != 0 }">{{ item.compra }}</td>
                                <td :class="{ 'bg-danger text-white': item.venta != 0 }">{{ item.venta }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade show active" id="inexistencias" role="tabpanel"
                    aria-labelledby="inexistencias-tab">
                    <table class="tabla-inexistencias table">
                        <thead>
                            <tr>
                                <!-- <th>Inventario</th> -->
                                <th>Código barras</th>
                                <th>Nombre</th>
                                <th>Stock</th>
                                <th>Compra</th>
                                <th>Venta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, key) in state.diff.inexistencias" :key="key">
                                <!-- <td>{{ item.donde }}</td> -->
                                <td>{{ item.codigo_barras }}</td>
                                <td>
                                    <a :href="'/productos?term=' + item.codigo_barras">
                                        {{ item.nombre }}<br>
                                        <sub>{{ item.descripcion }}</sub>
                                    </a>
                                </td>
                                <td>{{ item.stock }}</td>
                                <td>{{ item.compra }}</td>
                                <td>{{ item.venta }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
.bg-blue {
    background-color: #5c8eb4 !important;
}

.tabla-diferencias,
.tabla-inexistencias {
    display: grid;
    max-height: 70vh;
    overflow-y: auto;
    overflow-x: hidden;
}

.table thead,
.table tbody,
.table tr {
    display: contents;
}

.table thead tr th {
    position: sticky;
    background-color: white;
    top: 0;
    z-index: 1000;
}

.tabla-diferencias {
    grid-template-columns: 1fr 3fr repeat(3, 1fr);
}

.tabla-inexistencias {
    grid-template-columns: 1fr 3fr repeat(3, 1fr);
}

@media(max-height: 650px) {

    .tabla-diferencias,
    .tabla-inexistencias {
        max-height: 60vh;
    }
}

@media(max-height: 500px) {

    .tabla-diferencias,
    .tabla-inexistencias {
        max-height: 50vh;
    }
}

@media(max-height: 400px) {

    .tabla-diferencias,
    .tabla-inexistencias {
        max-height: 40vh;
    }
}
</style>
