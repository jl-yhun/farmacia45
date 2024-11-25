<script setup>
defineProps({
    msg: String,
    processing: {
        type: Boolean,
        default: false
    }
})
defineEmits(['onClose', 'onConfirm'])
</script>

<template>
    <Teleport to="body">
        <div class="modal show" tabindex="-1" role="dialog" aria-modal="true" style="display: block;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmación</h5>
                        <button type="button" class="close" aria-label="Close" @click="$emit('onClose')">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-html="msg">
                    </div>
                    <div class="modal-footer">
                        <button :disabled="processing" type="button" class="btn btn-secondary"
                            @click="$emit('onClose')">Cancelar</button>
                        <button :disabled="processing" data-cy="btn-ok" type="button" class="btn btn-primary"
                            @click="$emit('onConfirm')">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop show"></div>
    </Teleport>
</template>