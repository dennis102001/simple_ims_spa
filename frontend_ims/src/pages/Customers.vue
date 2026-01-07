<template>
    <div class="relative h-full w-full min-h-fit min-w-[200px]">
     
        <!-- CUSTOMERS LIST -->
        <transition
        :enter-from-class="mainContentTransition.enterFrom "
        :enter-to-class="mainContentTransition.enterTo"
        :enter-active-class="mainContentTransition.enterActive"
        :leave-from-class="mainContentTransition.leaveFrom"
        :leave-to-class="mainContentTransition.leaveTo"
        :leave-active-class="mainContentTransition.leaveActive"
        @after-leave="changeContent(pageContent)"
        >
            <div v-show="viewingCustomersList" class="h-full flex flex-col max-h-[1200px] min-h-[700px] w-full max-w-[1300px] mx-auto">

                <div class="flex flex-row items-start justify-between px-2 mt-2 mb-4">
                    <h3 class="font-semibold tracking-wider">Customers List</h3>
                    <ButtonYellow @click="showAddForm" type="button">
                        <div class="relative flex flex-row items-center w-fit ">
                            <span class="text-sm px-2">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                            <span class="pr-4">New Customer</span>
                        </div>
                    </ButtonYellow>
                </div>

                <div class="flex flex-1 p-2 overflow-hidden">
                    <div class="flex flex-col flex-1 overflow-hidden min-h-[350px] bg-white rounded-xl shadow-[0px_2px_4px_rgba(0,0,0,0.1)] p-6">
                        
                        <!-- search -->
                        <div class="flex mb-4">
                            <input v-model="searchValue" type="text" placeholder="Search by name or phone" class="search-bar">
                        </div>

                        <section class="flex flex-col flex-1 pb-2 overflow-hidden">

                            <!-- div-based table -->
                            <div class="relative flex-1 hidden overflow-hidden lg:flex">
                                <!-- gradient overlays -->
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                                
                                <div class="flex-1 px-1 overflow-auto">
                                    <!-- head -->
                                    <div class="head-row grid grid-cols-[30%_30%_30%_10%]">
                                        <div class="head-data">Name</div>
                                        <div class="head-data">Phone</div>
                                        <div class="head-data">Address</div>
                                        <div class="justify-center head-data">Action</div>
                                    </div>
                                    <!-- body -->
                                    <div v-if="dataStatus" class="flex items-center justify-center w-full py-3">
                                        <p>{{ dataStatus }}</p>
                                    </div>
                                    <div v-if="!dataStatus" v-for="customer in filteredCustomersListData" class="body-row grid grid-cols-[30%_30%_30%_10%]">
                                        <div class="grid body-data">
                                            <p class="style-wide-customer-name" :title="customer.name">{{ customer.name }}</p>
                                            <p class="style-wide-customer-email" :title="customer.email">{{ customer.email ?? 'no email' }}</p>
                                        </div>
                                        <div class="body-data style-wide-customer-phone" :title="customer.phone">{{ customer.phone }}</div>
                                        <div class="body-data style-wide-customer-address" :title="customer.address">{{ customer.address }}</div>
                                        <div class="flex justify-center items-center body-data">
                                            <button @click="toggleMoreActions(customer.id)" class="px-4 py-1 rounded-md btnActions hover:bg-gray-300">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </button>
                                            <Transition
                                                enter-from-class="translate-x-full opacity-0 "
                                                enter-to-class="translate-x-0 opacity-100"
                                                enter-active-class="transition duration-300 ease-out transform"
                                                leave-from-class="translate-x-0 opacity-100"
                                                leave-to-class="translate-x-full opacity-0"
                                                leave-active-class="transition duration-300 ease-in transform"
                                            >                
                                            <div v-if="currentCustomerId == customer.id" class="absolute inset-0 flex items-center justify-end w-full h-full ">
                                                <div class="bg-gradient-to-r flex-1 h-full from-[rgba(0,0,0,0.01)] to-darkgray-pri "></div>
                                                <div class="flex items-center justify-end h-full p-2 bg-darkgray-pri w-fit btnActions">
                                                    <button 
                                                        type="button"
                                                        @click="showUpdateForm(customer)" 
                                                        class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-darkgray-sec"
                                                    >
                                                        Update
                                                    </button>
                                                    <button 
                                                        @click="openConfirmModal('Delete Customer', 'Are you sure you want to delete this customer?', customer.id)"
                                                        type="button" 
                                                        class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-red-700 "
                                                    >
                                                        Delete
                                                    </button>
                                                </div> 
                                            </div>
                                            </Transition>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- grid items for small screen -->
                            <div class="relative flex flex-1 overflow-hidden lg:hidden">
                                <!-- gradient overlays -->
                                <div class="absolute top-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-b from-white to-transparent"></div>
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                    
                                <div class="flex flex-1 overflow-auto">
                                    <div v-if="dataStatus" class="flex items-center justify-center w-full py-3">
                                        <p>{{ dataStatus }}</p>
                                    </div>

                                    <!-- grid - margin 2 for gradient overlays -->
                                    <div class="my-2 content-start flex-1 gap-3 p-1 overflow-hidden min-h-fit grid grid-cols-[repeat(auto-fill,minmax(250px,1fr))]">
                                    
                                        <!-- grid items -->
                                        <div v-if="!dataStatus" v-for="customer in filteredCustomersListData" class="cursor-default relative flex flex-col rounded-lg shadow-emerald-300 shadow-[0_2px_4px] max-h-full">
                                        
                                            <!-- card head -->
                                            <div class="flex items-center pt-5 pb-3 mx-5 mb-2 border-b border-gray-200">
                                                <h5 class="style-narrow-customer-name" :title="customer.name">{{ customer.name }}</h5>
                                                
                                                <button 
                                                    type="button"
                                                    @click="toggleMoreActions(customer.id)"
                                                    class="px-4 py-1 ml-auto rounded-md btnActions hover:bg-gray-300"
                                                >
                                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                                </button>
                                            </div>

                                            <!-- card body -->
                                            <div class="grid gap-4 px-5 pt-2 pb-8 ">
                                                
                                                <!-- Phone -->
                                                <div class="flex flex-col flex-wrap h-fit overflow-hidden">
                                                    <p class="label-style-two mb-1">Phone:</p>
                                                    <p class="ml-auto style-narrow-customer-phone" :title="customer.phone">{{ customer.phone }}</p>
                                                </div>

                                                <!-- Email -->
                                                <div class="flex flex-col flex-wrap h-fit overflow-hidden">
                                                    <p class="label-style-two mb-1">Email:</p>
                                                    <p class="style-narrow-customer-email" :title="customer.email">{{ customer.email ?? 'not set' }}</p>
                                                </div>

                                                <!-- Address -->
                                                <div class="flex flex-col flex-wrap h-fit overflow-hidden">
                                                    <p class="label-style-two mb-1">Address:</p>
                                                    <p class="ml-auto style-narrow-customer-address" :title="customer.address">{{ customer.address ?? 'not set' }}</p>
                                                </div>

                                            </div>

                                            <!-- More Actions -->
                                                <Transition
                                                    enter-from-class="opacity-0"
                                                    enter-to-class="opacity-100 "
                                                    enter-active-class="transition duration-300 ease-out"
                                                    leave-from-class="opacity-100 "
                                                    leave-to-class="opacity-0"
                                                    leave-active-class="transition duration-200 ease-in"
                                                >                
                                                <div v-if="currentCustomerId == customer.id" class="absolute flex flex-col items-center justify-center w-full h-full gap-1 rounded-lg bg-black/60">
                                                    <div class="grid w-3/5 grid-cols-1 gap-1 btnActions min-w-[150px]">
                                                        <button 
                                                            type="button"
                                                            @click="showUpdateForm(customer)" 
                                                            class="w-full px-4 py-2 bg-white rounded-lg hover:shadow-[0_0_6px] hover:shadow-white/70" 
                                                        >
                                                            Edit
                                                        </button>
                                                        <button 
                                                            type="button" 
                                                            @click="openConfirmModal('Delete Customer', 'Are you sure you want to delete this customer?', customer.id)"
                                                            class="w-full px-4 py-2 bg-white text-red-700 rounded-lg hover:shadow-[0_0_6px] hover:shadow-red-500/90"
                                                        >
                                                            Delete
                                                        </button>
                                                    </div>
                                                </div>
                                                </Transition>

                                        </div>
                                    </div>
                                
                                </div>
                            </div>

                        </section>
                    </div>
                </div>
                
            </div>
        </transition>

        <!-- ADD FORM -->
        <transition
        :enter-from-class="formTransition.enterFrom"
        :enter-to-class="formTransition.enterTo"
        :enter-active-class="formTransition.enterActive"
        :leave-from-class="formTransition.leaveFrom"
        :leave-to-class="formTransition.leaveTo"
        :leave-active-class="formTransition.leaveActive"
        @after-leave="changeContent('Customers List')"
        >
            <div v-show="viewingAddForm" class="w-full ">

                <form @submit.prevent="submitAddForm" class="w-full max-w-sm p-6 mx-auto mb-4 bg-white shadow-[0px_2px_4px_rgba(0,0,0,0.1)] rounded-xl">
                    <h2 class="mb-10 text-2xl font-bold text-center">New Customer</h2>

                    <div class="flex flex-col w-full h-[76px]">
                        <label for="" class="label-style-one">Name:</label>
                        <input v-model="addFormData.name" type="text" class="value-style">
                        <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.name ? errors.name[0] : '' }}</p>
                    </div>
                    <div class="flex flex-col w-full h-[76px]">
                        <label for="" class="label-style-one">Phone:</label>
                        <input v-model="addFormData.phone" type="text" class="value-style">
                        <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.phone ? errors.phone[0] : '' }}</p>
                    </div>
                    <div class="flflex flex-col w-full h-[76px]">
                        <label for="" class="label-style-one">Email:</label>
                        <input v-model="addFormData.email" type="text" class="value-style">
                        <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.email ? errors.email[0] : '' }}</p>
                    </div>
                    <div class="flflex flex-col w-full h-[76px] mb-4">
                        <label for="" class="label-style-one">Address:</label>
                        <input v-model="addFormData.address" type="text" class="value-style">
                        <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.address ? errors.address[0] : '' }}</p>
                    </div>
                    <div class="flex items-end justify-end w-full">
                        <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
                            <ButtonYellow type="submit" width="full">
                                <div class="relative flex flex-row items-center w-full">
                                    <span class="mx-auto">Save</span>
                                </div>
                            </ButtonYellow>
                            <ButtonWhite @click="showCustomersList" type="button" width="full">
                                <div class="relative flex flex-row items-center w-full">
                                    <span class="mx-auto">Cancel</span>
                                </div>
                            </ButtonWhite>
                        </div>
                    </div>
                </form>

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
        @after-leave="changeContent('Customers List')"
        >
            <div v-show="viewingUpdateForm" class="w-full ">

                <form @submit.prevent="submitUpdateForm(updateFormData.id)" class="w-full max-w-sm p-6 mx-auto mb-4 bg-white shadow-[0px_2px_4px_rgba(0,0,0,0.1)] rounded-xl">
                    <h2 class="mb-10 text-2xl font-bold text-center">Update Customer</h2>

                    <div class="flex flex-col w-full h-[76px]">
                        <label for="" class="label-style-one">Name:</label>
                        <input v-model="updateFormData.name" type="text" class="value-style">
                        <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.name ? errors.name[0] : '' }}</p>
                    </div>
                    <div class="flex flex-col w-full h-[76px]">
                        <label for="" class="label-style-one">Phone:</label>
                        <input v-model="updateFormData.phone" type="text" class="value-style">
                        <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.phone ? errors.phone[0] : '' }}</p>
                    </div>
                    <div class="flex flex-col w-full h-[76px]">
                        <label for="" class="label-style-one">Email:</label>
                        <input v-model="updateFormData.email" type="text" class="value-style">
                        <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.email ? errors.email[0] : '' }}</p>
                    </div>
                    <div class="flex flex-col w-full h-[76px] mb-4">
                        <label for="" class="label-style-one">Address:</label>
                        <input v-model="updateFormData.address" type="text" class="value-style">
                        <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.address ? errors.address[0] : '' }}</p>
                    </div>
                    <div class="flex items-end justify-end w-full">
                        <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
                            <ButtonYellow type="submit" width="full">
                                <div class="relative flex flex-row items-center w-full">
                                    <span class="mx-auto">Save</span>
                                </div>
                            </ButtonYellow>
                            <ButtonWhite @click="showCustomersList" type="button" width="full">
                                <div class="relative flex flex-row items-center w-full">
                                    <span class="mx-auto">Cancel</span>
                                </div>
                            </ButtonWhite>
                        </div>
                    </div>
                </form>

            </div>
        </transition>

    </div>

    <!-- LOADING SUCCESS ERROR CONFIRM-->
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

        <ConfirmModal 
        :inUse="viewingConfirmModal" 
        @cancel="viewingConfirmModal = !viewingConfirmModal"
        @yes="deleteCustomer"
        >
            <template v-slot:title>{{ confirmTitle }}</template>
            <template v-slot:body>{{ confirmBody }}</template>
        </ConfirmModal>
    </teleport>
