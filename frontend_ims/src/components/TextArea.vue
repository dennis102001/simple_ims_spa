<template>
    <div class="flex flex-col w-full">
        <label :for="id" class="text-sm label-style-one">{{ label }}</label>
        <textarea
            v-model="model"
            :id="id"
            v-bind="$attrs"
            class="value-style resize-none h-16 "
        ></textarea>
        <p class="block ml-2 text-sm text-red-500 h-[20px]">
            {{ message ?? '' }}
        </p>
    </div>
</template>

<script setup>
defineProps({
    id: {
        type: String,
        required: true
    },
    label: {
        type: String,
        required: true
    },
    message: String,

})

const model = defineModel({
  required: true
})
</script>