<script setup>
defineProps(['modelValue', 'cy'])
defineEmits(['update:modelValue'])
</script>

<template>
    <select :data-cy="cy" class="form-control" :value="modelValue" @change="$emit('update:modelValue', $event.target.value)">
        <slot></slot>
    </select>
</template>