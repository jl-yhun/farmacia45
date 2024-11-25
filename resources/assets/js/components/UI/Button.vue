<script setup>
defineProps({
    type: {
        type: String,
        default: 'primary'
    },
    cy: String
})
</script>

<template>
    <button :data-cy="cy" class="btn" :class="'btn-' + $props.type" v-bind="$attrs">
        <slot></slot>
    </button>
</template>