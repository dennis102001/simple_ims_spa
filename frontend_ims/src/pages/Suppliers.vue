<template>
    <div class="relative h-full w-full min-h-fit min-w-[200px]">
     
        <!-- SUPPLIERS LIST -->
        <transition
        :enter-from-class="mainContentTransition.enterFrom "
        :enter-to-class="mainContentTransition.enterTo"
        :enter-active-class="mainContentTransition.enterActive"
        :leave-from-class="mainContentTransition.leaveFrom"
        :leave-to-class="mainContentTransition.leaveTo"
        :leave-active-class="mainContentTransition.leaveActive"
        @after-leave="changeContent(pageContent)"
        >
            <div v-show="viewingSuppliersList" class="h-full flex flex-col max-h-[1200px] min-h-[700px] w-full max-w-[1300px] mx-auto">

                <div class="flex flex-row items-start justify-between px-2 mt-2 mb-4">
                    <h3 class="font-semibold tracking-wider">Suppliers List</h3>
                    <ButtonYellow @click="showAddForm" type="button">
                        <div class="relative flex flex-row items-center w-fit ">
                            <span class="text-sm px-2">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                            <span class="pr-4">New Supplier</span>
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
                                    <div class="head-row grid grid-cols-[25%_20%_20%_25%_10%]">
                                        <div class="head-data">Name</div>
                                        <div class="head-data">Phone</div>
                                        <div class="head-data">Contact Person</div>
                                        <div class="head-data">Address</div>
                                        <div class="justify-center head-data">Action</div>
                                    </div>
                                    <!-- body -->
                                    <div v-if="dataStatus" class="flex items-center justify-center w-full py-3">
                                        <p>{{ dataStatus }}</p>
                                    </div>
                                    <div v-if="!dataStatus" v-for="supplier in filteredSuppliersListData" class="body-row grid-cols-[25%_20%_20%_25%_10%]">
                                        <div class="grid body-data">
                                            <p class="style-wide-supplier-name" :title="supplier.name">{{ supplier.name }}</p>
                                            <p class="style-wide-supplier-email" :title="supplier.email">{{ supplier.email ?? 'no email' }}</p>
                                        </div>
                                        <div class="body-data style-wide-supplier-phone" :title="supplier.phone">{{ supplier.phone }}</div>
                                        <div class="body-data style-wide-supplier-contact" :title="supplier.contactPerson">{{ supplier.contactPerson }}</div>
                                        <div class="body-data style-wide-supplier-address" :title="supplier.address">{{ supplier.address }}</div>
                                        <div class="flex justify-center items-center body-data">
                                            <button @click="toggleMoreActions(supplier.id)" class="px-4 py-1 rounded-md btnActions hover:bg-gray-300">
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
                                            <div v-if="currentSupplierId == supplier.id" class="absolute inset-0 flex items-center justify-end w-full h-full ">
                                                <div class="bg-gradient-to-r flex-1 h-full from-[rgba(0,0,0,0.01)] to-darkgray-pri "></div>
                                                <div class="flex items-center justify-end h-full p-2 bg-darkgray-pri w-fit btnActions">
                                                    <button 
                                                        type="button"
                                                        @click="showUpdateForm(supplier)" 
                                                        class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-darkgray-sec"
                                                    >
                                                        Update
                                                    </button>
                                                    <button 
                                                        type="button" 
                                                        @click="openConfirmModal('Delete Supplier', 'Are you sure you want to delete this supplier?', supplier.id)"
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
                                        <div v-if="!dataStatus" v-for="supplier in filteredSuppliersListData" class="cursor-default relative flex flex-col rounded-lg shadow-emerald-300 shadow-[0_2px_4px] max-h-full">
                                        
                                            <!-- card head -->
                                            <div class="flex items-center pt-5 pb-3 mx-5 mb-2 border-b border-gray-200">
                                                <h5 class="style-narrow-supplier-name" :title="supplier.name">{{ supplier.name }}</h5>
                                                    
                                                <button 
                                                    type="button"
                                                    @click="toggleMoreActions(supplier.id)" 
                                                    class="px-4 py-1 ml-auto rounded-md btnActions hover:bg-gray-300"
                                                >
                                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                                </button>
                                            </div>

                                            <!-- card body -->
                                            <div class="grid gap-4 px-5 pt-2 pb-8">
                                                
                                                <!-- Phone -->
                                                <div class="flex flex-col flex-wrap h-fit overflow-hidden">
                                                    <p class="label-style-two mb-1">Phone</p>
                                                    <p class="style-narrow-supplier-phone" :title="supplier.phone">{{ supplier.phone }}</p>
                                                </div>

                                                <!-- Email -->
                                                <div class="flex flex-col flex-wrap h-fit overflow-hidden">
                                                    <p class="label-style-two mb-1">Email</p>
                                                    <p class="style-narrow-supplier-email" :title="supplier.email">{{ supplier.email ?? 'not set' }}</p>
                                                </div>
                                                
                                                <!-- Contact Person -->
                                                <div class="flex flex-col flex-wrap h-fit overflow-hidden">
                                                    <p class="label-style-two mb-1">Contact Person</p>
                                                    <p class="style-narrow-supplier-contact" :title="supplier.contactPerson">{{ supplier.contactPerson ?? 'not set' }}</p>
                                                </div>

                                                <!-- Address -->
                                                <div class="flex flex-col flex-wrap h-fit overflow-hidden">
                                                    <p class="label-style-two mb-1">Address</p>
                                                    <p class="style-narrow-supplier-address" :title="supplier.address">{{ supplier.address ?? 'not set' }}</p>
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
                                                <div v-if="currentSupplierId == supplier.id" class="absolute flex flex-col items-center justify-center w-full h-full gap-1 rounded-lg bg-black/60">
                                                    <div class="grid w-3/5 grid-cols-1 gap-1 btnActions min-w-[150px]">
                                                        <button 
                                                            type="button"
                                                            @click="showUpdateForm(supplier)" 
                                                            class="w-full px-4 py-2 bg-white rounded-lg hover:shadow-[0_0_6px] hover:shadow-white/70" 
                                                        >
                                                            Edit
                                                        </button>
                                                        <button 
                                                            type="button" 
                                                            @click="openConfirmModal('Delete Supplier', 'Are you sure you want to delete this supplier?', supplier.id)"
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
        @after-leave="changeContent('Suppliers List')"
        >
            <div v-show="viewingAddForm" class="w-full ">

                <form @submit.prevent="submitAddForm" class="w-full max-w-sm p-6 mx-auto mb-4 bg-white shadow-[0px_2px_4px_rgba(0,0,0,0.1)] rounded-xl">
                    <h2 class="mb-10 text-2xl font-bold text-center">New Supplier</h2>

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
                    <div class="flex flex-col w-full h-[76px]">
                        <label for="" class="label-style-one">Contact Person:</label>
                        <input v-model="addFormData.contactPerson" type="text" class="value-style">
                        <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.contactPerson ? errors.contactPerson[0] : '' }}</p>
                    </div>
                    <div class="flex flex-col w-full h-[76px]">
                        <label for="" class="label-style-one">Email:</label>
                        <input v-model="addFormData.email" type="text" class="value-style">
                        <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.email ? errors.email[0] : '' }}</p>
                    </div>
                    <div class="flex flex-col w-full h-[76px] mb-4">
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
                            <ButtonWhite @click="showSuppliersList" type="button" width="full">
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
        @after-leave="changeContent('Suppliers List')"
        >
            <div v-show="viewingUpdateForm" class="w-full ">

                <form @submit.prevent="submitUpdateForm(updateFormData.id)" class="w-full max-w-sm p-6 mx-auto mb-4 bg-white shadow-[0px_2px_4px_rgba(0,0,0,0.1)] rounded-xl">
                    <h2 class="mb-10 text-2xl font-bold text-center">Update Supplier</h2>

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
                        <label for="" class="label-style-one">Contact Person:</label>
                        <input v-model="updateFormData.contactPerson" type="text" class="value-style">
                        <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.contactPerson ? errors.contactPerson[0] : '' }}</p>
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
                            <ButtonWhite @click="showSuppliersList" type="button" width="full">
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

    <!-- LOADING SUCCESS ERROR CONFIRM -->
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
        @yes="deleteSupplier"
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

