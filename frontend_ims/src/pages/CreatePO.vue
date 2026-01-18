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
            <section v-show="viewingItemsList" class=" h-full flex flex-col max-h-[1200px] min-h-[750px] lg:min-h-[500px] w-full max-w-[1300px] mx-auto">
                
                <div class="flex flex-col items-start px-2 mt-2 mb-4">
                    <h3 class="font-semibold tracking-wider">Create Purchase Order</h3>
                    <p class="text-sm ">Select one or more items then proceed</p>
                </div>

                <div class="flex flex-1 p-2 overflow-hidden">
                    <div class="flex flex-col flex-1 overflow-hidden bg-white rounded-xl shadow-[0px_1px_4px_rgba(0,0,0,0.1)] p-6">
                        
                        <!-- Search bar -->
                        <div class="flex mb-4">
                            <input 
                                v-model="searchValue" 
                                type="text" 
                                placeholder="Search by item name or SKU" 
                                class="search-bar"
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
                                        <div class="head-data">Cost</div>
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
                                        <div class="body-data item-cost-style-wide">
                                            ₱{{ item.cost }}
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
                                    <div class="my-2 flex-1 gap-3 overflow-hidden min-h-fit content-start grid grid-cols-[repeat(auto-fill,minmax(250px,1fr))] p-1">
                                    
                                        <!-- Card items -->
                                        <div 
                                            v-if="!dataStatus" 
                                            v-for="item in filteredItemsListData" 
                                            class="cursor-default flex flex-col shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-lg max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all"
                                        >
                                        
                                            <!-- Card head -->
                                            <div class="flex items-center  justify-between pt-5 pb-3 mx-5 mb-2 border-b border-gray-200">
                                                <div class="overflow-hidden">
                                                    <h5 class="item-name-style-narrow" :title="item.itemName">
                                                        {{ item.itemName }}
                                                    </h5>
                                                    <p class="item-sku-style-narrow"  :title="item.sku">
                                                        {{ item.sku ?? 'Not set' }}
                                                    </p>
                                                </div>
                                                <div class="justify-center ml-auto w-fit">
                                                    <button
                                                        @click="toggleSelectedItem(item)" 
                                                        :class=" selectedIds.includes(item.id) ? 'text-white bg-red-500' : 'border-2  border-[#45a049] text-[#45a049] bg-[#eef3ee]' " 
                                                        class="flex items-center justify-center rounded-full size-12"
                                                        type="button" 
                                                    >
                                                        <i :class="{'rotate-45': selectedIds.includes(item.id)}" class="text-lg transition duration-500 transform fa-solid fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Card body -->
                                            <div class="grid gap-2 px-5 pt-2 pb-5 cursor-default">

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="label-style-two">Quantity</p>
                                                    <span :class="getStockColor(item)" class="item-quantity-style-narrow ml-auto">
                                                        {{ item.quantity + ' ' + (item.unit ?? ( item.quantity < 2 ? 'unit' : 'units') ) }}
                                                    </span>
                                                </div>
                                                
                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="label-style-two">Cost</p>
                                                    <p class="ml-auto item-cost-style-narrow">
                                                        ₱{{ item.cost }}
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
                                <ButtonYellow type="button" @click="showPOForm" >
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
            <div v-show="viewingPOForm" class="flex flex-col w-full max-h-[1200px] min-h-[1150px] lg:min-h-[900px] h-full max-w-[1300px] mx-auto ">
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
                
                <form @submit.prevent="submitPO" class="flex flex-col flex-1 w-full p-2 mx-auto overflow-hidden">
                    <section class="shadow-[0px_1px_4px_rgba(0,0,0,0.1)] overflow-hidden flex-1 py-6 px-5 bg-white rounded-xl flex flex-col mb-4">
                        
                        <div class="flex flex-col items-center mb-8">
                            <h3 class="font-bold tracking-wide">Purchase Order</h3>
                        </div>

                        <div class="flex flex-col mb-6 px-1">
                            <p class="label-style-one">Supplier:</p>
                            <div @click="viewingSuppliersMenu = !viewingSuppliersMenu" class="relative cursor-pointer value-style suppliers-dropdown">
                                <div class="flex">
                                    <p v-if="formData.supplierName">
                                        {{ formData.supplierName }}
                                    </p>
                                    <p v-else class="text-gray-400 ">
                                        -- select supplier --
                                    </p>
                                    <span class="ml-auto">
                                        <i class="transition-all transform fa-solid fa-caret-down" :class="viewingSuppliersMenu ? '-rotate-180 ' : ''"></i>
                                    </span>
                                </div>

                                <Transition
                                    enter-from-class="opacity-0"
                                    enter-to-class="opacity-100"
                                    enter-active-class="duration-200 transition transform"
                                    leave-from-class="opacity-100"
                                    leave-to-class="opacity-0"
                                    leave-active-class="duration-200 transtion transform"
                                >
                                    <div v-if="viewingSuppliersMenu" class="z-30 p-3 absolute flex-col flex w-full bg-white rounded-md shadow-[0px_1px_4px_rgba(0,0,0,0.1)] -translate-x-3 translate-y-3 overflow-auto">
                                        <ul class="max-h-40 overflow-auto">
                                            <li @click="selectSupplier()" class="p-2 hover:bg-gray-50 cursor-pointer">
                                                None
                                            </li>

                                            <li
                                                v-for="supplier in suppliersListData"
                                                @click="selectSupplier(supplier)"
                                                class="p-2 hover:bg-gray-50 cursor-pointer"
                                            >
                                                {{ supplier.name }}
                                            </li>
                                        </ul>
                                    </div>
                                </Transition> 
                            </div>
                        </div>
                       
                        <div class="flex flex-col flex-1 py-4 mb-6 overflow-hidden border-darkgray-sec border-y">
                            <p class="label-style-one text-lg">Ordered Items</p>

                            <!-- Desktop table layout -->
                            <div class="relative flex-1 hidden overflow-hidden lg:flex">

                                <!-- Gradient overlay -->
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                                
                                <div class="flex-1 px-1 overflow-auto">
                                    <div class="grid grid-cols-[auto_20%_25%_20%_80px] head-row">
                                        <div class="head-data">Item</div>
                                        <div class="head-data">Cost</div>
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
                                        <div class="body-data item-cost-style-wide">
                                            ₱{{ item.cost }}
                                        </div>
                                        <div class="body-data">
                                            <div class="flex flex-row w-full border border-gray-300 rounded-lg">
                                                <input 
                                                    v-model="item.orderedQty"
                                                    class="flex-1 min-w-0 px-3 py-1.5 text-right text-sm bg-transparent focus:outline-none"
                                                    placeholder="0"
                                                    type="number"
                                                >
                                                <span class="grid px-3 text-gray-500 border-l border-gray-300 whitespace-nowrap place-items-center">
                                                    {{ item.unit ?? (item.orderedQty < 2 ? 'unit' : 'units') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="body-data item-subtotal-style-wide">
                                            ₱{{ (item.cost * item.orderedQty).toFixed(2)  }}
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
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                    
                                <div class="flex flex-1 overflow-auto">

                                    <!-- Card grid -->
                                    <div class="my-2 content-start flex-1 gap-3 p-1 overflow-hidden min-h-fit grid grid-cols-[repeat(auto-fill,minmax(250px,1fr))]">
                                    
                                        <!-- Card items -->
                                        <div v-for="item in formData.selectedItemsList" class="cursor-default flex flex-col shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-lg max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all">
                                        
                                            <!-- Card head -->
                                            <div class="flex items-center  justify-between pt-5 pb-3 mx-5 mb-2 border-b border-gray-200">
                                                <div class="overflow-hidden">
                                                    <h5 class="item-name-style-narrow" :title="item.itemName">
                                                        {{ item.itemName }}
                                                    </h5>
                                                    <p class="item-sku-style-narrow"  :title="item.sku">
                                                        {{ item.sku ?? 'Not set' }}
                                                    </p>
                                                </div>
                                                <div class="justify-center ml-auto w-fit">
                                                    <button 
                                                        @click="toggleSelectedItem(item)" 
                                                        :class=" selectedIds.includes(item.id) ? 'text-white bg-red-500' : 'border-2  border-[#45a049] text-[#45a049] bg-[#eef3ee]' " 
                                                        class="flex items-center justify-center rounded-full size-12"
                                                        type="button"
                                                    >
                                                        <i :class="{'rotate-45': selectedIds.includes(item.id)}" class="text-lg transition duration-500 transform fa-solid fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Card body -->
                                            <div class="grid gap-2 px-5 pt-2 pb-5">

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="label-style-two">Cost</p>
                                                    <p class="ml-auto item-cost-style-narrow">
                                                        ₱{{ item.cost }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-4 h-fit overflow-hidden flex-row items-center">
                                                    <p class="w-1/2 label-style-two">Quantity</p>

                                                    <div class="flex items-center justify-between w-1/2 transition bg-white border border-gray-300 rounded-md">
                                                        <input 
                                                            v-model="item.orderedQty"
                                                            class="flex-1 min-w-0 px-1 py-1.5 text-right text-sm bg-transparent"
                                                            placeholder="0"
                                                            type="number"
                                                        >
                                                        <span class="px-3 text-sm text-gray-500 border-l border-gray-200 whitespace-nowrap">
                                                            {{ item.unit ?? (item.orderedQty < 2 ? 'unit' : 'units') }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="flex gap-4 flex-row items-center pt-2 border-t h-fit overflow-hidden">
                                                    <p class="label-style-two">Subtotal</p>
                                                    <p class="ml-auto item-subtotal-style-narrow">
                                                        ₱{{ (item.cost * item.orderedQty).toFixed(2) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col w-full gap-4 mb-2 px-1">
                            <div class="flex flex-col flex-1">
                                <label for="remarks" class="label-style-one">Remarks:</label>
                                <textarea 
                                    v-model="formData.remarks" 
                                    id="remarks" 
                                    class="h-20 value-style cursor-text"
                                >
                                </textarea>
                            </div>
                            <div class="flex flex-col flex-1">
                                <p class="label-style-one">Created by:</p>
                                <p class="cursor-default value-style">
                                    {{ formData.userName }}
                                </p>                            
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
const formTransition = useFormTransition()
const mainContentTransition = useMainContentTransition()

const viewingItemsList = ref(null)
const viewingPOForm = ref(null)
const viewingSuppliersMenu = ref(false)
const pageContent = ref(null)

const searchValue = ref("")
const selectedIds = ref([])
const dataStatus = ref(null)
const loading = ref(false)

const itemsListData = ref([])
const suppliersListData = ref([])

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
    supplierName: null,
    supplierId: null,
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
        suppliersListData.value = response.data.suppliersListData

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

async function submitPO(){
    loading.value = true

    try {
        const response = await axiosClient.post('/api/save-purchase-order', formData.value)
        
        showSuccessMsg(response.data.poNumber, response.data.message)
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
    }else if(content === 'Create PO'){
        viewingPOForm.value = true
    }else{
        return
    }
    
    mainContentTransition.value.enterFrom = "-translate-x-1/2 opacity-0"
}

function showItemsList(clear) {
    viewingPOForm.value = false

    if(clear === "clear"){
        formData.value.remarks = null
        formData.value.supplierId = null
        formData.value.supplierName = null
        formData.value.selectedItemsList = []
        selectedIds.value = []
        
        viewingSuppliersMenu.value = false
    }
}

function showPOForm (){
    viewingItemsList.value = false
    pageContent.value = "Create PO"
}

function toggleSelectedItem(item){
    if (selectedIds.value.includes(item.id)){
        formData.value.selectedItemsList = formData.value.selectedItemsList.filter(selectedItem => {
            return selectedItem.id != item.id
        });

        selectedIds.value = selectedIds.value.filter(id => {
            return id != item.id
        })
    }
    else{
        formData.value.selectedItemsList.push({
            id: item.id, 
            itemName: item.itemName, 
            cost: item.cost,
            orderedQty: 1,
            unit: item.unit,
            category: item.category,
            sku: item.sku
        })

        selectedIds.value.push(item.id)
    }
}

function handleClickOutside(e) {
    if(!e.target.closest('.suppliers-dropdown')){
        viewingSuppliersMenu.value = false
    }
}

function selectSupplier(supplier){
    if(supplier){
        formData.value.supplierId = supplier.id
        formData.value.supplierName = supplier.name
    }else{
        formData.value.supplierId = null
        formData.value.supplierName = null
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