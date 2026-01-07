<template>

    <div class="relative h-full mx-auto w-full max-w-[1000px] min-h-[500px] min-w-[200px]">

        <transition
            :enter-from-class="itemsListTransition.enterFrom "
            :enter-to-class="itemsListTransition.enterTo"
            :enter-active-class="itemsListTransition.enterActive"
            :leave-from-class="itemsListTransition.leaveFrom"
            :leave-to-class="itemsListTransition.leaveTo"
            :leave-active-class="itemsListTransition.leaveActive"
        >
            <div v-show="viewingItemsList" class="px-2 pt-2 pb-4 absolute h-full flex flex-col max-h-[800px] bg-white rounded shadow-[0_0_10px_rgba(0,0,0,0.1)] w-full">

                <div class="flex flex-col items-center mt-4 mb-10">
                    <h1 class="text-3xl font-semibold tracking-wide">Stock Out</h1>
                </div>

                <div class="flex flex-col flex-1 overflow-hidden">
                    <!-- search and proceed -->
                    <div class="flex flex-col items-end justify-between px-2 mb-2 space-y-2 sm:space-y-0 sm:flex-row">
                        <ButtonYellow type="button" @click="showStockOutForm" class="sm:order-2">
                            <p v-show="selectedIds.length" class="absolute p-1 text-xs text-center text-white -translate-y-5 rounded-full bg-darkgray-sec -translate-x-7 min-h-6 min-w-6">{{ selectedIds.length }}</p>
                            Proceed
                        </ButtonYellow>
                        <input v-model="searchValue" type="text" placeholder="Search here" class="sm:order-1 placeholder:font-semibold sm:max-w-[400px] w-full min-w-[100px] outline-none px-2 py-2 text-sm bg-white border-2 border-gray-500 rounded placeholder:text-gray-400 placeholder:tracking-widest ">
                    </div>
                    
                    <!-- div-based table -->
                    <div class="flex-1 px-2 overflow-auto">
                        <!-- head -->
                        <div class="grid grid-cols-[30%_20%_10%_10%_10%_10%_10%] head-row">
                            <div class="justify-start head-data">Item</div>
                            <div class="justify-start head-data">Category</div>
                            <div class="justify-start head-data">Unit</div>
                            <div class="justify-end head-data">Cost</div>
                            <div class="justify-end head-data">Price</div>
                            <div class="justify-end head-data">Quantity</div>
                            <div class="justify-center head-data">Action</div>
                        </div>
                        <!-- body -->
                        <div v-if="dataStatus" class="flex items-center justify-center w-full py-3">
                            <p>{{ dataStatus }}</p>
                        </div>
                        <div v-if="!dataStatus" v-for="item in filteredItemsListData" class="grid grid-cols-[30%_20%_10%_10%_10%_10%_10%] body-row">
                            <div class="justify-start body-data">{{ item.item_name }}</div>
                            <div class="justify-start body-data">{{ item.category }}</div>
                            <div class="justify-start body-data">{{ item.unit }}</div>
                            <div class="justify-end body-data">{{ item.cost }}</div>
                            <div class="justify-end body-data">{{ item.price }}</div>
                            <div class="justify-end body-data">{{ item.quantity }}</div>
                            <div class="justify-center body-data">
                                <button 
                                @click="toggleSelectItem(item.id, item.item_name, item.cost, item.price, item.quantity, item.unit)" 
                                :class=" selectedIds.includes(item.id) ? 'bg-red-500' : 'bg-gray-400' " class="text-white rounded size-10">
                                    <i :class="{'rotate-45': selectedIds.includes(item.id)}" class="transition duration-500 transform fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </transition>

        <!-- STOCK OUT FORM -->
        <transition
            :enter-from-class="formTransition.enterFrom"
            :enter-to-class="formTransition.enterTo"
            :enter-active-class="formTransition.enterActive"
            :leave-from-class="formTransition.leaveFrom"
            :leave-to-class="formTransition.leaveTo"
            :leave-active-class="formTransition.leaveActive"
        >
            <div v-show="viewingStockOutForm" class="absolute w-full h-full">
                <form @submit.prevent="submitStockOut" class="mx-auto flex flex-col px-2 sm:px-4 pt-2 pb-4 bg-white rounded shadow-[0_0_10px_rgba(0,0,0,0.1)] max-h-[800px] h-full w-full max-w-2xl">
                    <div class="flex flex-col items-center mt-4 mb-10">
                        <h1 class="text-3xl font-semibold tracking-wide">Stock Out Items</h1>
                    </div>

                    <div class="flex flex-col flex-1 overflow-hidden">
                        <!-- search and back -->
                        <div class="flex justify-end px-2 mb-2">
                            <ButtonYellow type="button" @click="showItemsList('keep')">
                                Back
                            </ButtonYellow>
                        </div>
                    
                        <!-- table -->
                        <div class="flex-1 px-2 overflow-auto border-b-2">
                            <!-- head -->
                            <div class="grid grid-cols-[45%_15%_15%_15%_10%] head-row">
                                <div class="justify-start head-data">Item</div>
                                <div class="justify-start head-data">Unit</div>
                                <div class="justify-end head-data">Cost</div>
                                <div class="justify-end head-data">Quantity</div>
                                <div class="justify-center head-data">Action</div>
                            </div>
                            <!-- body -->
                            <div v-for="item in formData.selectedItemsList" class="grid grid-cols-[45%_15%_15%_15%_10%] body-row">
                                <div class="justify-start body-data">{{ item.itemName }}</div>
                                <div class="justify-start body-data">{{ item.unitName }}</div>
                                <div class="justify-end body-data">{{ item.itemCost }}</div>
                                <div class="justify-end body-data">
                                    <input v-model="item.quantityToOut" type="text" class="w-full px-2 text-right rounded outline outline-1">
                                </div>
                                <div class="justify-center body-data">
                                    <button @click="toggleSelectItem(item.itemId, item.itemName, item.itemCost, item.itemPrice)" 
                                        type="button" class="text-white bg-red-500 rounded size-10">
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
                        <ButtonRed width="full" @click="showItemsList('clear')" type="button" >
                            CANCEL
                        </ButtonRed>
                    </div>
                </form>
                
            </div>
        </transition>
    </div>

    <!-- LOADING SUCCESS ERROR -->
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
            <SuccessBanner @closeSuccess="closeSuccessMsg" v-if="hasSuccessMsg">
                <template v-slot:title>
                    Success 
                </template>
                <template v-slot:body>
                    {{ successMsgBody }}
                </template>
            </SuccessBanner>
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
    </teleport>