</template>

<script setup>
import { useAlertLoadingTransition, usePopUpTransition, useMainContentTransition, useFormTransition } from '../composables/useTransition';
import { onMounted, ref, computed, onBeforeUnmount } from 'vue';
import { useAlertMessages } from '../composables/useAlertMessages';
import { useConfirmModal } from '../composables/useConfirmModal';
import axiosClient from '../axios';

import Loading from '../components/alerts/Loading.vue';
import SuccessBanner from '../components/alerts/SuccessBanner.vue';
import ErrorBanner from '../components/alerts/ErrorBanner.vue';
import ButtonYellow from '../components/buttons/ButtonYellow.vue';
import ButtonDark from '../components/buttons/ButtonDark.vue';
import ButtonRed from '../components/buttons/ButtonRed.vue';
import ConfirmModal from '../components/ConfirmModal.vue';
import ButtonGreen from '../components/buttons/ButtonGreen.vue';
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

const { 
  viewingConfirmModal, 
  confirmTitle,
  confirmBody,
  confirmId,
  openConfirmModal 
} = useConfirmModal()

const alertLoadingTransition = useAlertLoadingTransition()
const mainContentTransition = useMainContentTransition()
const popUpTransition = usePopUpTransition()
const formTransition = useFormTransition()

const viewingCustomersList = ref(null)
const viewingAddForm = ref(null)
const viewingUpdateForm = ref(null)
const pageContent = ref(null)

