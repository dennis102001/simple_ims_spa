<template>
  <transition
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    enter-active-class="transition duration-300 ease-in-out transform"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
    leave-active-class="transition duration-300 ease-in-out transform"
  >
    <div v-show="inUse" class="modal absolute top-0 z-50 w-full h-full overflow-hidden bg-black/30 ">
      <div class="p-2 size-full overflow-auto grid place-items-center">
        <transition
          :enter-from-class="popUpTransition.enterFrom"
          :enter-to-class="popUpTransition.enterTo"
          :enter-active-class="popUpTransition.enterActive"
          :leave-from-class="popUpTransition.leaveFrom"
          :leave-to-class="popUpTransition.leaveTo"
          :leave-active-class="popUpTransition.leaveActive"
        >
          <div v-if="inUse" class="w-full max-w-sm mb-4 bg-white shadow-md  rounded-xl">
            <div class="flex flex-col items-center rounded-t-xl overflow-hidden">
              <div class="flex flex-col items-center  py-4">
                <div class="text-red-500 bg-gray-100 size-12 text-2xl rounded-full items-center justify-center flex mb-2">
                  <i class="fa-solid fa-warning"></i>
                </div>
                
                <h4 class="bg-white text-center font-semibold tracking-wider w-full px-6  gap-3">
                  <slot name="title"></slot>
                </h4>

                <h6 class="text-center w-full tracking-wider px-6">
                    <slot name="body"></slot>
                </h6>
              </div>
              <div class="grid w-full grid-cols-2 gap-4 px-6 pt-2 pb-6 rounded-b-xl">
                <ButtonRed @click="$emit('yes')" width="full">
                  Delete
                </ButtonRed>

                <ButtonWhite @click="$emit('cancel')" width="full">
                  Cancel
                </ButtonWhite>
              </div>
            </div>
          </div>
        </transition>
      </div>  
    </div> 
  </transition>
</template>

<script setup>
import { usePopUpTransition } from '../composables/useTransition';
import ButtonRed from './buttons/ButtonRed.vue';
import ButtonWhite from './buttons/ButtonWhite.vue';

const popUpTransition = usePopUpTransition()

defineProps({
  inUse: {type: Boolean, required: true}
})

</script>

<style scoped>

</style>