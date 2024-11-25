<script setup>
import { onMounted, reactive, computed, watch, ref } from 'vue';

const props = defineProps({
    aperturaCajaId: {
        default: 0
    }
})

defineEmits(['onSelected'])

onMounted(async () => {
    await getList()
})

const aperturaCajaId = computed(() => props.aperturaCajaId)

watch(aperturaCajaId, () => {
    getList()
})

const endpoint = '/api/ordenes-compra'

const state = reactive({
    sugerencias: []
})


const similares = ref(null)

const alert = reactive({
    shown: false,
    text: '',
    type: 'info'
})

const getList = async () => {
    try {
        const result = await axios.get(endpoint + '/faltantes/suggested/' + props.aperturaCajaId)
        if (result.data.estado) {
            state.sugerencias = result.data.data
        } else {
            showAlert('Error al obtener la lista de sugerencias.', 'danger')
        }
    } catch (error) {
        showAlert('Error al obtener la lista de sugerencias.', 'danger')
    }
}

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

const getColor = (sugerencia) => {
    if (sugerencia.stock == 0)
        return 'bg-danger text-white';

}

const numItems = computed(() => state.sugerencias.length)

</script>
<template>
    <div class="border px-4">
        <h5>Compras sugeridas ({{ numItems }})</h5>
        <Notification @onClose="alert.shown = false" v-if="alert.shown" :text="alert.text" :type="alert.type">
        </Notification>
        <table class="table-sugeridas table">
            <thead>
                <tr>
                    <th>Código Barras</th>
                    <th>Producto</th>
                    <th>Stock actual</th>
                    <th>Sugerido</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="sugerencia of state.sugerencias" :key="sugerencia.codigo_barras">
                    <td :class="getColor(sugerencia)">{{ sugerencia.codigo_barras }}</td>
                    <td :class="getColor(sugerencia)">
                        <a :class="getColor(sugerencia)" :href="'/productos?term=' + sugerencia.codigo_barras">
                            {{ sugerencia.nombre }}
                            <br>
                            <sub>{{ sugerencia.descripcion }}</sub>
                        </a>
                    </td>
                    <td :class="getColor(sugerencia)">
                        {{ sugerencia.stock }}
                    </td>
                    <td :class="getColor(sugerencia)">
                        {{ sugerencia.sugerido }}
                    </td>
                    <td :class="getColor(sugerencia)">
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
                        <Button v-if="sugerencia.proveedores > 0" @click="$emit('onSelected', sugerencia.id)" class="m-1"
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
.table-sugeridas,
.table-similares {
    display: grid;
    grid-template-columns: 1fr repeat(4, minmax(80px, .8fr));
    overflow-y: auto;
}

.table-sugeridas {
    grid-template-columns: 1fr 1fr repeat(3, minmax(80px, .3fr));
    font-size: .8em;
    max-height: 74vh;
}

.table-similares {
    grid-template-columns: .1fr .3fr .3fr .1fr;
}

.table-sugeridas thead,
.table-sugeridas tfoot,
.table-sugeridas tbody,
.table-sugeridas tr {
    display: contents;
}

.table-sugeridas>thead tr th,
.table-sugeridas>tfoot tr th {
    position: sticky;
    background-color: white;
    z-index: 1000;
}

.table-sugeridas thead tr th {
    top: 0;
}

.table-sugeridas tfoot tr th {
    bottom: 0;
}

@media(max-height: 980px) {
    .table-sugeridas {
        max-height: 70vh;
    }
}

@media(max-height: 700px) {
    .table-sugeridas {
        max-height: 65vh;
    }
}

@media(max-height:590px) {
    .table-sugeridas {
        max-height: 60vh;
    }
}

@media(max-height:530px) {
    .table-sugeridas {
        max-height: 55vh;
    }
}

@media(max-height:470px) {
    .table-sugeridas {
        max-height: 50vh;
    }
}

@media(max-height:410px) {
    .table-sugeridas {
        max-height: 45vh;
    }
}
</style>