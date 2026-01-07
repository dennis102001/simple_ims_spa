<template>
    <div class="fixed z-50 flex items-center p-3 mb-4 text-red-800 -translate-x-1/2 border border-red-500 rounded-lg left-1/2 top-10 bg-red-50">
        <i class="justify-center text-center fa-solid fa-circle-info"></i>
        <div class="ml-4 text-sm font-medium">
            <span class="font-bold">
                <slot name="title"></slot> 
            </span>
            <span class="ml-1">
                <slot name="body"></slot>
            </span>
        </div>
        <button @click="closeBanner" type="button" class="size-[32px] bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 ">
            <i class="text-lg fa-solid fa-xmark"></i>
        </button>
    </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted } from 'vue';

const emit = defineEmits(['closeError'])
let timeout = null

function closeBanner(){
    emit('closeError')
}

onMounted(() => {
    if (timeout) clearTimeout(timeout)

    timeout = setTimeout(() => {
        closeBanner()
    }, 5000)
})

onBeforeUnmount(() => {
    if (timeout) {
       clearTimeout(timeout)
       timeout = null 
    }
})
</script>

<style scoped>

</style>