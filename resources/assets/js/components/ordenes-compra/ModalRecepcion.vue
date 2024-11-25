<script setup>
import { onMounted, reactive } from 'vue'

const endpoint = '/api/ordenes-compra'

const props = defineProps({
    ordenCompraId: Number,
    processing: {
        type: Boolean,
        default: false
    }
})
const emit = defineEmits(['onClose', 'onItemsUpdate', 'onConfirm'])

onMounted(() => {
    fetchOrdenCompra()
})


const state = reactive({
    orden: {},
})

const steps = reactive({
    items: [
        {
            target: '#step-1',
            content: 'Iguale cantidades/precios de compra con nota/factura de proveedor. De doble clic para editar.'
        },
        {
            target: '#step-2',
            content: 'Una vez verificado, confirme pedido.'
        }
    ],
    labels: {
        next: 'Entendido',
        prev: 'Regresar',
        finish: 'Finalizar',
        skip: 'Saltar'
    }
})

const alert = reactive({
    shown: false,
    text: '',
    type: 'info'
})

const fetchOrdenCompra = async () => {
    try {
        const result = await axios.get(`${endpoint}/${props.ordenCompraId}`)
        if (result.data.estado)
            state.orden = result.data.data
        else
            showAlert('Error al obtener el detalle de la orden de compra.', 'danger')
    } catch (error) {
        showAlert('Error al obtener el detalle de la orden de compra.', 'danger')
    }
}

const showAlert = (text, type = 'success') => {
    alert.shown = true
    alert.text = text
    alert.type = type
}

const setEditando = (producto, isEditando = true) => {
    producto.editando = isEditando
}

const patchItem = async (producto) => {
    try {
        const input = {
            'cantidad': producto.pivot.cantidad,
            'compra': producto.pivot.compra,
            'identificador': producto.identificador_proveedor
        }

        const result = await axios.patch(`${endpoint}/${state.orden.id}/items/${producto.id}`, input)
        if (result.data.estado) {
            showAlert('Se actualizó correctamente.')
            emit('onItemsUpdate', state.orden)
            producto.editando = false
        } else {
            showAlert('Error al actualizar el registro. Contacte al administrador.', 'danger')
        }

    } catch (error) {
        showAlert(error.response.data.message, 'danger')
    }
}

</script>
<template>
    <Teleport to="body">
        <Notification @onClose="alert.shown = false" v-if="alert.shown" :text="alert.text" :type="alert.type">
        </Notification>
        <div class="modal show" tabindex="-1" role="dialog" aria-modal="true" style="display: block;">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirme productos recibidos. Orden compra # {{ state.orden.id }}</h5>
                        <button @click="$emit('onClose')" type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="container d-flex flex-column">
                                <table class="tabla-items table">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th id="step-1">Cantidad</th>
                                            <th>Compra</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="pointer" v-for="producto in state.orden?.productos" :key="producto.id">
                                            <td>{{ producto.codigo_barras }}</td>
                                            <td class="flex flex-column">
                                                <div>
                                                    <span class="badge badge-primary mx-1" v-for="tag in producto.tags">
                                                        {{ tag.nombre }}
                                                    </span>
                                                </div>
                                                <div>
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
                                                    &nbsp;
                                                    <a :href="'/productos?term=' + producto.codigo_barras">
                                                        {{ producto.nombre }}<br>
                                                        <sub>{{ producto.descripcion }}</sub>
                                                    </a>
                                                </div>
                                            </td>
                                            <td @dblclick="setEditando(producto)">
                                                <template v-if="!producto.editando">
                                                    {{ producto.pivot.cantidad }}
                                                </template>
                                                <NumberInput v-else @keyup.esc="setEditando(producto, false)"
                                                    @keyup.enter="patchItem(producto)" v-model="producto.pivot.cantidad">
                                                </NumberInput>
                                            </td>
                                            <td @dblclick="setEditando(producto)">
                                                <template v-if="!producto.editando">
                                                    {{ producto.pivot.compra }}
                                                </template>
                                                <NumberInput v-else @keyup.esc="setEditando(producto, false)"
                                                    @keyup.enter="patchItem(producto)" v-model="producto.pivot.compra">
                                                </NumberInput>
                                            </td>
                                            <td>
                                                {{ (producto.pivot.compra * producto.pivot.cantidad).toFixed(2) }}
                                            </td>
                                            <!-- <td class="text-center">
                                                <input data-cy="check-confirmar" class="check-confirmar" type="checkbox"
                                                    v-model="producto.confirmado" />
                                            </td> -->
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <Button :disabled="props.processing" @click="$emit('onClose')" data-cy="btn-cancelar" type="info" data-dismiss="modal">
                            CANCELAR
                        </Button>
                        <Button :disabled="props.processing" id="step-2" @click="$emit('onConfirm')" data-cy="btn-confirmar"
                            type="success">
                            CONFIRMAR
                        </Button>
                    </div>
                </div>
            </div>
        </div>
        <VTour :buttonLabels="steps.labels" :steps="steps.items" autoStart></VTour>
        <div class="modal-backdrop show"></div>
    </Teleport>
</template>
<style>
.tabla-items {
    display: grid;
    grid-template-columns: 1fr 2fr repeat(3, 1fr);
    max-height: 60vh;
    overflow-y: auto;
}

.tabla-items thead,
.tabla-items tbody,
.tabla-items tr {
    display: contents;
}

.tabla-items thead tr th {
    position: sticky;
    background-color: white;
    top: 0;
    z-index: 1000;
}

.check-confirmar {
    transform: scale(1.5);
}

@media(max-height: 570px) {
    .tabla-items {
        max-height: 50vh;
    }
}
</style>
