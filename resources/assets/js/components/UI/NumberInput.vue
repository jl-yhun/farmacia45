<script setup>
defineProps(['modelValue', 'cy'])
defineEmits(['update:modelValue'])
</script>

<template>
    <input :data-cy="cy" type="number" class="form-control" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)">
</template>
