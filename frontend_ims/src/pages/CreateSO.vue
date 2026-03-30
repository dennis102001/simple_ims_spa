<template>
    <div class="relative h-full w-full min-h-fit min-w-[200px]">
        
        <!-- Items list -->
        <transition
            :enter-from-class="mainContentTransition.enterFrom "
            :enter-to-class="mainContentTransition.enterTo"
            :enter-active-class="mainContentTransition.enterActive"
            :leave-from-class="mainContentTransition.leaveFrom"
            :leave-to-class="mainContentTransition.leaveTo"
            :leave-active-class="mainContentTransition.leaveActive"
            @after-leave="changeContent(pageContent)"
        >
            <section v-show="viewingItemsList" class=" h-full flex flex-col max-h-[1200px] min-h-[800px] lg:min-h-[600px] w-full max-w-[1300px] mx-auto ">
                
                <div class="flex flex-col items-start px-2 mt-2 mb-4">
                    <h3 class="font-semibold tracking-wider">Create Sale Order</h3>
                    <p class="text-sm ">Select one or more items then proceed</p>
                </div>
                
                <div class="flex flex-1 p-2 overflow-hidden">
                    <div class="flex flex-col flex-1 overflow-hidden bg-white rounded-xl shadow-[0px_1px_4px_rgba(0,0,0,0.1)] p-6">

                        <!-- Search bar -->
                        <div class="flex mb-4">
                            <input 
                                v-model="searchValue" 
                                placeholder="Search by item name or SKU" 
                                class="search-bar"
                                type="text" 
                            >
                        </div>

                        <div class="flex flex-col flex-1 pb-2 mb-4 overflow-hidden border-b border-darkgray-sec">

                            <!-- Desktop table layout -->
                            <div class="relative flex-1 hidden overflow-hidden lg:flex">

                                <!-- Gradient overlay -->
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>

                                <div class="flex-1 px-1 overflow-auto">
                                    <div class="grid grid-cols-[auto_25%_15%_15%_80px] head-row">
                                        <div class="head-data">Item</div>
                                        <div class="head-data">Category</div>
                                        <div class="head-data">Price</div>
                                        <div class="head-data">Quantity</div>
                                        <div class="justify-center head-data">Action</div>
                                    </div>

                                    <div v-if="dataStatus" class="flex items-center justify-center w-full py-3">
                                        <p>{{ dataStatus }}</p>
                                    </div>

                                    <div v-if="!dataStatus" v-for="item in filteredItemsListData" class="grid grid-cols-[auto_25%_15%_15%_80px] body-row">
                                        <div class="grid body-data">
                                            <p class="item-name-style-wide" :title="item.itemName">
                                                {{ item.itemName }}
                                            </p>
                                            <p class="item-sku-style-wide" :title="item.sku">
                                                {{ item.sku }}
                                            </p>
                                        </div>
                                        <div class="body-data item-category-style-wide" :title="item.category">
                                            {{ item.category }}
                                        </div>
                                        <div class="body-data item-price-style-wide">
                                            ₱{{ item.price }}
                                        </div>
                                        <div class="body-data">
                                            <div class="item-quantity-style-wide" :class="getStockColor(item)">
                                                {{ item.quantity + ' ' + (item.unit ?? ( item.quantity < 2 ? 'unit' : 'units') ) }}
                                            </div>
                                        </div>
                                        <div class="justify-center body-data">
                                            <button 
                                                @click="toggleSelectedItem(item)" 
                                                :class=" selectedIds.includes(item.id) ? 'bg-red-500 text-white' : 'border-2 border-[#45a049] text-[#45a049] bg-[#eef3ee]' " 
                                                class="flex items-center justify-center rounded-full size-12"
                                                type="button"
                                            >
                                                <i :class="{'rotate-45': selectedIds.includes(item.id)}" class="transition duration-500 transform fa-solid fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile card layout -->
                            <div class="relative flex flex-1 overflow-hidden lg:hidden">

                                <!-- Gradient overlays -->
                                <div class="absolute top-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-b from-white to-transparent"></div>
                                <div class="absolute bottom-0 left-0 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                    
                                <div class="flex flex-1 overflow-auto">

                                    <div v-if="dataStatus" class="flex items-center justify-center w-full py-3">
                                        <p>{{ dataStatus }}</p>
                                    </div>

                                    <!-- Card grid -->
                                    <div class="my-2 content-start flex-1 gap-3 p-1 overflow-hidden min-h-fit grid grid-cols-[repeat(auto-fill,minmax(250px,1fr))]">
                                    
                                        <!-- Card items -->
                                        <div v-if="!dataStatus" v-for="item in filteredItemsListData" class="flex flex-col cursor-default shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-lg max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all">
                                        
                                            <!-- Card head -->
                                            <div class="flex items-center  justify-between pt-5 pb-3 mx-5 mb-2 border-b border-gray-200">
                                                <div class="overflow-hidden">
                                                    <h5 class="item-name-style-narrow" :title="item.itemName">
                                                        {{ item.itemName }}
                                                    </h5>
                                                    <p class="item-sku-style-narrow" :title="item.sku">
                                                        {{ item.sku ?? 'Not set' }}
                                                    </p>
                                                </div>

                                                <div class="justify-center ml-auto w-fit">
                                                    <button 
                                                        @click="toggleSelectedItem(item)" 
                                                        :class=" selectedIds.includes(item.id) ? 'text-white bg-red-500' : 'border-2  border-[#45a049] text-[#45a049] bg-[#eef3ee]' " 
                                                        class="rounded-full size-12 "
                                                        type="button"
                                                    >
                                                        <i :class="{'rotate-45': selectedIds.includes(item.id)}" class="text-lg transition duration-500 transform fa-solid fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Card body -->
                                            <div class="grid gap-2 px-5 pt-2 pb-5">

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="label-style-two">Quantity</p>
                                                    <span :class="getStockColor(item)" class="item-quantity-style-narrow ml-auto">
                                                        {{ item.quantity + ' ' + (item.unit ?? ( item.quantity < 2 ? 'unit' : 'units') ) }}
                                                    </span>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="label-style-two">Price</p>
                                                    <p class="ml-auto item-price-style-narrow">
                                                        ₱{{ item.price }}
                                                    </p>
                                                </div>
                                                
                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center ">
                                                    <p class="label-style-two">Category</p>
                                                    <p class="ml-auto item-category-style-narrow" :title="item.category">
                                                        {{ item.category ?? 'Not Set' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-center lg:justify-end">
                            <div class="relative w-fit">
                                <p v-show="selectedIds.length" class="absolute top-0 left-0 z-10 p-1 text-xs text-center text-white -translate-x-1 -translate-y-1 rounded-full bg-darkgray-sec min-h-6 min-w-6">
                                    {{ selectedIds.length }}
                                </p>

                                <ButtonYellow @click="showSOForm" type="button">
                                    <div class="relative flex flex-row items-center justify-end w-40">
                                        <span class="mx-auto">Proceed</span>
                                        <span class="absolute text-sm px-2">
                                            <i class="fa-solid fa-chevron-right"></i>
                                        </span>
                                    </div>
                                </ButtonYellow>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </transition>

        <!-- Create form -->
        <transition
            :enter-from-class="formTransition.enterFrom"
            :enter-to-class="formTransition.enterTo"
            :enter-active-class="formTransition.enterActive"
            :leave-from-class="formTransition.leaveFrom"
            :leave-to-class="formTransition.leaveTo"
            :leave-active-class="formTransition.leaveActive"
            @after-leave="changeContent('Items List')"
        >
            <div v-show="viewingSOForm" class="flex flex-col w-full max-h-[1300px] min-h-[1200px] lg:max-h-[1200px] lg:min-h-[1100px] h-full max-w-[1300px] mx-auto ">
                <div class="flex items-start justify-start w-full px-2 mx-auto mb-2 h-fit">
                    <ButtonDark type="button" @click="showItemsList('keep')">
                        <div class="relative flex flex-row items-center w-52 ">
                            <span class="absolute text-sm px-2">
                                <i class="fa-solid fa-chevron-left"></i>
                            </span>
                            <span class="mx-auto">Add more items</span>
                        </div>
                    </ButtonDark>
                </div>

                <form @submit.prevent="submitSO" class="flex flex-col flex-1 w-full p-2 mx-auto overflow-hidden">
                    <section class="shadow-[0px_1px_4px_rgba(0,0,0,0.1)] overflow-hidden flex-1 p-6 sm:p-8 bg-white rounded-xl flex flex-col mb-4">
                        <h3 class=" font-bold text-left px-1">Record Sale</h3>
                        <p class="mb-5 text-sm text-gray-500 px-1">Fill in the details to record a new sale</p>

                        <hr class="mb-5 mx-1">

                        <div class="mb-5 flex items-center gap-2 text-lg">
                            <i class="fas fa-info-circle"></i>
                            <span class="mr-2 font-medium">Sale Details</span>
                        </div>

                        <SelectDropdown
                            v-model="formData.customerId"
                            label="Customer:"
                            :listData="customersListData"
                        />
                        
                        <TextArea
                            v-model="formData.remarks" 
                            id="remarks" 
                            label="Remarks:"
                        />

                        <TextInput
                            v-model="formData.userName" 
                            id="created-by" 
                            label="Created by:"
                            type="text"
                            disabled
                        />

                        <hr class="mt-2 mb-5 mx-1">

                        <div class="flex flex-col flex-1 overflow-hidden">
                            <div class="mb-5 flex items-center gap-2 text-lg">
                                <i class="fas fa-list"></i>
                                <span class="mr-2 font-medium">Selected Items</span>
                            </div>

                            <!-- Desktop table layout -->
                            <div class="relative flex-1 hidden overflow-hidden lg:flex">

                                <!-- Gradient overlay -->
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>

                                <div class="flex-1 px-1 overflow-auto">
                                    <div class="grid grid-cols-[auto_20%_25%_20%_80px] head-row">
                                        <div class="head-data">Item</div>
                                        <div class="head-data">Price</div>
                                        <div class="head-data">Quantity</div>
                                        <div class="head-data">Subtotal</div>
                                        <div class="justify-center head-data">Action</div>
                                    </div>

                                    <div v-for="item in formData.selectedItemsList" class="grid grid-cols-[auto_20%_25%_20%_80px] body-row">
                                        <div class="grid body-data">
                                            <p class="item-name-style-wide" :title="item.itemName">
                                                {{ item.itemName }}
                                            </p>
                                            <p class="item-sku-style-wide" :title="item.sku">
                                                {{ item.sku }}
                                            </p>
                                        </div>
                                        <div class="body-data item-price-style-wide">
                                            ₱{{ item.price }}
                                        </div>
                                        <div class="body-data">
                                            <div class="flex flex-row w-full border border-gray-300 rounded-lg">
                                                <input 
                                                    v-model="item.quantity"
                                                    type="number"
                                                    class="flex-1 min-w-0 px-3 py-1.5 text-right text-sm bg-transparent focus:outline-none"
                                                    placeholder="0"
                                                >
                                                <span class="grid px-3 text-gray-500 border-l border-gray-300 whitespace-nowrap place-items-center">
                                                    {{ item.unit ?? (item.quantity < 2 ? 'unit' : 'units') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="body-data item-subtotal-style-wide">
                                            ₱{{ (item.price * item.quantity).toFixed(2)  }}
                                        </div>
                                        <div class="justify-center body-data">
                                            <button 
                                                @click="toggleSelectedItem(item)" 
                                                class="flex items-center justify-center text-white bg-red-500 rounded-full size-12"
                                                type="button" 
                                            >
                                                <i class="rotate-45 fa-solid fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>                            
                                </div>
                            </div>

                            <!-- Mobile card layout -->
                            <div class="relative flex flex-1 overflow-hidden lg:hidden">
                                
                                <!-- Gradient overlays -->
                                <div class="absolute top-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-b from-white to-transparent"></div>
                                <div class="absolute bottom-0 left-0 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                    
                                <div class="flex flex-1 overflow-auto">

                                    <!-- Card grid -->
                                    <div class="my-2 content-start flex-1 gap-3 p-1 overflow-hidden min-h-fit grid grid-cols-[repeat(auto-fill,minmax(250px,1fr))]">
                                    
                                        <!-- Card items -->
                                        <div v-for="item in formData.selectedItemsList" class="flex flex-col cursor-default shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-lg max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all">
                                        
                                            <!-- Card head -->
                                            <div class="flex items-center  justify-between pt-5 pb-3 mx-5 mb-2 border-b border-gray-200">
                                                <div class="overflow-hidden">
                                                    <h5 class="item-name-style-narrow" :title="item.itemName">
                                                        {{ item.itemName }}
                                                    </h5>
                                                    <p class="item-sku-style-narrow" :title="item.sku">
                                                        {{ item.sku ?? 'Not set' }}
                                                    </p>
                                                </div>
                                                
                                                <div class="justify-center ml-auto w-fit">
                                                    <button 
                                                        @click="toggleSelectedItem(item)" 
                                                        :class=" selectedIds.includes(item.id) ? 'text-white bg-red-500' : 'border-2  border-[#45a049] text-[#45a049] bg-[#eef3ee]' " 
                                                        class="rounded-full size-12 "
                                                        type="button"
                                                    >
                                                        <i :class="{'rotate-45': selectedIds.includes(item.id)}" class="text-lg transition duration-500 transform fa-solid fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Card body -->
                                            <div class="grid gap-2 px-5 pt-2 pb-5">

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="label-style-two">Price</p>
                                                    <p class="ml-auto item-price-style-narrow">
                                                        ₱{{ item.price }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="w-1/2 label-style-two">
                                                        Quantity
                                                    </p>
                                                    <div class="flex items-center justify-between w-1/2 transition bg-white border border-gray-300 rounded-md">
                                                        <input 
                                                            v-model="item.quantity"
                                                            class="flex-1 min-w-0 px-1 py-1.5 text-right text-sm bg-transparent"
                                                            placeholder="0"
                                                            type="number"
                                                        >
                                                        <span class="px-3 text-sm text-gray-500 border-l border-gray-200 whitespace-nowrap">
                                                            {{ item.unit ?? (item.quantity < 2 ? 'unit' : 'units') }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="flex gap-4 flex-row items-center pt-2 border-t h-fit overflow-hidden">
                                                    <p class="label-style-two">Subtotal</p>
                                                    <p class="ml-auto item-subtotal-style-narrow">
                                                        ₱{{ (item.price * item.quantity).toFixed(2)  }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="flex items-end justify-end w-full">
                        <div class="grid grid-cols-2 w-full md:w-[400px] gap-2 lg:gap-4">
                            <ButtonYellow width="full" type="submit">
                                Save
                            </ButtonYellow>
                            
                            <ButtonWhite @click="showItemsList('clear')" width="full" type="button">
                               Cancel
                            </ButtonWhite>
                        </div>
                    </div>
                </form>
            </div>
        </transition>
    </div>
    
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
                    {{ successMsgTitle }} 
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
import { useMainContentTransition, useFormTransition, useAlertLoadingTransition } from '../composables/useTransition';
import { ref, onMounted, computed, onBeforeUnmount } from 'vue';
import { useAlertMessages } from '../composables/useAlertMessages';
import axiosClient from '../axios';
import useUserStore from '../store/user';
import SuccessBanner from '../components/alerts/SuccessBanner.vue';
import ErrorBanner from '../components/alerts/ErrorBanner.vue';
import Loading from '../components/alerts/Loading.vue';
import ButtonYellow from '../components/buttons/ButtonYellow.vue';
import ButtonDark from '../components/buttons/ButtonDark.vue';
import ButtonWhite from '../components/buttons/ButtonWhite.vue';
import SelectDropdown from '../components/SelectDropdown.vue';
import TextArea from '../components/TextArea.vue';
import TextInput from '../components/TextInput.vue';

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

const userStore = useUserStore()
const user = computed(() => userStore.user)

const alertLoadingTransition = useAlertLoadingTransition()
const mainContentTransition = useMainContentTransition()
const formTransition = useFormTransition()

const viewingItemsList = ref(null)
const viewingSOForm = ref(null)
const viewingCustomersMenu = ref(false)
const pageContent = ref(null)

const searchValue = ref("")
const selectedIds = ref([])
const dataStatus = ref(null)
const loading = ref(false)

const itemsListData = ref([])
const customersListData = ref([])

const colors = ref({
    low: null,
    zero: null
})

function getStockColor(item){
    if (item.quantity < 1){
        return colors.value.zero
    }
    else if(item.quantity <= item.reorderLevel){
        return colors.value.low
    }
    else {
        return "bg-emerald-500"
    }
}

const colorClass = ref({
    red: 'bg-red-500',
    yellow: 'bg-yellow-500',
    orange: 'bg-orange-500',
    green: 'bg-green-500',
    blue: 'bg-blue-500',
    violet: 'bg-violet-500',
    pink: 'bg-pink-500',
    gray: 'bg-gray-500',
})

const formData = ref({
    userId: null,
    userName: null,
    remarks: null,
    customerId: null,
    selectedItemsList: ref([])
})

const filteredItemsListData = computed(() => {
    return itemsListData.value.filter(item => {
        return item.itemName.toLowerCase().includes(searchValue.value.toLowerCase()) || 
            item.sku.toLowerCase().includes(searchValue.value.toLowerCase())
    })
})

async function getItemsListData() {
    dataStatus.value = "Loading..."

    try {
        const response = await axiosClient.get('/api/items-list')

        itemsListData.value = response.data.itemsListData
        customersListData.value = response.data.customersListData

        const lowStockColor = response.data.colors?.lowStockColor?.color_name
        if(lowStockColor){
            colors.value.low = colorClass.value[lowStockColor]
        }else{
            colors.value.low = "bg-emerald-500"
        }

        const zeroStockColor = response.data.colors?.noStockColor?.color_name
        if(zeroStockColor){
            colors.value.zero = colorClass.value[zeroStockColor]
        }else{
            colors.value.zero = "bg-emerald-500"
        }
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to load items list data')
    }
    finally{
        if(itemsListData.value.length < 1){
            dataStatus.value = "No data"
        }else{
            dataStatus.value = null
        }
    }
}

async function submitSO(){
    loading.value = true

    try {
        const response = await axiosClient.post('/api/save-sales', formData.value)

        showSuccessMsg(response.data.soNumber, response.data.message)
        await getItemsListData()
        showItemsList("clear")
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Saving failed')
    }
    finally{
        loading.value = false
    }
}

function changeContent(content){
    if(content === 'Items List'){
        viewingItemsList.value = true
    }else if(content === 'Create SO'){
        viewingSOForm.value = true
    }else{
        return
    }

    mainContentTransition.value.enterFrom = "-translate-x-1/2 opacity-0"
}

function showItemsList(clear) {
    viewingSOForm.value = false

    if(clear === "clear"){
        formData.value.remarks = ''
        formData.value.customerId = ''
        formData.value.customerName = ''
        formData.value.selectedItemsList = []
        selectedIds.value = []
    }
}

function showSOForm (){
    viewingItemsList.value = false
    pageContent.value = "Create SO"
}

function toggleSelectedItem(item){
    if (selectedIds.value.includes(item.id)){
        formData.value.selectedItemsList = formData.value.selectedItemsList.filter(selectedItem => {
            return selectedItem.id != item.id
        });

        selectedIds.value = selectedIds.value.filter(i => {
            return i != item.id
        })
    }
    else{
        formData.value.selectedItemsList.push({
            id: item.id, 
            itemName: item.itemName, 
            price: item.price,  
            quantity: 1,
            unit: item.unit,
            sku: item.sku
        })

        selectedIds.value.push(item.id)
    }
}

function handleClickOutside(e) {
    if(!e.target.closest('.customers-dropdown')){
        viewingCustomersMenu.value = false
    }
}

onMounted(() => {
    getItemsListData()
    viewingItemsList.value = true
    formData.value.userName = user.value.name
    formData.value.userId = user.value.id
    
    document.addEventListener("click", handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside)
})

</script>

<style scoped>

</style>