const viewingSuppliersList = ref(null)
const viewingAddForm = ref(null)
const viewingUpdateForm = ref(null)
const pageContent = ref(null)

const searchValue = ref("")
const dataStatus = ref(null)
const loading = ref(false)
const suppliersListData = ref([])
const currentSupplierId = ref(null)

const filteredSuppliersListData = computed(() => {
  return suppliersListData.value.filter(supplier => {
    return supplier.name.toLowerCase().includes(searchValue.value.toLowerCase()) ||
           supplier.phone.toLowerCase().includes(searchValue.value.toLowerCase())
  })
})

const addFormData = ref({
    name: '',
    contactPerson: '',
    phone: '',
    email: '',
    address: ''
})

const updateFormData = ref({
    id: '',
    name: '',
    contactPerson: '',
    phone: '',
    email: '',
    address: ''
})

const errors = ref({
    name: [],
    contactPerson: [],
    phone: [],
    email: [],
    address: []
})

// FUNCTIONS API
function getSuppliersListData() {
  dataStatus.value = "Loading..."

  axiosClient.get('/api/suppliers-list')
    .then(response => {
        console.log(response);
        
        suppliersListData.value = response.data.suppliers

        if(suppliersListData.value.length < 1){
            dataStatus.value = "No data"
        }else{
            dataStatus.value = null
        }
    })
    .catch(error => {
        console.log(error.response);
        
        if(suppliersListData.value.length < 1){
            dataStatus.value = "No data"
        }else{
            dataStatus.value = null
        }
    })
}

