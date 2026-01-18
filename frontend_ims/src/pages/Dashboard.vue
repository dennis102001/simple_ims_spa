<template>
  <transition
    :enter-from-class="mainContentTransition.enterFrom "
    :enter-to-class="mainContentTransition.enterTo"
    :enter-active-class="mainContentTransition.enterActive"
    :leave-from-class="mainContentTransition.leaveFrom"
    :leave-to-class="mainContentTransition.leaveTo"
    :leave-active-class="mainContentTransition.leaveActive"
  >
      
    <section v-if="isMounted" class="mx-auto max-w-[1400px] h-full min-h-fit overflow-hidden flex flex-col gap-2">
      <h4 class="pt-2 px-2 ml-1 font-semibold">Dashboard Overview</h4>

      <div class="p-2 min-h-fit w-full">
        <div class="grid grid-cols-[repeat(auto-fit,minmax(280px,1fr))] gap-6 ">
          
          <section class="flex flex-col flex-1 p-6 lg:p-8 bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl hover-up-effect">
            <div class="flex flex-row items-center gap-1 mb-6 ">
              <div class="size-12 text-lg flex items-center justify-center text-warmyellow-pri bg-darkgray-pri rounded-xl">
                <i class="fa-solid fa-chart-line"></i>
              </div>
              <div class="ml-2">
                <p class="font-bold">Monthly Revenue</p>
                <p class="text-xs text-gray-400">Current month performance</p>
              </div>
            </div>

            <div class="flex flex-col">
              <div class="font-bold text-3xl border-b pb-4">
                <span v-if="isLoading">
                  Loading...
                </span>
                <span v-else-if="data.revenue.totalRevenue !== null && data.revenue.totalRevenue !== undefined">
                  {{ '₱' + data.revenue.totalRevenue }}
                </span>
                <span v-else>
                  No data
                </span>
              </div>
              
              <div class="text-xs text-gray-400 pt-4">
                <span v-if="isLoading">
                  Loading...
                </span>
                <span v-else-if="data.revenue.start && data.revenue.end">
                  {{ formatDate(data.revenue.start) }} - {{ formatDate(data.revenue.end) }} • Updated today
                </span>
                <span v-else>
                  No data
                </span>
              </div>
            </div>
          </section> 

          <section class="flex flex-col flex-1 p-6 lg:p-8 bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl hover-up-effect">
            <div class="flex flex-row items-center gap-1 mb-6 ">
              <div class="size-12 text-lg flex items-center justify-center text-warmyellow-pri bg-darkgray-pri rounded-xl">
                <i class="fa-solid fa-boxes-stacked"></i>
              </div>
              <div class="ml-2">
                <p class="font-bold">Inventory Status</p>
                <p class="text-xs text-gray-400">Items count and stock alerts</p>
              </div>
            </div>
            
            <div class="flex flex-row flex-wrap text-center justify-between ">
              <div class="flex-1">
                <p class="text-xs font-semibold text-gray-500 mb-2">Total items</p>
                <p class="font-bold text-3xl">
                  <span v-if="isLoading">
                    -
                  </span>
                  <span v-else-if="data.totalItems !== null && data.totalItems !== undefined">
                    {{ data.totalItems }}
                  </span>
                  <span v-else class="text-sm">
                    No Data
                  </span>
                </p> 
              </div>

              <div class=" flex-1">
                <p class="text-xs font-semibold text-gray-500 mb-2">Low Stock</p>
                <p class="font-bold text-3xl ">
                  <span v-if="isLoading">
                    -
                  </span>
                  <span v-else-if="data.lowStock !== null && data.lowStock !== undefined">
                    {{ data.lowStock }}
                  </span>
                  <span v-else class="text-sm">
                    No Data
                  </span>
                </p> 
              </div>

              <div class=" flex-1">
                <p class="text-xs font-semibold text-gray-500 mb-2">Zero Stock</p>
                <p class="font-bold text-3xl ">
                  <span v-if="isLoading">
                    -
                  </span>
                  <span v-else-if="data.zeroStock !== null && data.zeroStock !== undefined">
                    {{ data.zeroStock }}
                  </span>
                  <span v-else class="text-sm">
                    No Data
                  </span>
                </p> 
              </div>
            </div>
          </section> 
          
          <section class="flex flex-col justify-between flex-1 p-6 lg:p-8 bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl hover-up-effect">
            <p class="mb-2 font-bold">Quick Actions</p>

            <div class="flex flex-col flex-1 gap-3">
              <router-link :to="{ name: 'CreatePO' }" class="relative p-1 w-full overflow-hidden  transition-all transform duration-200 hover:bg-warmyellow-sec border border-gray-100 bg-warmyellow-pri rounded-lg group">
                <div class="relative z-10 flex flex-row items-center justify-start p-1 text-darkgray-sec">
                  <div class="text-lg py-1 px-3 rounded-lg ">
                    <i class=" fa-solid fa-file-arrow-down"></i>
                  </div>
                  <span class="font-semibold text-sm">Create Purchase Order</span>
                  <div class="ml-auto text-sm px-2">
                    <i class="fas fa-chevron-right"></i>
                  </div>
                </div>
              </router-link>

              <router-link :to="{ name: 'CreateSO' }" class="relative p-1 w-full overflow-hidden  transition-all transform duration-200 hover:bg-warmyellow-sec border border-gray-100 bg-warmyellow-pri rounded-lg group">
                <div class="relative z-10 flex flex-row items-center justify-start p-1 text-darkgray-sec">
                  <div class="text-lg py-1 px-3 rounded-lg">
                    <i class=" fa-solid fa-file-arrow-up"></i>
                  </div>
                  <span class="font-semibold text-sm">Create Sale Order</span>
                  <div class="ml-auto text-sm px-2">
                    <i class="fas fa-chevron-right"></i>
                  </div>
                </div>
              </router-link>
            </div>
          </section> 
        </div>
      </div>

      <div class="p-2">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <section class="w-full p-6 lg:p-8 bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl hover-up-effect">
            <h4>Top Selling Items</h4>
            <BarChart :chartData="topItemsData" />
          </section>
            
          <section class="w-full p-6 lg:p-8 bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl hover-up-effect">
            <h4>Stock Movement</h4>
            <LineChart :chartData="stockMovement" />
          </section>
        </div>
      </div>

      <div class=" p-2 overflow-hidden grid grid-cols-1 lg:grid-cols-2 gap-6">
        <section class="flex flex-col flex-1 bg-white p-6 lg:p-8 overflow-hidden shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl transition-all transform hover-up-effect">
          <h4 class="mb-4 font-semibold">Recent Purchase Orders</h4>

          <div class="flex flex-col flex-1 overflow-hidden ">

            <div v-if="isLoading" class="w-full text-center py-4">
              Loading...
            </div>

            <div v-else-if="data.recentOrders && data.recentOrders.length > 0" class="overflow-auto" >
              <div v-for="po in data.recentOrders" class="flex flex-row flex-wrap w-full overflow-hidden border-b last:border-b-0 py-4">
                <div>
                  <h6 class="font-semibold">
                    {{ po.poNumber }}
                  </h6>
                  <div class="text-xs font-medium text-gray-600 ">
                    {{ formatDate(po.orderDate) }}
                  </div>
                </div>

                <div 
                  :class="['px-3 py-1 w-fit text-xs font-medium rounded text-nowrap ml-auto h-fit uppercase', 
                    po.status == 'Pending' ? 'bg-yellow-100 text-yellow-600' : '', 
                    po.status == 'Received' ? 'bg-blue-100 text-blue-600' : '',
                    po.status == 'Cancelled' ? 'bg-red-100 text-red-600' : '', 
                    po.status == 'Returned' ? 'bg-orange-100 text-orange-600' : ''
                  ]"
                >
                  {{ po.status }}
                </div>
              </div>
            </div>

            <div v-else class="w-full text-center py-4">
              No Data
            </div>
          </div>
        </section>

        <section class="flex flex-col flex-1 bg-white p-6 lg:p-8 overflow-hidden shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl transition-all transform hover-up-effect">
          <h4 class="mb-4 font-semibold">Recent Sales</h4>

          <div class="flex flex-col flex-1 overflow-hidden">

            <div v-if="isLoading" class="w-full text-center py-4">
              Loading...
            </div>

            <div v-else-if="data.recentSales && data.recentSales.length > 0" class="overflow-auto">
              <div v-for="sale in data.recentSales" class="flex flex-row flex-wrap w-full overflow-hidden border-b last:border-b-0 py-4">
                <div>
                  <h6 class="font-semibold">
                    {{ sale.soNumber }}
                  </h6>
                  <div class="text-xs font-medium text-gray-500 ">
                    {{ formatDate(sale.createdDate) }}
                  </div>
                </div>
                <div 
                  :class="['px-3 py-1 w-fit text-xs font-medium rounded text-nowrap ml-auto h-fit uppercase', 
                    sale.status == 'Pending' ? 'bg-yellow-100 text-yellow-600' : '', 
                    sale.status == 'Paid' ? 'bg-blue-100 text-blue-600' : '',
                    sale.status == 'Cancelled' ? 'bg-red-100 text-red-600' : '', 
                    sale.status == 'Returned' ? 'bg-orange-100 text-orange-600' : ''
                  ]"
                >
                  {{ sale.status }}
                </div>
              </div>
            </div>

            <div v-else class="w-full text-center py-4">
              No Data
            </div>
          </div>
        </section>
      </div>
    </section>
  </transition>

  <teleport to="body">
    <Transition
      :enter-from-class="alertLoadingTransition.enterFrom"
      :enter-to-class="alertLoadingTransition.enterTo"
      :enter-active-class="alertLoadingTransition.enterActive"
      :leave-from-class="alertLoadingTransition.leaveFrom"
      :leave-to-class="alertLoadingTransition.leaveTo"
      :leave-active-class="alertLoadingTransition.leaveActive"
    >
      <ErrorBanner @closeError="closeErrorMsg" v-if="hasErrorMsg">
        <template v-slot:title>
          Error 
        </template>

        <template v-slot:body>
          {{ errorMsgBody }}
        </template>
      </ErrorBanner>
    </Transition>
  </teleport>

