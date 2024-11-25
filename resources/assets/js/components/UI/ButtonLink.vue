<script setup>
defineProps({
    type: {
        type: String,
        default: 'primary'
    },
    href: String
})
defineEmits(['click'])
</script>

<template>
    <a :href="href" class="btn" :class="'btn-' + type" @click="$emit('click')">
        <slot></slot>
    </a>
</template>