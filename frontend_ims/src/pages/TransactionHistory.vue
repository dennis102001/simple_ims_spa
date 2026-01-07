<template>

  <div class="relative h-full mx-auto w-full max-w-[900px] min-h-[500px] min-w-[200px]">

    <!-- LIST -->
    <transition
      :enter-from-class="itemsListTransition.enterFrom "
      :enter-to-class="itemsListTransition.enterTo"
      :enter-active-class="itemsListTransition.enterActive"
      :leave-from-class="itemsListTransition.leaveFrom"
      :leave-to-class="itemsListTransition.leaveTo"
      :leave-active-class="itemsListTransition.leaveActive"
    >
      <div v-show="viewingTransactionsList" class="px-2 pt-2 pb-4 absolute h-full flex flex-col max-h-[800px] bg-white rounded shadow-[0_0_10px_rgba(0,0,0,0.1)] w-full">
        
        <div class="flex flex-col items-center mt-4 mb-8">
          <h1 class="px-2 text-3xl font-semibold tracking-wide">Stock Transactions</h1>
        </div>
        
        <div class="flex flex-col flex-1 overflow-hidden">

          <div class="flex flex-row p-2 space-y-4 sm:space-y-0">
            <!-- date range for big screen -->
            <div class="hidden flex-row flex-wrap items-center max-w-[630px] flex-grow gap-2 md:flex">
              <ButtonDark @click="search">
                Search
              </ButtonDark>
              <div class="flex flex-wrap flex-grow h-full gap-2">
                <div class="flex items-center h-full px-2 text-sm bg-white border-2 border-gray-500 rounded">
                  <label for="dateFrom" class="text-sm font-semibold text-darkgray-pri">From:</label>
                  <input v-model="dateRange.from" id="dateFrom" type="date" class="ml-1 text-sm cursor-pointer">
                </div>
                <div class="flex items-center h-full px-2 text-sm bg-white border-2 border-gray-500 rounded ">
                  <label for="dateTo" class="text-sm font-semibold text-darkgray-pri">To:</label>
                  <input v-model="dateRange.to" id="dateTo" type="date" class="ml-1 text-sm cursor-pointer">
                </div>           
              </div>
            </div>
            <!-- date range for small screen -->
            <div class="justify-end md:hidden date-range">
              <ButtonWhite @click="viewingDateRange.value = !viewingDateRange.value">
                Select date range
              </ButtonWhite>
              <!-- profile dropdown --> 
              <Transition
                  :enter-from-class="popUpTransition.enterFrom"
                  :enter-to-class="popUpTransition.enterTo"
                  :enter-active-class="popUpTransition.enterActive"
                  :leave-from-class="popUpTransition.leaveFrom"
                  :leave-to-class="popUpTransition.leaveTo"
                  :leave-active-class="popUpTransition.leaveActive"
              >         
                <div v-show="viewingDateRange" class="h-fit flex flex-col gap-3 z-40 max-h-[200px] overflow-auto absolute p-3 mt-2 bg-white rounded-md shadow-[0_0_10px_rgba(0,0,0,0.3)] ">
                  <div class="flex items-center justify-between p-2 text-sm bg-white border-2 border-gray-500 rounded">
                    <label for="dateFrom" class="text-sm font-semibold text-darkgray-pri">From:</label>
                    <input v-model="dateRange.from" id="dateFrom" type="date" class="ml-1 text-sm cursor-pointer">
                  </div>
                  <div class="flex items-center justify-between p-2 text-sm bg-white border-2 border-gray-500 rounded ">
                    <label for="dateTo" class="text-sm font-semibold text-darkgray-pri">To:</label>
                    <input v-model="dateRange.to" id="dateTo" type="date" class="ml-1 text-sm cursor-pointer">
                  </div>  

                  <ButtonDark @click="search" width="full">
                    Search
                  </ButtonDark>
                </div>
              </Transition>
            </div>
            <div class="flex items-end justify-end flex-1">
              <ButtonYellow @click="getTransactionHistory()">
                See all
              </ButtonYellow>
            </div>
          </div>
          
          <!-- table -->
          <div class="flex-1 px-2 overflow-auto">
            <!-- head -->
            <div class="grid grid-cols-[20%_20%_20%_10%_20%_10%] head-row">
              <div class="head-data">Date</div>
              <div class="head-data">Prepared by</div>
              <div class="head-data">Remarks</div>
              <div class="head-data">Type</div>
              <div class="head-data">Items</div>
              <div class="justify-center head-data">Action</div>
            </div>
            <!-- body -->
            <div v-if="dataStatus" class="flex items-center justify-center w-full py-3">
                <p>{{ dataStatus }}</p>
            </div>
            <div v-if="!dataStatus" v-for="item in resultData" class="grid grid-cols-[20%_20%_20%_10%_20%_10%] body-row">
              <div class="justify-start body-data">{{ formatDate(item.date) }}</div>
              <div class="justify-start body-data">{{ item.user_name }}</div>
              <div class="justify-start body-data">{{ item.remarks }}</div>
              <div class="justify-start body-data">{{ item.type }}</div>
              <div class="justify-start body-data">{{ item.items }}</div>
              <div class="justify-center body-data">
                <button @click="toggleMoreActions(item.id)" class="px-4 py-2 rounded-md btnActions hover:bg-gray-300">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                  </button>
                  <Transition
                    :enter-from-class="popUpTransition.enterFrom"
                    :enter-to-class="popUpTransition.enterTo"
                    :enter-active-class="popUpTransition.enterActive"
                    :leave-from-class="popUpTransition.leaveFrom"
                    :leave-to-class="popUpTransition.leaveTo"
                    :leave-active-class="popUpTransition.leaveActive"
                  >                
                    <div v-if="currentTransactionId == item.id" class="absolute z-20 btnActions -translate-x-2/3 w-fit">
                      <button 
                        @click="showUpdateForm(item.id, item.remarks)"
                        type="button"
                        class="px-4 py-1 text-black rounded-l-full bg-warmyellow-pri">
                        Edit
                      </button>
                      <button 
                        @click="deleteTransaction(item.id)"
                        type="button" 
                        class="px-4 py-1 text-black rounded-r-full bg-warmyellow-pri"
                      >
                        Delete
                      </button>
                    </div>
                  </Transition>
              </div>
            </div>
          </div>
        </div>
      </div>

    </transition>

    <!-- UPDATE FORM -->
    <transition
      :enter-from-class="formTransition.enterFrom"
      :enter-to-class="formTransition.enterTo"
      :enter-active-class="formTransition.enterActive"
      :leave-from-class="formTransition.leaveFrom"
      :leave-to-class="formTransition.leaveTo"
      :leave-active-class="formTransition.leaveActive"
    >
      <div v-show="viewingUpdateForm" class="absolute w-full h-full">
          
          <form @submit.prevent="submitUpdates" class="mx-auto flex flex-col px-2 sm:px-4 pt-2 pb-4 bg-white rounded shadow-[0_0_10px_rgba(0,0,0,0.1)] max-h-[800px] h-full w-full max-w-2xl">
              <div class="flex flex-col items-center mt-4 mb-10">
                <h1 class="text-3xl font-semibold tracking-wide">Update Transaction</h1>
              </div>

              <div class="flex flex-col flex-1 overflow-hidden">

                  <!-- table -->
                  <div class="flex-1 px-2 overflow-auto border-b-2">
                    <!-- head -->
                    <div class="grid grid-cols-[45%_20%_20%_15%] head-row">
                      <div class="head-data">Item</div>
                      <div class="head-data">Cost</div>
                      <div class="head-data">Quantity</div>
                      <div class="justify-center head-data">Action</div>
                    </div>
                    <!-- body -->
                    <div v-for="item in formData.selectedItemsList" class="grid grid-cols-[45%_20%_20%_15%] body-row">
                      <div class="justify-start body-data">{{ item.item_name }}</div>
                      <div class="justify-start body-data">{{ item.cost }}</div>
                      <div class="justify-start body-data">
                          <input v-model="item.quantity" type="text" class="w-full px-2 text-right rounded outline outline-1">
                      </div>
                      <div class="justify-center body-data">
                        <button @click="addToDeletedItems(item.id)" 
                          type="button" class="px-4 py-2 text-white bg-red-500 rounded">
                          <i class="rotate-45 fa-solid fa-plus"></i>
                        </button>
                      </div>
                    </div>
                  </div>
              </div>

              <div class="flex flex-col w-full px-2 my-4 sm:space-x-8 sm:flex-row">
                <div class="flex flex-col flex-1 py-2">
                    <label for="remarks" class="mb-1 ">Remarks:</label>
                    <textarea v-model="formData.remarks" id="remarks" class="h-12 p-1 bg-white border border-gray-400 rounded border-1"></textarea>
                </div>
                <div class="flex flex-row flex-wrap flex-1 py-2">
                  <label class="mb-1" >Prepared by:</label>
                  <label class="font-semibold ml-2 underline underline-offset-[6px]" >{{ formData.userName }}</label>
                </div>
              </div>

              <div class="flex flex-row justify-between w-full px-2 space-x-4 sm:space-x-6 md:space-x-8">
                <ButtonDark width="full" type="submit">
                    SAVE
                </ButtonDark>
                <ButtonRed width="full" @click="showTransactionsList" type="button" >
                    CANCEL
                </ButtonRed>  
              </div>
          </form>
          
      </div>
    </transition>
  </div>

  <!-- LOADING ERROR SUCCESS -->
    <teleport to="body">
      <Transition
        :enter-from-class="alertLoadingTransition.enterFrom"
        :enter-to-class="alertLoadingTransition.enterTo"
        :enter-active-class="alertLoadingTransition.enterActive"
        :leave-from-class="alertLoadingTransition.leaveFrom"
        :leave-to-class="alertLoadingTransition.leaveTo"
        :leave-active-class="alertLoadingTransition.leaveActive"
      >
        <Loading v-if="loading"></Loading>
      </Transition>

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

      <Transition
        :enter-from-class="alertLoadingTransition.enterFrom"
        :enter-to-class="alertLoadingTransition.enterTo"
        :enter-active-class="alertLoadingTransition.enterActive"
        :leave-from-class="alertLoadingTransition.leaveFrom"
        :leave-to-class="alertLoadingTransition.leaveTo"
        :leave-active-class="alertLoadingTransition.leaveActive"
      >
        <SuccessBanner @closeSuccess="closeSuccessMsg" v-if="hasSuccessMsg">
          <template v-slot:title>
              Success 
          </template>
          <template v-slot:body>
              {{ successMsgBody }}
          </template>
        </SuccessBanner>
      </Transition>
    </teleport>
