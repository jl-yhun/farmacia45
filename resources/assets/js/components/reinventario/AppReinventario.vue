<script setup>
import { onMounted, reactive, watch } from 'vue'
import Notification from '../UI/Notification'

onMounted(() => {
    getDraftList()
})
const endpoint = '/api/inventario'

const state = reactive({
    codigo_barras: '',
    productos: []
})

const alert = reactive({
    shown: false,
    text: '',
    type: 'info'
})

const getDraftList = async () => {
    const response = await axios.get(endpoint)
    if (!response.data.estado)
        showAlert('Error al obtener la lista de lecturas hechas hasta ahora, recargue la página nuevamente.', 'danger')
    else
        state.lecturas = response.data.data.reverse()
}

const saveLectura = async () => {
    try {
        const resp = await axios.post(endpoint, state)
        if (!resp.data.estado)
            showAlert(resp.data.data, 'danger')
        else {
            addToListOfProducts(resp.data.data)
            state.codigo_barras = ''
        }
    } catch (error) {
        showAlert('Error al registrar entrada.', 'danger')
    }
}

const patchItem = async (item) => {
    try {
        const resp = await axios.patch(endpoint + `/${item.producto.codigo_barras}`, item)
        if (!resp.data.estado)
            showAlert('Error al guardar el cambio, vuelva a intentar.', 'danger')
        else {
            updateInListOfProducts(item)
            showAlert(`Código '${item.producto.codigo_barras}' actualizado.`)
        }
    } catch (error) {
        showAlert('Error al actualizar entrada.', 'danger')
    } finally {
        item.editando = false
    }
}

const destroy = async (codigo_barras) => {
    try {
        const resp = await axios.delete(endpoint + `/${codigo_barras}`)
        if (!resp.data.estado)
            showAlert('Error al eliminar entrada, vuelva a intentar.', 'danger')
        else
            removeFromListOfProducts(codigo_barras)
    } catch (error) {
        showAlert('Error al eliminar entrada, vuelva a intentar.', 'danger')
    }
}

const findByCodigo = (codigoBarras) => state.lecturas.findIndex(c => c.producto.codigo_barras == codigoBarras)

const addToListOfProducts = (item) => {
    const productFoundIndex = findByCodigo(item.producto.codigo_barras)
    if (productFoundIndex == -1) {
        state.lecturas.unshift({
            ...item
        })
        showAlert(`Código '${state.codigo_barras}' agregado.`)
    }
    else {
        state.lecturas[productFoundIndex].cantidad++
        showAlert(`Cantidad del código '${state.codigo_barras}' actualizada a '${state.lecturas[productFoundIndex].cantidad}'.`)
    }
}

const updateInListOfProducts = (item) => {
    const productFoundIndex = findByCodigo(item.producto.codigo_barras)

    state.lecturas[productFoundIndex] = item
}

const removeFromListOfProducts = (codigoBarras) => {
    const productFoundIndex = findByCodigo(codigoBarras)
    state.lecturas.splice(productFoundIndex, 1)
}

const setEditando = (item, editando = true) => {
    item.editando = editando
}

const showAlert = (text, type = 'success') => {
    alert.shown = true
    alert.text = text
    alert.type = type
}

watch(alert, (newValue) => {
    if (newValue.shown) {
        setTimeout(() => {
            alert.shown = false
        }, 2000)
    }
})
</script>
<template>
    <Notification @onClose="alert.shown = false" v-if="alert.shown" :text="alert.text" :type="alert.type"></Notification>

    <div class="d-flex flex-column mx-5">
        <div class="d-flex justify-content-between">
            <h1 class="my-4">Re-inventario físico</h1>
            <ButtonLink href="/inventario/diff" type="success" class="align-self-center">
                <i class="fa fa-eye"></i> Ver diferencias
            </ButtonLink>
        </div>
        <div class="form-group">
            <label for="lectura">Entre código de barras</label>
            <TextInput id="lectura" autofocus v-model="state.codigo_barras" @keypress.enter="saveLectura"></TextInput>
        </div>
        <table class="tabla-lecturas table">
            <thead>
                <tr>
                    <th>Código barras</th>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Compra</th>
                    <th>Venta</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, i) of state.lecturas" :key="item.id">
                    <td>{{ item.producto.codigo_barras }}</td>
                    <td>
                        {{ item.producto.nombre }}
                        <br>
                        <sub>{{ item.producto.descripcion }}</sub>
                    </td>
                    <td @dblclick="setEditando(item)">
                        <template v-if="!item.editando">
                            {{ item.cantidad }}
                        </template>
                        <NumberInput @keyup.esc="setEditando(item, false)" @keyup.enter="patchItem(item)" v-else
                            v-model="item.cantidad"></NumberInput>
                    </td>
                    <td @dblclick="setEditando(item)">
                        <template v-if="!item.editando">
                            {{ item.compra }}
                        </template>
                        <NumberInputGroup @keyup.esc="setEditando(item, false)" @keyup.enter="patchItem(item)" v-else
                            v-model="item.compra"></NumberInputGroup>
                    </td>
                    <td @dblclick="setEditando(item)">
                        <template v-if="!item.editando">
                            {{ item.venta }}
                        </template>
                        <NumberInputGroup @keyup.esc="setEditando(item, false)" @keyup.enter="patchItem(item)" v-else
                            v-model="item.venta"></NumberInputGroup>
                    </td>
                    <td>
                        <Button @click="destroy(item.producto.codigo_barras)" type="danger" data-toggle='tooltip' title='Eliminar'>
                            <i class="fa fa-ban"></i>
                        </Button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<style>
.bg-blue {
    background-color: #5c8eb4 !important;
}

.tabla-lecturas {
    display: grid;
    grid-template-columns: 1fr 3fr repeat(4, 1fr);
    max-height: 70vh;
    overflow-y: auto;
    overflow-x: hidden;
}

.tabla-lecturas thead,
.tabla-lecturas tbody,
.tabla-lecturas tr {
    display: contents;
}

.tabla-lecturas thead tr th {
    position: sticky;
    top: 0;
    background-color: white;
}

@media(max-height: 868px) {

    .tabla-lecturas {
        max-height: 60vh;
    }
}

@media(max-height: 600px) {

    .tabla-lecturas {
        max-height: 50vh;
    }
}

@media(max-height: 500px) {

    .tabla-lecturas {
        max-height: 40vh;
    }
}
</style>