const searchValue = ref("")
const dataStatus = ref(null)
const loading = ref(false)
const customersListData = ref([])
const currentCustomerId = ref(null)

const filteredCustomersListData = computed(() => {
    return customersListData.value.filter(customer => {
        return customer.name.toLowerCase().includes(searchValue.value.toLowerCase()) ||
               customer.phone.toLowerCase().includes(searchValue.value.toLowerCase())
    })
})

const addFormData = ref({
    name: '',
    phone: '',
    email: '',
    address: ''
})

const updateFormData = ref({
    id: '',
    name: '',
    phone: '',
    email: '',
    address: ''
})

const errors = ref({
    name: [],
    phone: [],
    email: [],
    address: []
})

// FUNCTIONS API
function getCustomersListData() {
  dataStatus.value = "Loading..."

  axiosClient.get('/api/customers-list')
    .then(response => {
        console.log(response);
        
        customersListData.value = response.data.customers

        if(customersListData.value.length < 1){
            dataStatus.value = "No data"
        }else{
            dataStatus.value = null
        }
    })
    .catch(error => {
        console.log(error.response);
        
        if(customersListData.value.length < 1){
            dataStatus.value = "No data"
        }else{
            dataStatus.value = null
        }
    })
}

function submitAddForm(){
    loading.value = true

    axiosClient.post('/api/add-customer', addFormData.value)
        .then(response => {
            loading.value = false
            showSuccessMsg("Success", response.data.message)
            getCustomersListData()
            showCustomersList()
        })
        .catch(error => {
            console.log(error);

            loading.value = false
            showErrorMsg(error.response.data.message)
            errors.value = error.response.data.errors
        })
}