</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import { useAlertLoadingTransition, useMainContentTransition } from '../composables/useTransition';
import { useFormatDate } from '../composables/useFormatDate';
import { useAlertMessages } from '../composables/useAlertMessages';
import axiosClient from '../axios';
import ErrorBanner from '../components/alerts/ErrorBanner.vue';
import BarChart from '../components/BarChart.vue';
import LineChart from '../components/LineChart.vue';

const { 
    showSuccessMsg, 
    closeSuccessMsg, 
    showErrorMsg, 
    closeErrorMsg, 
    hasSuccessMsg, 
    hasErrorMsg, 
    successMsgTitle, 
    successMsgBody, 
    errorMsgBody 
} = useAlertMessages()

const mainContentTransition = useMainContentTransition()
const alertLoadingTransition = useAlertLoadingTransition()

const { formatDate } = useFormatDate()
const isLoading = ref(false)
const isMounted = ref(false)

const topItemsData = computed(() => JSON.parse(JSON.stringify(topSellingData.value)))
const topSellingData = ref({
  labels: [],
  datasets: [
    {
      label: 'Top Selling Items',
      data: [],
      backgroundColor: [
        'rgba(255, 99, 132, 0.2)',
        'rgba(255, 159, 64, 0.2)',
        'rgba(255, 205, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(54, 162, 235, 0.2)',
      ],
      borderColor: [
        'rgb(255, 99, 132)',
        'rgb(255, 159, 64)',
        'rgb(255, 205, 86)',
        'rgb(75, 192, 192)',
        'rgb(54, 162, 235)',
      ],
      borderWidth: 1
    }
  ]
})