</template>

<script setup>
import { useAlertLoadingTransition, useItemsListTransition, usePopUpTransition, useFormTransition } from '../composables/useTransition';
import { computed, onMounted, onBeforeUnmount, ref } from 'vue';
import { useAlertMessages } from '../composables/useAlertMessages';
import { useFormatDate } from '../composables/useFormatDate';
import axiosClient from '../axios';
import useUserStore from '../store/user';

import SuccessBanner from '../components/alerts/SuccessBanner.vue';
import ErrorBanner from '../components/alerts/ErrorBanner.vue';
import Loading from '../components/alerts/Loading.vue';
import ButtonYellow from '../components/buttons/ButtonYellow.vue';
import ButtonWhite from '../components/buttons/ButtonWhite.vue';
import ButtonDark from '../components/buttons/ButtonDark.vue';
import ButtonRed from '../components/buttons/ButtonRed.vue';

const { showSuccessMsg, closeSuccessMsg, showErrorMsg, closeErrorMsg, hasSuccessMsg, hasErrorMsg, successMsgBody, errorMsgBody } = useAlertMessages()
const { formatDate } = useFormatDate()
const userStore = useUserStore()
const user = computed(() => userStore.user)

const alertLoadingTransition = useAlertLoadingTransition()
const itemsListTransition = useItemsListTransition()
const popUpTransition = usePopUpTransition()
const formTransition = useFormTransition()