function submitAddForm(){
    loading.value = true

    axiosClient.post('/api/add-supplier', addFormData.value)
        .then(response => {
            loading.value = false
            showSuccessMsg("Success", response.data.message)
            getSuppliersListData()
            showSuppliersList()
        })
        .catch(error => {
            console.log(error);

            loading.value = false
            showErrorMsg(error.response.data.message)
            errors.value = error.response.data.errors
        })
}

function submitUpdateForm(supplierId){
    loading.value = true

    axiosClient.put(`/api/update-supplier/${supplierId}`, updateFormData.value)
        .then(response => {
            loading.value = false
            showSuccessMsg("Success", response.data.message)      
            getSuppliersListData()
            showSuppliersList()
        })
        .catch(error => {
            console.log(error);
            
            loading.value = false
            showErrorMsg(error.response.data.message)
            errors.value = error.response.data.errors
        })
}

function deleteSupplier(){
  
    loading.value = true

    axiosClient.delete(`/api/delete-supplier/${confirmId.value}`)
        .then(response => {
            loading.value = false
            viewingConfirmModal.value = false
            showSuccessMsg("Success", response.data.message)
            getSuppliersListData()
            console.log(response);
        })
        .catch(error => {
            loading.value = false
            showErrorMsg(error.response.data.message)
            console.log(error)
        })

}

//OTHER FUNCTIONS
function changeContent(content){
    if(content === 'Suppliers List'){
        viewingSuppliersList.value = true
    }else if(content === 'Add Supplier'){
        viewingAddForm.value = true
    }else if(content === 'Update Supplier'){
        viewingUpdateForm.value = true
    }else{
        return
    }
}

function showSuppliersList () {
    viewingAddForm.value = false
    viewingUpdateForm.value = false

    clearInputsErrors()
}

function showAddForm () {
    viewingSuppliersList.value = false
    pageContent.value = "Add Supplier"
}

function showUpdateForm(supplier){
    viewingSuppliersList.value = false
    pageContent.value = "Update Supplier"

    //to bind selected user
    updateFormData.value.id = supplier.id
    updateFormData.value.name = supplier.name
    updateFormData.value.contactPerson = supplier.contactPerson
    updateFormData.value.phone = supplier.phone
    updateFormData.value.email = supplier.email
    updateFormData.value.address = supplier.address
}

function clearInputsErrors(){
    errors.value.name = []
    errors.value.contactPerson = []
    errors.value.phone = []
    errors.value.email = []
    errors.value.address = []

    addFormData.value.name = ''
    addFormData.value.contactPerson = ''
    addFormData.value.phone = ''
    addFormData.value.email = ''
    addFormData.value.address = ''

    updateFormData.value.id = ''
    updateFormData.value.name = ''
    updateFormData.value.contactPerson = ''
    updateFormData.value.phone = ''
    updateFormData.value.email = ''
    updateFormData.value.address = ''
}

function toggleMoreActions (id){
  if (currentSupplierId.value == id){
    currentSupplierId.value = null
  }else{
    currentSupplierId.value = id
  } 
}

function handleClickOutside(e) {
  // close if clicking outside the dropdown
  if (!e.target.closest(".btnActions")) {
    currentSupplierId.value = null
  }
}

onMounted(() => {
    viewingSuppliersList.value = true
    viewingAddForm.value = false
    viewingUpdateForm.value = false

    // GET SUPPLIERS LIST
    getSuppliersListData()

    document.addEventListener("click", handleClickOutside)
})

onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside)
})
</script>

<style>

</style>