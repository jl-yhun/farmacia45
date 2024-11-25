<script setup>
import { reactive, ref } from 'vue'

const state = reactive({
    monto: '',
    compania: 'Telcel',
    metodo_pago: 'Efectivo'
})

const processing = ref(false)

const alert = reactive({
    shown: false,
    text: '',
    type: 'info'
})

const add = async () => {
    try {
        processing.value = true
        const result = await axios.post('/api/pago-servicios/recargas', state)
        if (!result.data.estado) {
            showAlert('Error al registrar la recarga, verifique que la caja esté abierta.', 'danger')
            return
        }
        document.querySelector('[data-dismiss="modal"]').click()
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
        <h5 class="modal-title">Registrar recarga telefónica</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="container">
            <div class="container d-flex flex-column">
                <div class="mb-3">
                    <label for="monto">Monto</label>
                    <NumberInputGroup cy="txt-recargas-monto" id="monto" v-model="state.monto"></NumberInputGroup>
                </div>
                <div>
                    <label for="metodo_pago">Método de pago</label>
                    <Select cy="sel-recargas-metodo-pago" id="metodo_pago" v-model="state.metodo_pago">
                        <option>Efectivo</option>
                        <!-- <option>Tarjeta de débito</option>
                        <option>Tarjeta de crédito</option> -->
                    </Select>
                </div>
                <div>
                    <label for="compania">Compañía</label>
                    <Select cy="sel-recargas-compania" id="compania" v-model="state.compania">
                        <option>Telcel</option>
                        <option>Movistar</option>
                        <option>AT&T</option>
                        <option>Otra</option>
                    </Select>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <Button :disabled="processing" data-cy="btn-recargas-cancelar" type="danger" data-dismiss="modal">
            CANCELAR
        </Button>
        <Button :disabled="processing" data-cy="btn-recargas-ok" @click="add()" type="success">
            OK <i class="fa fa-check-circle"></i>
        </Button>
    </div>
</template>
