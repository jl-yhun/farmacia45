<script setup>
import { nextTick } from 'process';
import { computed, onMounted, reactive } from 'vue'

const props = defineProps(['productoId'])

onMounted(() => {
    fetchListOfDealers();
    nextTick(() => {
        document?.getElementById('cantidad').focus()
    })
})

defineEmits(['onClose', 'onConfirm'])

const state = reactive({
    proveedor_id: 0,
    cantidad: 1
})

const secondaryState = reactive({
    listaProveedores: [],
    processing: false
})

const precioCompra = computed(() => secondaryState.listaProveedores.find(c => c.id == state.proveedor_id)?.pivot.precio)

const alert = reactive({
    shown: false,
    text: '',
    type: 'info'
})

const arrangeListOfDealers = (lista) => {
    lista = lista.filter(c => c.pivot.disponible)
    return sortProveedores(lista)
}

const sortProveedores = (lista) => {
    return lista.sort((a, b) => {
        return (+b.pivot.default - +a.pivot.default) || (a.pivot.precio - b.pivot.precio)
    })
}

const fetchListOfDealers = async () => {
    try {
        const result = await axios.get(`/api/productos/${props.productoId}/proveedores`)

        if (result.data.estado) {
            secondaryState.listaProveedores = arrangeListOfDealers(result.data.data)
            state.proveedor_id = secondaryState.listaProveedores[0].id
        }
        else
            showAlert('Error al obtener la lista de proveedores.', 'danger')
    } catch (error) {
        showAlert('Error al obtener la lista de proveedores.', 'danger')
    }
}

const showAlert = (text, type = 'success') => {
    alert.shown = true
    alert.text = text
    alert.type = type
}
</script>
<template>
    <Teleport to="body">
        <Notification @onClose="alert.shown = false" v-if="alert.shown" :text="alert.text" :type="alert.type">
        </Notification>
        <div class="modal show" tabindex="-1" role="dialog" aria-modal="true" style="display: block;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Seleccione un proveedor</h5>
                        <button @click="$emit('onClose')" type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="container d-flex flex-column">
                                <div class="mb-3">
                                    <label for="proveedor">Proveedor</label>
                                    <Select id="proveedor" v-model="state.proveedor_id">
                                        <option :value="proveedor.id" v-for="proveedor in secondaryState.listaProveedores"
                                            :key="proveedor.id">
                                            {{ proveedor.nombre }}
                                        </option>
                                    </Select>
                                </div>
                                <div class="mb-3 d-flex">
                                    <div class="mr-1">
                                        <label for="cantidad">Cantidad</label>
                                        <NumberInput id="cantidad" v-model="state.cantidad"></NumberInput>
                                    </div>
                                    <div class="ml-1">
                                        <label for="compra">Precio de compra</label>
                                        <NumberInputGroup v-model="precioCompra" :disabled="true" id="compra">
                                        </NumberInputGroup>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <Button :disabled="secondaryState.processing" @click="$emit('onClose')" data-cy="btn-cancelar"
                            type="danger" data-dismiss="modal">
                            CANCELAR
                        </Button>
                        <Button @click="$emit('onConfirm', state)" :disabled="secondaryState.processing" data-cy="btn-ok"
                            type="success">
                            OK <i class="fa fa-check-circle"></i>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop show"></div>
    </Teleport>
</template>