const stockMovement = computed(() => JSON.parse(JSON.stringify(stocksMovementData.value)))
const stocksMovementData = ref({
  labels: [],
  datasets: [
    {
      label: 'Stock In',
      data: [],
      backgroundColor: '#10b981',
      borderColor: '#10b981'
    },
    {
      label: 'Stock Out',
      data: [],
      backgroundColor: '#ef4444',
      borderColor: '#ef4444'
    }
  ]
})

const data = ref({
  totalItems: null,
  totalCategories: null,
  zeroStock: null,
  lowStock: null,
  recentOrders: [],
  recentSales: [],
  revenue: {}
})

async function getHomeData(){
  isLoading.value = true

  const now = new Date();
  const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1, 0, 0, 0);
  const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0, 23, 59, 59);
  const startISO = startOfMonth.toISOString();
  const endISO   = endOfMonth.toISOString();  
  
  try {
    const response = await axiosClient.get('/api/home-info', {params: {start: startISO, end: endISO}})
    data.value = response.data

    topSellingData.value.labels = []
    topSellingData.value.datasets[0].data = []

    stocksMovementData.value.labels = []
    stocksMovementData.value.datasets[0].data = []
    stocksMovementData.value.datasets[1].data = []

    response.data.topSellingData.forEach(item => {
      topSellingData.value.labels.push(truncateText(item.item_name, 15))
      topSellingData.value.datasets[0].data.push(item.total_sold)
    });

    response.data.stocksMovementData.forEach(item => {
      const date = new Date(item.date);
      stocksMovementData.value.labels.push(`${date.getMonth()+1}/${date.getDate()}`)
      stocksMovementData.value.datasets[0].data.push(item.total_in)
      stocksMovementData.value.datasets[1].data.push(item.total_out)
    });
  } 
  catch (error) {
    showErrorMsg(error.response?.data?.message || 'Failed to load data')
  }
  finally{
    isLoading.value = false
  }
}

function truncateText(text, limit) {
  return text.length > limit ? text.slice(0, limit) + "..." : text
}

onMounted(() => { 
  isMounted.value = true
  getHomeData()
})

</script>

<style scoped>

</style>
