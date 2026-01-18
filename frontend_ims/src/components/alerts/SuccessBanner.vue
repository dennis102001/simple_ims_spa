<template>
    <div class="fixed z-50 flex items-center p-3 mb-4 text-green-800 -translate-x-1/2 border border-green-500 rounded-lg left-1/2 top-10 bg-green-50">
        <i class="justify-center text-center fa-solid fa-circle-check"></i>
        <div class="ml-4 text-sm font-medium">
            <span class="font-bold">
                <slot name="title"></slot> 
            </span>

            <span class="ml-1">
                <slot name="body"></slot>
            </span>
        </div>
        
        <button @click="closeBanner" type="button" class="size-[32px] bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 ">
            <i class="text-lg fa-solid fa-xmark"></i>
        </button>
    </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount } from 'vue'

const emit = defineEmits(['closeSuccess'])
let timeout = null

function closeBanner(){
    emit('closeSuccess')
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