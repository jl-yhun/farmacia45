<script setup>
import { nextTick, reactive } from 'vue';

defineProps(['tags', 'cy', 'list'])
const emit = defineEmits(['onAdded', 'onDeleted']);
const state = reactive({
    tag: ''
})

const addTag = ($event) => {
    nextTick(() => {
        if (state.tag == '' && ($event?.target.value == undefined || $event?.target.value == ''))
            return
        emit('onAdded', state.tag != '' ? state.tag : $event.target.value)
        state.tag = ''
    })
}

</script>

<template>
    <ModelListSelect :data-cy="cy" v-model="state.tag" :list="list ?? []" optionValue="nombre" @blur="addTag"
        optionText="nombre" @keyup.enter="addTag($event)">
    </ModelListSelect>
    <!-- <input :data-cy="cy" type="text" class="form-control" v-model="state.tag" @keyup.enter="addTag($event)"> -->
    <div>
        <span v-for="item in tags" class="badge bg-primary text-white ml-1">
            {{ item.nombre }}
            <i class="fa fa-times pointer m-1" @click="emit('onDeleted', item.id)"></i>
        </span>
    </div>
</template>