function submitUpdateForm(customerId){
    loading.value = true

    axiosClient.put(`/api/update-customer/${customerId}`, updateFormData.value)
        .then(response => {
            loading.value = false
            showSuccessMsg("Success", response.data.message)      
            getCustomersListData()
            showCustomersList()
        })
        .catch(error => {
            console.log(error);
            
            loading.value = false
            showErrorMsg(error.response.data.message)
            errors.value = error.response.data.errors
        })
}

function deleteCustomer(){
    loading.value = true

    axiosClient.delete(`/api/delete-customer/${confirmId.value}`)
        .then(response => {
            loading.value = false
            viewingConfirmModal.value = false
            showSuccessMsg("Success", response.data.message)
            getCustomersListData()
            console.log(response);
        })
        .catch(error => {
            loading.value = false
            showErrorMsg(error.response.data.message)
            console.log(error)
        })
  
}

// OTHER FUNCTIONS
function changeContent(content){
    if(content === 'Customers List'){
        viewingCustomersList.value = true
    }else if(content === 'Add Customer'){
        viewingAddForm.value = true
    }else if(content === 'Update Customer'){
        viewingUpdateForm.value = true
    }else{
        return
    }
}

function showCustomersList () {
    viewingAddForm.value = false
    viewingUpdateForm.value = false

    clearInputsErrors()
}

