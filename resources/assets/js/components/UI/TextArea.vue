<script setup>
defineProps(['modelValue', 'cy'])
defineEmits(['update:modelValue'])
</script>

<template>
    <textarea :data-cy="cy" class="form-control" @input="$emit('update:modelValue', $event.target.value)">{{ modelValue }}</textarea>
</template>