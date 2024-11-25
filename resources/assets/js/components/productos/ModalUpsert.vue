<script setup>
import { nextTick } from 'process';
import { onMounted, reactive } from 'vue'

const props = defineProps(['producto'])

onMounted(() => {
    fetchListOfCategories();
    getTagsList();
    if (state.editing)
        syncTags();
    nextTick(() => {
        document.getElementById('nombre').focus()
    })
})

const emit = defineEmits(['onClose', 'onConfirm', 'onTagsUpdated'])

const state = reactive({
    ...props.producto,
    editing: props.producto != null,
    min_stock: props.producto?.min_stock ?? 2,
    max_stock: props.producto?.max_stock ?? 3,
    isGranel: props.producto?.isGranel ?? 0
})

const secondaryState = reactive({
    listaCategorias: [],
    listaTags: [],
    processing: false
})

const alert = reactive({
    shown: false,
    text: '',
    type: 'info'
})

const fetchListOfCategories = async () => {
    try {
        const result = await axios.get('/api/categorias')

        if (result.data.estado)
            secondaryState.listaCategorias = result.data.data
        else
            showAlert('Error al obtener la lista de categorías.', 'danger')
    } catch (error) {
        showAlert('Error al obtener la lista de categorías.', 'danger')
    }
}

const showAlert = (text, type = 'success') => {
    alert.shown = true
    alert.text = text
    alert.type = type
}

const syncTags = async () => {
    try {
        const result = await axios.get(`/api/productos/${state.id}/tags`)
        if (result.data.estado) {
            state.tags = result.data.data;
            emit('onTagsUpdated', { 'productoId': state.id, 'tags': state.tags })
        }
        else
            showAlert('Error al obtener los tags, inténtelo nuevamente.', 'danger')
    } catch (error) {
        console.error(error)
        showAlert('Error al obtener los tags, contacte al admin.', 'danger')
    }
}

const addTag = async (tag) => {
    try {
        const result = await axios.post(`/api/productos/${state.id}/tags`, { 'nombre': tag })
        if (result.data.estado)
            await syncTags()
        else
            showAlert('Error al agregar tag, inténtelo nuevamente.', 'danger')
    } catch (error) {
        console.error(error)
        showAlert('Error al agregar tag, contacte al admin.', 'danger')
    }
}

const deleteTag = async (tagId) => {
    try {
        const result = await axios.delete(`/api/productos/${state.id}/tags/${tagId}`)
        if (result.data.estado)
            await syncTags()
        else
            showAlert('Error al eliminar tag, inténtelo nuevamente.', 'danger')
    } catch (error) {
        console.error(error)
        showAlert('Error al eliminar tag, contacte al admin.', 'danger')
    }
}

const getTagsList = async () => {
    try {
        const result = await axios.get('/api/tags')
        if (result.data.estado)
            secondaryState.listaTags = result.data.data
        else
            showAlert('Error al obtener tags, inténtelo nuevamente.', 'danger')
    } catch (error) {
        console.error(error)
        showAlert('Error al obtener tags, contacte al admin.', 'danger')
    }
}
</script>
<template>
    <Teleport to="body">
        <Notification @onClose="alert.shown = false" v-if="alert.shown" :text="alert.text" :type="alert.type">
        </Notification>
        <div class="modal show" tabindex="-1" role="dialog" aria-modal="true" style="display: block;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ state.editing ? 'Editar' : 'Añadir' }} producto</h5>
                        <button @click="$emit('onClose')" type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="container d-flex flex-column">
                                <div class="mb-3 d-flex">
                                    <div class="mr-1 w-50">
                                        <label for="nombre">Nombre</label>
                                        <TextInput cy="txt-nombre" id="nombre" v-model="state.nombre"></TextInput>
                                    </div>
                                    <div v-if="state.editing" class="ml-1 w-50">
                                        <label for="labels">Labels</label>
                                        <InputTag :tags="state.tags" :list="secondaryState.listaTags"
                                            @onAdded="addTag($event)" @onDeleted="deleteTag($event)"></InputTag>
                                    </div>
                                </div>
                                <div class="mb-3 d-flex">
                                    <div class="mr-1 w-50">
                                        <label for="codigo_barras">Código de barras</label>
                                        <TextInput cy="txt-codigo_barras" id="codigo_barras"
                                            v-model="state.codigo_barras">
                                        </TextInput>
                                    </div>
                                    <div class="ml-1 w-50">
                                        <label for="caducidad">Caducidad</label>
                                        <DateInput cy="txt-caducidad" id="caducidad" v-model="state.caducidad">
                                        </DateInput>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="descripcion">Descripción</label>
                                    <TextArea cy="txt-descripcion" id="descripcion"
                                        v-model="state.descripcion"></TextArea>
                                </div>
                                <div class="mb-3 d-flex">
                                    <div class="mr-1 w-50">
                                        <label for="compra">Precio compra</label>
                                        <NumberInputGroup cy="txt-compra" id="compra" v-model="state.compra">
                                        </NumberInputGroup>
                                    </div>
                                    <div class="ml-1 w-50">
                                        <label for="venta">Precio de venta</label>
                                        <NumberInputGroup cy="txt-venta" id="venta" v-model="state.venta">
                                        </NumberInputGroup>
                                    </div>
                                </div>
                                <div class="mb-3 d-flex">
                                    <div class="mr-1 w-50">
                                        <label for="categoria">Categoría</label>
                                        <Select cy="sel-categoria" id="categoria" v-model="state.categoria_id">
                                            <option value="">--Categoría--</option>
                                            <option :value="categoria.id"
                                                v-for="categoria in secondaryState.listaCategorias" :key="categoria.id">
                                                {{ categoria.nombre }}
                                            </option>
                                        </Select>
                                    </div>
                                    <div class="ml-1 w-50">
                                        <label for="stock">Stock</label>
                                        <NumberInput cy="txt-stock" id="stock" v-model="state.stock"></NumberInput>
                                    </div>
                                </div>
                                <div class="mb-3 d-flex">
                                    <div class="mr-1 w-50">
                                        <label for="min_stock">Stock mínimo</label>
                                        <NumberInput cy="txt-min_stock" id="min_stock" v-model="state.min_stock">
                                        </NumberInput>
                                    </div>
                                    <div class="ml-1 w-50">
                                        <label for="max_stock">Stock máximo</label>
                                        <NumberInput cy="txt-max_stock" id="max_stock" v-model="state.max_stock">
                                        </NumberInput>
                                    </div>
                                </div>
                                <div class="mb-3 d-flex align-items-center">
                                    <div class="mr-1 w-50">
                                        <label for="isGranel">A granel?</label><br>
                                        <Select cy="sel-is_granel" id="isGranel" v-model="state.isGranel">
                                            <option value=0>No</option>
                                            <option value=1>Si</option>
                                        </Select>
                                    </div>
                                    <div class="ml-1 w-50" v-if="state.isGranel == 1">
                                        <label for="unidades_paquete">Unidades por paquete</label>
                                        <NumberInput cy="txt-unidades_paquete" id="unidades_paquete"
                                            v-model="state.unidades_paquete">
                                        </NumberInput>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <Button cy="btn-cancelar" :disabled="secondaryState.processing" @click="$emit('onClose')"
                            data-cy="btn-cancelar" type="danger" data-dismiss="modal">
                            CANCELAR
                        </Button>
                        <Button cy="btn-ok" @click="$emit('onConfirm', state)" :disabled="secondaryState.processing"
                            data-cy="btn-ok" type="success">
                            OK <i class="fa fa-check-circle"></i>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop show"></div>
    </Teleport>
</template>
