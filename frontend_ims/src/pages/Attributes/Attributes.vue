<template>
    <transition
        :enter-from-class="mainContentTransition.enterFrom"
        :enter-to-class="mainContentTransition.enterTo"
        :enter-active-class="mainContentTransition.enterActive"
        :leave-from-class="mainContentTransition.leaveFrom"
        :leave-to-class="mainContentTransition.leaveTo"
        :leave-active-class="mainContentTransition.leaveActive"
    >
        <section v-if="isMounted" class="pt-2 h-fit lg:h-full mx-auto max-w-[1300px] min-w-[200px] flex flex-col overflow-auto lg:max-h-[700px] lg:min-h-[500px] lg:overflow-hidden w-full">
            <div class="flex flex-col items-start justify-center px-2 mt-2 mb-4">
                <h3 class="font-bold tracking-widest">Attributes Management</h3>
                <p>Manage Categories and Unit Measurements for Items</p>
            </div>
            
            <div class="flex flex-col p-2 lg:flex-row flex-1 gap-6 overflow-auto min-w-0 min-h-fit lg:min-h-[auto] lg:overflow-hidden">
                <!-- Categories management panel -->
                <Categories/> 
                
                <!-- Unit measurements management panel -->
                <UnitMeasurement/>
                
            </div>
        </section>
    </transition>
        
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useMainContentTransition, usePopUpTransition, useAlertLoadingTransition } from '../../composables/useTransition';
import Categories from './Categories.vue';
import UnitMeasurement from './UnitMeasurement.vue';

const mainContentTransition = useMainContentTransition()
const isMounted = ref(false)

onMounted(() => {
    isMounted.value = true
})
</script>

<style scoped>

</style>