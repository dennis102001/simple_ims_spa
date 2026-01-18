<template>
    <Teleport to="body">
        <transition
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            enter-active-class="transition duration-500 ease-in-out transform"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
            leave-active-class="transition duration-500 ease-in-out transform"
        >
            <div v-show="inUse" class="modal fixed top-0 z-50 w-full h-full overflow-hidden bg-black/30 ">
                <div @click.self="closeModal" class="p-2 sm:p-4 md:p-6 lg:p-8 size-full overflow-auto grid place-items-center">
                    <transition
                        :enter-from-class="popUpTransition.enterFrom"
                        :enter-to-class="popUpTransition.enterTo"
                        :enter-active-class="popUpTransition.enterActive"
                        :leave-from-class="popUpTransition.leaveFrom"
                        :leave-to-class="popUpTransition.leaveTo"
                        :leave-active-class="popUpTransition.leaveActive"
                    >
                    
                        <slot v-if="inUse"></slot>
                    </transition>
                </div>
            </div> 
        </transition>
    </Teleport>
</template>

<script setup>
import { usePopUpTransition } from '../composables/useTransition';

const popUpTransition = usePopUpTransition()
const emit = defineEmits(['close'])

defineProps({
    inUse: {type: Boolean, required: true},
})

function closeModal(){
    emit('close')
}

</script>

<style scoped>

</style>