function showAddForm () {
    viewingCustomersList.value = false
    pageContent.value = "Add Customer"

}

function showUpdateForm(customer){
    viewingCustomersList.value = false
    pageContent.value = "Update Customer"

    //to bind selected user
    updateFormData.value.id = customer.id
    updateFormData.value.name = customer.name
    updateFormData.value.phone = customer.phone
    updateFormData.value.email = customer.email
    updateFormData.value.address = customer.address

}

function clearInputsErrors(){
    errors.value.name = []
    errors.value.phone = []
    errors.value.email = []
    errors.value.address = []

    addFormData.value.name = ''
    addFormData.value.phone = ''
    addFormData.value.email = ''
    addFormData.value.address = ''

    updateFormData.value.id = ''
    updateFormData.value.name = ''
    updateFormData.value.phone = ''
    updateFormData.value.email = ''
    updateFormData.value.address = ''
}

function toggleMoreActions (id){
  if (currentCustomerId.value == id){
    currentCustomerId.value = null
  }else{
    currentCustomerId.value = id
  } 
}

function handleClickOutside(e) {
  // close if clicking outside the dropdown
  if (!e.target.closest(".btnActions")) {
    currentCustomerId.value = null
  }
}

onMounted(() => {
    viewingCustomersList.value = true
    viewingAddForm.value = false
    viewingUpdateForm.value = false

    // GET CUSTOMERS LIST
    getCustomersListData()

    document.addEventListener("click", handleClickOutside)
})

onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside)
})
</script>

<style>

</style>