<script setup>
import { reactive, ref, watch } from 'vue'

const state = reactive({
    monto: '',
    concepto: '',
    fuente: 'Caja',
    tipo: 'Apartado'
})

const sourceState = reactive({
    tipos: ['Apartado', 'Gasto', 'Transferencia'],
    fuentes: {
        Apartado: ['Caja'],
        Gasto: ['Caja', 'Mercado Pago', 'Apartados', 'Recargas Servicios'],
        Transferencia: ['Efectivo a Electrónico', 'Electrónico a Efectivo'],
    },
    uris: {
        Apartado: 'apartados',
        Gasto: 'gastos',
        Transferencia: 'transferencias'
    }
})

const processing = ref(false)

const alert = reactive({
    shown: false,
    text: '',
    type: 'info'
})

const applyBusinessRules = () => {
    let uri = sourceState.uris[state.tipo]
    let monto = state.monto
    // If tipo = gasto and fuente = apartados
    // monto should be negative
    if (state.fuente == 'Apartados') {
        uri = 'apartados'
        monto = -monto
    }

    // If Transferencia then 'fuente' should be 'tipo'
    if (state.tipo == 'Transferencia') {
        state.tipo = state.fuente
    }

    return { uri, monto }
}

const add = async () => {
    try {
        let { monto, uri } = applyBusinessRules()

        processing.value = true
        const result = await axios.post(`/api/${uri}`, {
            ...state,
            monto
        })
        if (!result.data.estado) {
            showAlert('Error al registrar el movimiento.', 'danger')
            return
        }
        document.querySelector('[data-dismiss="modal"]').click()
        // TODO: [Improvable] no refresh
        location.reload();
    } catch (error) {
        showAlert(error.response.data.message, 'danger')
    } finally {
        processing.value = false
    }
}

const showAlert = (text, type = 'success') => {
    alert.shown = true
    alert.text = text
    alert.type = type
}
</script>
<template>
    <Notification v-if="alert.shown" :text="alert.text" :type="alert.type" />
    <div class="modal-header">
        <h5 class="modal-title">Registrar movimiento</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="container">
            <div class="container d-flex flex-column">
                <div class="mb-3">
                    <label for="tipo">Tipo</label>
                    <Select id="tipo" data-cy="sel-gastos-tipo" v-model="state.tipo">
                        <option v-for="tipo of sourceState.tipos" :key="tipo">
                            {{ tipo }}
                        </option>
                    </Select>
                </div>
                <div class="mb-3">
                    <label for="fuente">{{ state.tipo == 'Transferencia' ? 'Tipo transferencia' : 'Fuente' }}</label>
                    <Select id="fuente" data-cy="sel-gastos-fuente" v-model="state.fuente">
                        <option v-for="fuente of sourceState.fuentes[state.tipo]" :key="fuente">
                            {{ fuente }}
                        </option>
                    </Select>
                </div>
                <div class="mb-3">
                    <label for="monto">Monto</label>
                    <NumberInputGroup id="monto" data-cy="txt-gastos-monto" v-model="state.monto"></NumberInputGroup>
                </div>
                <div>
                    <label for="concepto">Concepto</label>
                    <TextArea id="concepto" data-cy="txt-gastos-concepto" v-model="state.concepto"></TextArea>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <Button :disabled="processing" data-cy="btn-gastos-cancelar" type="danger" data-dismiss="modal">
            CANCELAR
        </Button>
        <Button :disabled="processing" data-cy="btn-gastos-ok" @click="add()" type="success">
            OK <i class="fa fa-check-circle"></i>
        </Button>
    </div>
</template>