</template>

<script setup>
import { useItemsListTransition, useAlertLoadingTransition, useFormTransition } from '../composables/useTransition';
import { onMounted, ref, computed } from 'vue';
import useUserStore from '../store/user';
import axiosClient from '../axios';

import SuccessBanner from '../components/alerts/SuccessBanner.vue';
import ErrorBanner from '../components/alerts/ErrorBanner.vue';
import Loading from '../components/alerts/Loading.vue';
import ButtonYellow from '../components/buttons/ButtonYellow.vue';
import ButtonDark from '../components/buttons/ButtonDark.vue';
import ButtonRed from '../components/buttons/ButtonRed.vue';

const viewingItemsList = ref(null)
const viewingStockOutForm = ref(null)

const userStore = useUserStore()
const user = computed(() => userStore.user);

const alertLoadingTransition = useAlertLoadingTransition()
const itemsListTransition = useItemsListTransition()
const formTransition = useFormTransition()

const loading = ref(false)
const dataStatus = ref(null)
const searchValue = ref("")
const hasSuccessMsg = ref(false)
const successMsgBody = ref("")
const hasErrorMsg = ref(false)
const errorMsgBody = ref("")

const selectedIds = ref([])
const itemsListData = ref([])

const formData = ref({
    userId: null,
    userName: null,
    remarks: null,
    selectedItemsList: ref([])
})
 
const filteredItemsListData = computed(() => {
  return itemsListData.value.filter(item => {
    return item.item_name.toLowerCase().includes(searchValue.value.toLowerCase())
  })
})

// FUNCTIONS APIs
function getItemsListData() {
    dataStatus.value = "Loading..."

    axiosClient.get('/api/items-list')
        .then(response => {
            itemsListData.value = response.data.itemsListData

            if(itemsListData.value.length < 1){
                dataStatus.value = "No data"
            }else{
                dataStatus.value = null
            }
        })
        .catch(error => {
            console.log(error);

            if(itemsListData.value.length < 1){
                dataStatus.value = "No data"
            }else{
                dataStatus.value = null
            }
        })    
}

function submitStockOut(){
    loading.value = true

    axiosClient.post('/api/stock-out', formData.value)
        .then(response => {
            loading.value = false
            showSuccessMsg("Successful stock transaction")
            getItemsListData()
            showItemsList("clear")
        })
        .catch(error => {
            loading.value = false
            showErrorMsg(error.response.data.message)            
        })
}


//OTHER FUNCTIONS
function showStockOutForm (){
    viewingItemsList.value = false
    viewingStockOutForm.value = true

      // change enter transition
    itemsListTransition.value.enterFrom = "opacity-0 -translate-x-full"
    itemsListTransition.value.enterTo = "translate-x-0 opacity-100"
    itemsListTransition.value.enterActive = "transition duration-[1000ms] ease"
}

function toggleSelectItem(id, name, cost, price, quantity = 1, unit){
    if (quantity <= 0){
        alert('This item has no stock left')
        return
    }

    if (selectedIds.value.includes(id)){
        formData.value.selectedItemsList = formData.value.selectedItemsList.filter(item => {
            return item.itemId != id
        });

        selectedIds.value = selectedIds.value.filter(i => {
            return i != id
        })
    }
    else{
        formData.value.selectedItemsList.push({
            itemId: id, 
            itemName: name, 
            itemCost: cost, 
            itemPrice: price,  
            quantityToOut: 1,
            unitName: unit
        })

        selectedIds.value.push(id)
    }
    
}

function closeSuccessMsg () {
    hasSuccessMsg.value = false
    successMsgBody.value = ""
}

function showSuccessMsg(msg) {
    successMsgBody.value = msg
    hasSuccessMsg.value = true
}

function showErrorMsg(msg){
    errorMsgBody.value = msg
    hasErrorMsg.value = true
}
function closeErrorMsg(){
    hasErrorMsg.value = false
    errorMsgBody.value = ""
}

function showItemsList (clear) {
    viewingItemsList.value = true
    viewingStockOutForm.value = false

    //clear values
    if (clear === "clear"){
        formData.value.remarks = ''
        formData.value.selectedItemsList = []
        selectedIds.value = []
    }
}

onMounted(() => {
    getItemsListData()
    viewingItemsList.value = true
    formData.value.userName = user.value.name
    formData.value.userId = user.value.id
})

</script>

<style scoped>

</style>