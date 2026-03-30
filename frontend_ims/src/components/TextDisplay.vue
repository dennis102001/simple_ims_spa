<template>
    <div class="flex flex-col w-full h-[80px]">
        <p class="text-sm label-style-one">{{ label }}</p>
        <p v-if="value" class="cursor-default value-style">
            {{ value }}
        </p>
        <p v-else class="text-gray-500 cursor-default value-style">
            -
        </p>
    </div>
</template>

<script setup>
defineProps({
    value: {
        type: String,
    },
    label: {
        type: String,
        required: true,
    },

})

</script>