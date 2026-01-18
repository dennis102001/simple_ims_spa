<template>
    <button 
        :class="['w-fit h-fit p-2 text-sm font-semibold rounded-full text-nowrap tracking-wider', 
        'shadow-[0px_1px_4px_rgba(0,0,0,0.1)] hover:shadow-[0px_1px_4px_rgba(0,0,0,0.3)]',
        'bg-white  text-darkgray-ter ',
        'transition-all transform duration-200 active:translate-y-[2px] ',
        'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600', 
            widthClass
        ]"
    >
        <slot></slot>
    </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    width: { type: String, default: "fit" }
})

const widthClass = computed(() => {
    return props.width === "full" ? "w-full" : "w-fit"
})

</script>

<style scoped>

</style>