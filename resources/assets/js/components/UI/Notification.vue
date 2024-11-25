<script setup>
defineProps(['text', 'type'])
defineEmits(['onClose'])
</script>

<template>
    <div data-cy="alert-validation_issue" class="notificacion alert alert-dismissible" :class="'alert-' + type" role="alert">
        <span class="mensaje">{{ text }}</span>
        <button @click="$emit('onClose')" type="button" class="close" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</template>
