// TODO: Refactor this one to CurrencyInputGroup once e2e testing is ready
<script setup>
defineProps(['modelValue', 'cy', 'disabled'])
defineEmits(['update:modelValue'])
</script>

<template>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">$</span>
        </div>
        <input :data-cy="cy" :disabled="disabled" type="number" class="form-control" :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)">
    </div>
</template>