<template>

        <div class="pt-2 h-fit lg:h-full mx-auto max-w-[1300px] min-w-[200px] flex flex-col overflow-auto lg:max-h-[700px] lg:min-h-[500px] lg:overflow-hidden w-full">
            <div class="flex flex-col items-start justify-center px-2 mt-2 mb-4">
                <h3 class="font-bold tracking-widest">Attributes Management</h3>
                <p>Manage Categories and Unit Measurement for Items</p>
            </div>

            <div class="flex flex-col p-2 lg:flex-row flex-1 gap-6 overflow-auto min-w-0 min-h-fit lg:min-h-[auto] lg:overflow-hidden">
                <!-- CATEGORIES LIST -->
                <Categories/> 
                
                <!-- UNIT MEASUREMENTS LIST -->
                <UnitMeasurement/>
                
            </div>
            
        </div>
        
</template>

<script setup>
import Categories from './Categories.vue';
import UnitMeasurement from './UnitMeasurement.vue';

</script>

<style scoped>

</style>