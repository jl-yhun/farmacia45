<script setup>
import { onMounted, reactive, computed, watch, ref } from 'vue';

const props = defineProps({
    aperturaCajaId: {
        default: 0
    }
})

const similares = ref(null)
defineEmits(['onSelected'])

const aperturaCajaId = computed(() => props.aperturaCajaId)

const endpoint = '/api/ordenes-compra'
onMounted(async () => {
    await getList()
})

watch(aperturaCajaId, () => {
    getList()
})

const state = reactive({
    sugerencias: []
})

const getList = async () => {
    try {
        const result = await axios.get(endpoint + '/faltantes/not-available/' + props.aperturaCajaId)
        if (result.data.estado) {
            state.sugerencias = result.data.data
        } else {
            showAlert('Error al obtener la lista de productos no disponibles.', 'danger')
        }
    } catch (error) {
        showAlert('Error al obtener la lista de productos no disponibles.', 'danger')
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

const loadSimilares = async (productoId) => {
    try {
        similares.value = null;
        const result = await axios.get('/api/similares/' + productoId)

        similares.value = result.data.data;
    } catch (error) {
        showAlert('Error al obtener lista de productos similares.', 'danger')
    }
}

const numItems = computed(() => state.sugerencias.length)
const uniqueProductsCount = computed(() => state.sugerencias.filter(c => c.similares == 0).length)
</script>
<template>
    <div class="border px-4">
        <h5>Productos no disponibles ({{ uniqueProductsCount }} - {{ numItems }})</h5>
        <Notification @onClose="alert.shown = false" v-if="alert.shown" :text="alert.text" :type="alert.type">
        </Notification>
        <table class="table-no-available table">
            <thead>
                <tr>
                    <th>Código Barras</th>
                    <th>Producto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="sugerencia of state.sugerencias" :key="sugerencia.codigo_barras">
                    <td>{{ sugerencia.codigo_barras }}</td>
                    <td>
                        <a :href="'/productos?term=' + sugerencia.codigo_barras">
                            {{ sugerencia.nombre }}
                            <br>
                            <sub>{{ sugerencia.descripcion }}</sub>
                        </a>
                    </td>
                    <td>
                        <Popper v-if="sugerencia.similares > 0" arrow placement="left">
                            <Button @click="loadSimilares(sugerencia.id)" class="btn-sm mx-1" type="info" title="Similares">
                                <i class="fa fa-info"></i>
                            </Button>
                            <template #content>
                                <i v-if="!similares" class="fa fa-spinner fa-spin"></i>
                                <table class="table-similares" v-else>
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Desc.</th>
                                            <th>Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="similar of similares" :key="similar.id">
                                            <td>{{ similar.codigo_barras }}</td>
                                            <td>{{ similar.nombre }}</td>
                                            <td>{{ similar.descripcion }}</td>
                                            <td>{{ similar.stock }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </template>
                        </Popper>
                        <Button v-if="sugerencia.proveedores > 0" @click="$emit('onSelected', sugerencia.id)" class="mx-1"
                            type="success" title="Comprar">
                            <i class="fa fa-cart-plus"></i>
                        </Button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<style>
.table-no-available,
.table-similares {
    display: grid;
    grid-template-columns: 1fr 2fr 1fr;
    overflow-y: auto;
}

.table-no-available {
    grid-template-columns: repeat(2, minmax(80px, 1fr)) 80px;
    font-size: .8em;
    max-height: 74vh;
}

.table-similares {
    grid-template-columns: .1fr .3fr .3fr .1fr;
}

.table-no-available thead,
.table-no-available tfoot,
.table-no-available tbody,
.table-no-available tr {
    display: contents;
}

.table-no-available>thead tr th,
.table-no-available>tfoot tr th {
    position: sticky;
    background-color: white;
    z-index: 1000;
}

.table-no-available>thead tr th {
    top: 0;
}

.table-no-available>tfoot tr th {
    bottom: 0;
}

@media(max-height: 980px) {
    .table-no-available {
        max-height: 70vh;
    }
}

@media(max-height: 700px) {
    .table-no-available {
        max-height: 65vh;
    }
}

@media(max-height:590px) {
    .table-no-available {
        max-height: 60vh;
    }
}

@media(max-height:530px) {
    .table-no-available {
        max-height: 55vh;
    }
}

@media(max-height:470px) {
    .table-no-available {
        max-height: 50vh;
    }
}

@media(max-height:410px) {
    .table-no-available {
        max-height: 45vh;
    }
}
</style>