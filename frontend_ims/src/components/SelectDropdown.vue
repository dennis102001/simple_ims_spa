<template>
    <div class="flex flex-col w-full h-[80px]">
        <p class="text-sm label-style-one">{{ label }}</p>
        <div ref="root" @click="toggle" class="relative cursor-pointer value-style">
            <div class="flex">
                <p v-if="selectedItem">
                    {{ selectedItem?.name }}
                </p>
                <p v-else class="text-gray-400">
                    None
                </p>
                <span class="ml-auto">
                    <i class="transition-all transform fa-solid fa-caret-down" :class="open ? '-rotate-180 ' : ''"></i>
                </span>
            </div>

            <Transition
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                enter-active-class="transition duration-200 transform"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
                leave-active-class="duration-200 transform transition"
            >
                <div v-if="open" class="z-20 p-3 absolute flex-col flex w-full bg-white rounded-md shadow-[0px_1px_4px_rgba(0,0,0,0.1)] translate-y-3 -translate-x-3 overflow-auto">
                    <ul class="max-h-40 overflow-auto">
                        <li @click.stop="select(null)" class="p-2 hover:bg-gray-50 cursor-pointer">
                            None
                        </li>

                        <li
                            v-for="item in listData"
                            @click.stop="select(item)"
                            class="p-2 hover:bg-gray-100 cursor-pointer"
                            :key="item.id"
                        >
                            {{ item.name }}
                        </li>
                    </ul>
                </div>
            </Transition>
        </div>
        </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref, computed } from 'vue';

const props = defineProps({
    label: {
        type: String,
        required: true
    },
    listData: {
        type: Array,
        default: () => []
    },
    rootClass: {
        type: String
    }
})

const model = defineModel({
    required: true
})

const open = ref(false)
const root = ref(null)

const selectedItem = computed(() => {
  return props.listData?.find(item => item.id === model.value)
})

function toggle(){
    open.value = !open.value
}

function select(item){
    model.value = item?.id ?? null
    open.value = false
}

function handleClickOutside(e) {
    if (root.value && !root.value.contains(e.target)) {
        open.value = false
        console.log('outside');
        
    }
}

onMounted(() => {
  document.addEventListener('mousedown', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('mousedown', handleClickOutside)
})
</script>