<template>
    <div class="relative h-full w-full min-h-fit min-w-[200px]">
     
        <!-- Suppliers list -->
        <transition
            :enter-from-class="mainContentTransition.enterFrom "
            :enter-to-class="mainContentTransition.enterTo"
            :enter-active-class="mainContentTransition.enterActive"
            :leave-from-class="mainContentTransition.leaveFrom"
            :leave-to-class="mainContentTransition.leaveTo"
            :leave-active-class="mainContentTransition.leaveActive"
            @after-leave="changeContent(pageContent)"
        >
            <section v-show="viewingSuppliersList" class="h-full flex flex-col max-h-[1200px] min-h-[1100px] lg:min-h-[500px] w-full max-w-[1300px] mx-auto">

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
                    <div class="flex flex-col flex-1 overflow-hidden min-h-[350px] bg-white rounded-xl shadow-[0px_1px_4px_rgba(0,0,0,0.1)] p-6">

                        <!-- Search bar -->
                        <div class="flex mb-4">
                            <input 
                                v-model="searchValue" 
                                type="text" 
                                placeholder="Search by name or phone" 
                                class="search-bar"
                            >
                        </div>

                        <div class="flex flex-col flex-1 pb-2 overflow-hidden">
                            
                            <!-- Desktop table layout -->
                            <div class="relative flex-1 hidden overflow-hidden lg:flex">

                                <!-- Gradient overlay -->
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>

                                <div class="flex-1 px-1 overflow-auto">
                                    <div class="head-row grid grid-cols-[25%_20%_20%_25%_10%]">
                                        <div class="head-data">Name</div>
                                        <div class="head-data">Phone</div>
                                        <div class="head-data">Contact Person</div>
                                        <div class="head-data">Address</div>
                                        <div class="justify-center head-data">Action</div>
                                    </div>

                                    <div v-if="dataStatus" class="flex items-center justify-center w-full py-3">
                                        <p>{{ dataStatus }}</p>
                                    </div>

                                    <div v-if="!dataStatus" v-for="supplier in filteredSuppliersListData" class="body-row grid-cols-[25%_20%_20%_25%_10%]">
                                        <div class="grid body-data">
                                            <p class="style-wide-supplier-name" :title="supplier.name">
                                                {{ supplier.name }}
                                            </p>
                                            <p class="style-wide-supplier-email" :title="supplier.email">
                                                {{ supplier.email ?? 'no email' }}
                                            </p>
                                        </div>
                                        <div class="body-data style-wide-supplier-phone" :title="supplier.phone">
                                            {{ supplier.phone }}
                                        </div>
                                        <div class="body-data style-wide-supplier-contact" :title="supplier.contactPerson">
                                            {{ supplier.contactPerson }}
                                        </div>
                                        <div class="body-data style-wide-supplier-address" :title="supplier.address">
                                            {{ supplier.address }}
                                        </div>
                                        <div class="flex justify-center items-center body-data">
                                            <button 
                                                @click="toggleMoreActions(supplier.id)" 
                                                class="px-4 py-1 rounded-md btnActions hover:bg-gray-300"
                                                type="button"
                                            >
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
                                                        @click="showUpdateForm(supplier)" 
                                                        class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-darkgray-sec"
                                                        type="button"
                                                    >
                                                        Update
                                                    </button>

                                                    <button 
                                                        @click="openConfirmModal('Delete Supplier', 'Are you sure you want to delete this supplier? This supplier will no longer be usable, but existing transactions will remain unchanged.', supplier.id)"
                                                        class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-red-700 "
                                                        type="button" 
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

                            <!-- Mobile card layout -->
                            <div class="relative flex flex-1 overflow-hidden lg:hidden">

                                <!-- Gradient overlays -->
                                <div class="absolute top-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-b from-white to-transparent"></div>
                                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
                    
                                <div class="flex flex-1 overflow-auto">

                                    <div v-if="dataStatus" class="flex items-center justify-center w-full py-3">
                                        <p>{{ dataStatus }}</p>
                                    </div>

                                    <!-- Card grid -->
                                    <div class="my-2 content-start flex-1 gap-3 p-1 overflow-hidden min-h-fit grid grid-cols-[repeat(auto-fill,minmax(250px,1fr))]">
                                    
                                        <!-- Card items -->
                                        <div v-if="!dataStatus" v-for="supplier in filteredSuppliersListData" class="cursor-default relative flex flex-col rounded-lg shadow-[0px_1px_4px_rgba(0,0,0,0.1)] max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all">
                                        
                                            <!-- Card head -->
                                            <div class="flex items-center pt-5 pb-3 mx-5 mb-2 border-b border-gray-200">
                                                <h5 class="style-narrow-supplier-name" :title="supplier.name">
                                                    {{ supplier.name }}
                                                </h5>
                                                    
                                                <button 
                                                    @click="toggleMoreActions(supplier.id)" 
                                                    class="px-4 py-1 ml-auto rounded-md btnActions hover:bg-gray-300"
                                                    type="button"
                                                >
                                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                                </button>
                                            </div>

                                            <!-- Card body -->
                                            <div class="grid gap-4 px-5 pt-2 pb-8">
                                                
                                                <div class="flex flex-col flex-wrap h-fit overflow-hidden">
                                                    <p class="label-style-two mb-1">Phone</p>
                                                    <p class="style-narrow-supplier-phone" :title="supplier.phone">
                                                        {{ supplier.phone }}
                                                    </p>
                                                </div>

                                                <div class="flex flex-col flex-wrap h-fit overflow-hidden">
                                                    <p class="label-style-two mb-1">Email</p>
                                                    <p class="style-narrow-supplier-email" :title="supplier.email">
                                                        {{ supplier.email ?? 'not set' }}
                                                    </p>
                                                </div>
                                                
                                                <div class="flex flex-col flex-wrap h-fit overflow-hidden">
                                                    <p class="label-style-two mb-1">Contact Person</p>
                                                    <p class="style-narrow-supplier-contact" :title="supplier.contactPerson">
                                                        {{ supplier.contactPerson ?? 'not set' }}
                                                    </p>
                                                </div>

                                                <div class="flex flex-col flex-wrap h-fit overflow-hidden">
                                                    <p class="label-style-two mb-1">Address</p>
                                                    <p class="style-narrow-supplier-address" :title="supplier.address">
                                                        {{ supplier.address ?? 'not set' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- More actions -->
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
                                                            @click="showUpdateForm(supplier)" 
                                                            class="w-full px-4 py-2 bg-white rounded-lg hover:shadow-[0_0_6px] hover:shadow-white/70" 
                                                            type="button"
                                                        >
                                                            Update
                                                        </button>

                                                        <button 
                                                            @click="openConfirmModal('Delete Supplier', 'Are you sure you want to delete this supplier? This supplier will no longer be usable, but existing transactions will remain unchanged.', supplier.id)"
                                                            class="w-full px-4 py-2 bg-white text-red-700 rounded-lg hover:shadow-[0_0_6px] hover:shadow-red-500/90"
                                                            type="button" 
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
            @after-leave="changeContent('Suppliers List')"
        >
            <section v-show="viewingAddForm" class="w-full ">
                <form @submit.prevent="submitAddForm" class="w-full max-w-xl p-6 sm:p-8 mx-auto mb-4 bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl">
                    <h3 class=" font-bold text-left">New Supplier</h3>
                    <p class="mb-5 text-sm text-gray-500">Fill in the details to add a new supplier</p>

                    <hr class="mb-5">

                    <div class="mb-5 flex items-center gap-2 text-lg">
                        <i class="fas fa-building"></i>
                        <span class="mr-2 font-medium">Supplier Details</span>
                    </div>

                    <TextInput
                        v-model="addFormData.name" 
                        id="add-name" 
                        label="Name:"
                        :message="errors.name ? errors.name[0] : ''"
                        type="text"
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                        <TextInput
                            v-model="addFormData.phone" 
                            id="add-phone" 
                            label="Phone:"
                            :message="errors.phone ? errors.phone[0] : ''"
                            type="text"
                        />

                        <TextInput
                            v-model="addFormData.contactPerson" 
                            id="add-contact" 
                            label="Contact Person:"
                            :message="errors.contactPerson ? errors.contactPerson[0] : ''"
                            type="text"
                        />
                    </div>

                    <TextInput
                        v-model="addFormData.email" 
                        id="add-email" 
                        label="Email:"
                        :message="errors.email ? errors.email[0] : ''"
                        type="email"
                    />

                    <TextArea
                        v-model="addFormData.address" 
                        id="add-address" 
                        label="Address:"
                        :message="errors.address ? errors.address[0] : ''"
                    />

                    <hr class="mb-6 mt-1">
                    
                    <div class="flex items-end justify-end w-full">
                        <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
                            <ButtonYellow type="submit" width="full">
                                Save
                            </ButtonYellow>

                            <ButtonWhite @click="showSuppliersList" type="button" width="full">
                                Cancel
                            </ButtonWhite>
                        </div>
                    </div>
                </form>
            </section>
        </transition>

        <!-- Update form -->
        <transition
            :enter-from-class="formTransition.enterFrom"
            :enter-to-class="formTransition.enterTo"
            :enter-active-class="formTransition.enterActive"
            :leave-from-class="formTransition.leaveFrom"
            :leave-to-class="formTransition.leaveTo"
            :leave-active-class="formTransition.leaveActive"
            @after-leave="changeContent('Suppliers List')"
        >
            <section v-show="viewingUpdateForm" class="w-full ">
                <form @submit.prevent="submitUpdateForm(updateFormData.id)" class="w-full max-w-xl p-6 sm:p-8 mx-auto mb-4 bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl">
                    <h3 class=" font-bold text-left">Update Supplier</h3>
                    <p class="mb-5 text-sm text-gray-500">Update details of an existing supplier</p>

                    <hr class="mb-5">

                    <div class="mb-5 flex items-center gap-2 text-lg">
                        <i class="fas fa-building"></i>
                        <span class="mr-2 font-medium">Supplier Details</span>
                    </div>

                    <TextInput
                        v-model="updateFormData.name" 
                        id="update-name" 
                        label="Name:"
                        :message="errors.name ? errors.name[0] : ''"
                        type="text"
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-x-4">
                        <TextInput
                            v-model="updateFormData.phone" 
                            id="update-phone" 
                            label="Phone:"
                            :message="errors.phone ? errors.phone[0] : ''"
                            type="text"
                        />

                        <TextInput
                            v-model="updateFormData.contactPerson" 
                            id="update-contact" 
                            label="Contact Person:"
                            :message="errors.contactPerson ? errors.contactPerson[0] : ''"
                            type="text"
                        />
                    </div>

                    <TextInput
                        v-model="updateFormData.email" 
                        id="update-email" 
                        label="Email:"
                        :message="errors.email ? errors.email[0] : ''"
                        type="email"
                    />
                    
                    <TextArea
                        v-model="updateFormData.address" 
                        id="update-address" 
                        label="Address:"
                        :message="errors.address ? errors.address[0] : ''"
                    />

                    <hr class="mb-6 mt-1">

                    <div class="flex items-end justify-end w-full">
                        <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
                            <ButtonYellow type="submit" width="full">
                                Save
                            </ButtonYellow>
                            
                            <ButtonWhite @click="showSuppliersList" type="button" width="full">
                                Cancel
                            </ButtonWhite>
                        </div>
                    </div>
                </form>
            </section>
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

        <ConfirmModal 
            :inUse="viewingConfirmModal" 
            @cancel="viewingConfirmModal = !viewingConfirmModal"
            @yes="deleteSupplier"
        >
            <template v-slot:title>
                {{ confirmTitle }}
            </template>

            <template v-slot:body>
                {{ confirmBody }}
            </template>
        </ConfirmModal>
    </teleport>
</template>

<script setup>
import { useAlertLoadingTransition, useMainContentTransition, useFormTransition } from '../composables/useTransition';
import { onMounted, ref, computed, onBeforeUnmount } from 'vue';
import { useAlertMessages } from '../composables/useAlertMessages';
import { useConfirmModal } from '../composables/useConfirmModal';
import axiosClient from '../axios';

import Loading from '../components/alerts/Loading.vue';
import SuccessBanner from '../components/alerts/SuccessBanner.vue';
import ErrorBanner from '../components/alerts/ErrorBanner.vue';
import ButtonYellow from '../components/buttons/ButtonYellow.vue';
import ConfirmModal from '../components/ConfirmModal.vue';
import ButtonWhite from '../components/buttons/ButtonWhite.vue';
import TextInput from '../components/TextInput.vue';
import TextArea from '../components/TextArea.vue';

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

async function getSuppliersListData() {
  dataStatus.value = "Loading..."

  try {
    const response = await axiosClient.get('/api/suppliers-list')

    suppliersListData.value = response.data.suppliers
  } 
  catch (error) {
    showErrorMsg(error.response?.data?.message || 'Failed to load suppliers list data')
  }
  finally{
    if(suppliersListData.value.length < 1){
        dataStatus.value = "No data"
    }else{
        dataStatus.value = null
    }
  }
}

async function submitAddForm(){
    loading.value = true

    try {
        const response = await axiosClient.post('/api/add-supplier', addFormData.value)

        showSuccessMsg("Success", response.data.message)
        await getSuppliersListData()
        showSuppliersList()
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to add supplier')
        errors.value = error.response?.data?.errors || {}
    }
    finally{
        loading.value = false
    }
}

async function submitUpdateForm(supplierId){
    loading.value = true

    try {
        const response = await axiosClient.put(`/api/update-supplier/${supplierId}`, updateFormData.value)

        showSuccessMsg("Success", response.data.message)      
        await getSuppliersListData()
        showSuppliersList()
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to update supplier')
        errors.value = error.response?.data?.errors || {}
    }
    finally{
        loading.value = false
    }
}

async function deleteSupplier(){
    loading.value = true

    try {
        const response = await axiosClient.delete(`/api/delete-supplier/${confirmId.value}`)

        viewingConfirmModal.value = false
        showSuccessMsg("Success", response.data.message)
        await getSuppliersListData()
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to delete supplier')
    }
    finally{
        loading.value = false
    }
}

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

    mainContentTransition.value.enterFrom = "-translate-x-1/2 opacity-0"
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
    if (!e.target.closest(".btnActions")) {
        currentSupplierId.value = null
    }
}

onMounted(() => {
    viewingSuppliersList.value = true
    viewingAddForm.value = false
    viewingUpdateForm.value = false
    getSuppliersListData()

    document.addEventListener("click", handleClickOutside)
})

onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside)
})
</script>

<style>

</style>