const viewingTransactionsList = ref(null)
const viewingUpdateForm = ref(null)
const viewingDateRange = ref(null)

const currentTransactionId = ref(null)
const loading = ref(false)
const dataStatus = ref(null)

const resultData = ref([])

const formData = ref({
  userId: null,
  userName: null,
  remarks: null,
  hdrId: null,
  selectedItemsList: ref([]),
  deletedItemsList: ref([])
})

const dateRange = ref({
  from: null,
  to: null
})

// FUNCTIONS API
function getTransactionHistory() {
  dataStatus.value = "Loading..."

  axiosClient.get('/api/stock-report')
    .then(response => {
      resultData.value = response.data
      console.log(response)

      if(resultData.value.length < 1){
        dataStatus.value = "No data"
      }else{
        dataStatus.value = null
      }
    })
    .catch(error => {
      console.log(error.response);
      
      if(resultData.value.length < 1){
        dataStatus.value = "No data"
      }else{
        dataStatus.value = null
      }
    })
}

function submitUpdates(){
  loading.value = true
  const id = formData.value.hdrId

  axiosClient.put(`/api/update-transaction/${id}`, formData.value)
    .then(response => {
      loading.value = false
       
      showSuccessMsg("Transaction successfully updated!")
      getTransactionHistory()
      showTransactionsList()
    })
    .catch(error => {
      loading.value = false
      console.log(error);
      
      showErrorMsg(error.response.data.message)
    })
}

