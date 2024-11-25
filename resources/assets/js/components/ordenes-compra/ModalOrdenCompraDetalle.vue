<script setup>
import { onMounted, reactive, computed } from 'vue'

const endpoint = '/api/ordenes-compra'

const props = defineProps({
    ordenCompraId: Number
})
const emit = defineEmits(['onClose', 'onItemsUpdate'])

onMounted(() => {
    fetchOrdenCompra()
})


const state = reactive({
    orden: {},
    shownDetalle: false
})

const editable = computed(() => state.orden?.estado == 'Pendiente')

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
    if (editable.value)
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

const removeItem = async (producto) => {
    try {
        const result = await axios.delete(`${endpoint}/${state.orden.id}/items/${producto.id}`)

        if (result.data.estado) {
            showAlert('Se eliminó correctamente.')

            const index = state.orden.productos.findIndex(c => c.id == producto.id)
            state.orden.productos.splice(index, 1)

            emit('onItemsUpdate', state.orden)
        } else {
            showAlert('Error al eliminar el registro. Contacte al administrador.', 'danger')
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
                        <h5 class="modal-title">{{ state.orden?.proveedor?.nombre }} - orden compra # {{ state.orden.id }}
                        </h5>
                        <button @click="$emit('onClose')" type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="tabla-items-detalle table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Cantidad</th>
                                    <th>Compra</th>
                                    <th>Subtotal</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr :class="{ 'pointer': editable }" @dblclick="setEditando(producto)"
                                    v-for="producto in state.orden?.productos" :key="producto.id">
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
                                    <td>
                                        <template v-if="!producto.editando">
                                            {{ producto.pivot.cantidad }}
                                        </template>
                                        <NumberInput v-else @keyup.esc="setEditando(producto, false)"
                                            @keyup.enter="patchItem(producto)" v-model="producto.pivot.cantidad">
                                        </NumberInput>
                                    </td>
                                    <td>
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
                                    <td>
                                        <Button v-if="editable" @click="removeItem(producto)" type="danger" title="Quitar">
                                            <i class="fa fa-ban"></i>
                                        </Button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <h5 class="mr-5">Total: $ {{ state.orden?.total }}</h5>
                        <Button @click="$emit('onClose')" data-cy="btn-cancelar" type="info" data-dismiss="modal">
                            OK
                        </Button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop show"></div>
    </Teleport>
</template>
<style>
.tabla-items-detalle {
    display: grid;
    grid-template-columns: 1fr 3fr repeat(4, 1fr);
    max-height: 60vh;
    overflow-y: auto;
}

.tabla-items-detalle thead,
.tabla-items-detalle tbody,
.tabla-items-detalle tr {
    display: contents;
}

.tabla-items-detalle thead tr th {
    position: sticky;
    background-color: white;
    top: 0;
    z-index: 1000;
}

@media(max-height: 570px) {
    .tabla-items-detalle {
        max-height: 50vh;
    }
}
</style>
