<template>
    <div class="w-[calc(100%-2rem)] max-w-sm min-w-[200px] fixed z-50 flex items-start mb-4 text-red-800 -translate-x-1/2 border border-red-500 rounded-lg left-1/2 top-10 bg-red-50/80 backdrop-blur-sm">
        <div class="py-2 px-3 ">
            <i class=" text-center fa-solid fa-circle-info"></i>
        </div>

        <div class="py-2.5 pr-4">
            <div class="font-bold text-sm">
                <slot name="title"></slot> 
            </div>

            <div class="text-xs">
                <slot name="body"></slot>
            </div>
        </div>
        
        <div class="p-1 ml-auto">
            <button @click="closeBanner" type="button" class="py-1 px-2.5 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 hover:bg-red-100">
                <i class="text-center fa-solid fa-xmark"></i>
            </button>
        </div>
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