function deleteTransaction(hdrId){
  if(confirm('Are you sure you want to delete this?')){
    loading.value = true

    axiosClient.delete(`/api/delete-transaction/${hdrId}`)
      .then(response => {
        loading.value = false
      
        showSuccessMsg("Transaction successfully deleted!")
        getTransactionHistory()
      })
      .catch(error => {
        loading.value = false
        showErrorMsg(error.response.data.error)
      })
  }
}

function search() {
  axiosClient.get('/api/stock-report-specified', {params: dateRange.value})
    .then(response => {
      resultData.value = response.data
    })
    .catch(error => {
      console.log(error);
      
    })
}

function addToDeletedItems(deletedDtlId){
  formData.value.deletedItemsList.push(deletedDtlId)

  formData.value.selectedItemsList = formData.value.selectedItemsList.filter(item => {
    return item.id != deletedDtlId
  })
}

function toggleMoreActions(id){
  if (currentTransactionId.value == id){
    currentTransactionId.value = null
  }else{
    currentTransactionId.value = id
  }  
}

function showTransactionsList(){
  viewingUpdateForm.value = false
  viewingTransactionsList.value = true

  formData.value.deletedItemsList = []
}

function showUpdateForm(hdrId, remarks){
  viewingTransactionsList.value = false
  viewingUpdateForm.value = true

  formData.value.hdrId = hdrId
  formData.value.remarks = remarks

  axiosClient.get('/api/stock-transaction-details', {params: {id: hdrId}})
    .then(response => {
      formData.value.selectedItemsList = response.data
    })
    .catch(error => {
      console.log(error);
    })
  
  // change enter transition
    itemsListTransition.value.enterFrom = "opacity-0 -translate-x-full"
    itemsListTransition.value.enterTo = "translate-x-0 opacity-100"
    itemsListTransition.value.enterActive = "transition duration-[1000ms] ease"
}

function handleClickOutside(e) {
  // close if clicking outside the dropdown
  if (!e.target.closest(".btnActions") && !e.target.closest(".date-range")) {
    currentTransactionId.value = null
   viewingDateRange.value = false
  }
  else if(e.target.closest(".btnActions")){
    viewingDateRange.value = false
  }
  else if(e.target.closest(".date-range")){
    currentTransactionId.value = null
  }
}


onMounted(() => {
  viewingTransactionsList.value = true

  getTransactionHistory()
  formData.value.userName = user.value.name
  formData.value.userId = user.value.id

  document.addEventListener("click", handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside)
})

</script>

<style>

</style>
