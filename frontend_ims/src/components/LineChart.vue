<template>
    <div class="relative p-2 w-full h-[300px]">
        <Line 
            class="relative w-[100%] h-[100%]" 
            id="my-chart-id"
            :options="chartOptions"
            :data="toRaw(chartData)"
        />
    </div>
</template>

<script setup>
import { ref, toRaw } from 'vue'
import { Line } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale)

const props = defineProps({
    chartData: { type: Object, required: true }
})

const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false
})

</script>

<style scoped>

</style>