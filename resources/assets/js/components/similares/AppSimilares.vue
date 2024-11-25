<script setup>
import { onMounted, reactive, ref, computed } from 'vue'

const props = defineProps(['baseId'])

const emit = defineEmits(['onClose', 'onAdded'])

onMounted(() => {
    getProductos()
    getSimilares()
})

const state = reactive({
    productoSelectedId: null
})

const productos = ref([])
const similares = ref([])

const processing = ref(false)

const alert = reactive({
    shown: false,
    text: '',
    type: 'info'
})

const getApplicableProducts = computed(() => {
    const applicableProducts = productos.value.filter(c =>
        !similares.value.flatMap(d => d.id).includes(c.id) &&
        c.id != props.baseId)

    return applicableProducts
})

const getProductos = async () => {
    try {
        processing.value = true
        const result = await axios.get('/api/productos/temp/json')

        productos.value = result.data;
    } catch (error) {
        showAlert('Error al obtener lista de productos.', 'danger')
    } finally {
        processing.value = false
    }
}

const getSimilares = async () => {
    try {
        processing.value = true
        const result = await axios.get('/api/similares/' + props.baseId)

        similares.value = result.data.data;
    } catch (error) {
        showAlert('Error al obtener lista de productos similares.', 'danger')
    } finally {
        processing.value = false
    }
}

const arrangeInputData = () => {
    return [props.baseId, state.productoSelectedId]
}

const add = async () => {
    try {
        processing.value = true
        const input = arrangeInputData()

        const result = await axios.post('/api/similares', input)
        if (!result.data.estado) {
            showAlert('Error al registrar el producto similar, contacte con el Administrador.', 'danger')
            return
        }

        getSimilares();
        emit('onAdded', input[0])
    } catch (error) {
        if (error.response.status == 403) {
            showAlert('No autorizado.', 'danger')
            return
        } else if (error.response.status == 422) {
            showAlert(error.response.data.message, 'danger')
            return
        }

        console.error(error)
        showAlert('Error al registrar el producto similar, contacte con el Administrador.', 'danger')
    } finally {
        processing.value = false
    }
}

const showAlert = (text, type = 'success') => {
    alert.shown = true
    alert.text = text
    alert.type = type
}

const customText = (item) => {
    return `${item.codigo_barras} ${item.nombre} ${item.descripcion}`
}

</script>
<template>
    <Notification v-if="alert.shown" :text="alert.text" :type="alert.type" />
    <Teleport to="body">
        <div class="modal show" tabindex="-1" role="dialog" aria-modal="true" style="display: block;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Medicamentos similares</h5>
                        <button @click="$emit('onClose')" type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="container d-flex flex-column">
                                <div class="mb-3">
                                    <label for="monto">Seleccione nuevo similar</label>
                                    <ModelListSelect data-cy="sel-producto-similar" v-model="state.productoSelectedId"
                                        :customText="customText" :list="getApplicableProducts ?? []" optionValue="id">
                                    </ModelListSelect>
                                </div>
                                <div>
                                    <table class="table" data-cy="tbl-similares">
                                        <thead>
                                            <tr>
                                                <th>Código</th>
                                                <th>Nombre</th>
                                                <th>Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="similar of similares" :key="similar.id">
                                                <td>{{ similar.codigo_barras }}</td>
                                                <td>
                                                    <a :href="'/productos?term=' + similar.codigo_barras">
                                                        {{ similar.nombre }}
                                                        <br>
                                                        <sub>{{ similar.descripcion }}</sub>
                                                    </a>
                                                </td>
                                                <td>{{ similar.stock }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <Button @click="$emit('onClose')" :disabled="processing" data-cy="btn-cancelar" type="danger"
                            data-dismiss="modal">
                            CANCELAR
                        </Button>
                        <Button cy="btn-agregar-similar" :disabled="processing" @click="add()" type="success">
                            OK <i class="fa fa-check-circle"></i>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop show"></div>
    </Teleport>
</template>
