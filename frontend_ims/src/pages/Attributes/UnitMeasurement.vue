<template>
    <transition
        :enter-from-class="mainContentTransition.enterFrom"
        :enter-to-class="mainContentTransition.enterTo"
        :enter-active-class="mainContentTransition.enterActive"
        :leave-from-class="mainContentTransition.leaveFrom"
        :leave-to-class="mainContentTransition.leaveTo"
        :leave-active-class="mainContentTransition.leaveActive"
    >
        <div v-if="viewingUnitsList" class="flex flex-col flex-1 h-full min-w-[100px] p-6 bg-white rounded-xl shadow-[0px_2px_4px_rgba(0,0,0,0.1)]">
            
            <div class="flex flex-row flex-wrap mb-4">
                <div class="flex flex-col items-start justify-center flex-1">
                    <h5 class="font-semibold tracking-wider">Unit Measurements List</h5>
                </div>

                <ButtonYellow @click="showAddUnitModal" type="button">
                    <div class="relative flex flex-row items-center w-fit ">
                        <span class="text-sm px-2">
                            <i class="fa-solid fa-plus"></i>
                        </span>
                        <span class="pr-2">New Measurement</span>
                    </div>
                </ButtonYellow>
            </div>
            
            <div class="flex flex-wrap-reverse items-end justify-end gap-2 mb-2">
                <input v-model="searchValue" type="text" placeholder="Search Unit" class="search-bar">
            </div>
                
            <div class="relative flex flex-1 overflow-hidden max-h-[500px] lg:max-h-full">
                <!-- gradient overlays -->
                <div class="absolute top-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-b from-white to-transparent"></div>
                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
    
                <div class="flex flex-1 overflow-auto">
                    <div v-if="dataStatus" class="flex items-center justify-center w-full py-3">
                        <p>{{ dataStatus }}</p>
                    </div>

                    <!-- grid - margin 2 for gradient overlays -->
                    <div class="my-2 content-start flex-1 gap-4 p-1 overflow-hidden min-h-fit grid grid-cols-[repeat(auto-fill,minmax(250px,1fr))]">
                    
                        <!-- grid items -->
                        <div v-if="!dataStatus" v-for="unit in filteredUnitsListData" class="relative flex flex-col rounded-lg shadow-emerald-300 shadow-[0_2px_4px] max-h-full">
                        
                            <!-- card header -->
                            <div class="flex items-start pt-5 pb-2 mx-5 overflow-hidden">
                                <h5 class="font-bold truncate" :title="unit.unitName">{{ unit.unitName }}</h5>
                                <button 
                                    type="button"
                                    @click="toggleMoreActions(unit.id)"
                                    class="px-4 py-1 ml-auto rounded-md btnActions hover:bg-gray-300"
                                >
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                            </div>

                            <!-- card body -->
                            <div class="grid gap-2 px-5 pb-5">

                                <!-- Description -->
                                <div class="flex h-fit overflow-hidden">
                                    <p class="text-sm w-full py-1 line-clamp-2" :title="unit.description">{{ unit.description ?? 'No description' }}</p>
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
                            <div v-if="currentUnitId == unit.id" class="absolute flex flex-col items-center justify-center w-full h-full gap-1 rounded-lg bg-black/60">
                                <div class="grid w-3/5 grid-cols-1 gap-1 btnActions min-w-[150px]">
                                    <button 
                                        type="button"
                                        @click="showUpdateUnitModal(unit)" 
                                        class="w-full px-4 py-2 bg-white rounded-lg hover:shadow-[0_0_6px] hover:shadow-white/70"
                                    >
                                        Update
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="openConfirmModal('Delete Unit Measurement', 'Are you sure you want to delete this unit measurement?', unit.id)"
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

        </div>
    </transition>

    <!-- ADD FORM -->
    <Modal :inUse="viewingAddUnitModal" @close="closeAddUpModal">
        <form @submit.prevent="submitAddForm" class="w-full max-w-sm mb-4 bg-white shadow-[0px_2px_4px_rgba(0,0,0,0.1)] rounded-xl">
            <div class="px-6 pt-4 pb-2 rounded-t-xl border-b mb-4 flex flex-row justify-between items-center">
                <h4 class="font-semibold tracking-wide mb-1">New Unit Measurement</h4>
                <div @click="closeAddUpModal" class="ml-auto hover:bg-gray-50 rounded-full size-10 flex items-center justify-center cursor-pointer" title="close">
                    <i class="fa-solid fa-xmark"></i>
                </div>
            </div>

            <div class="flex flex-col w-full h-[76px] px-6">
                <label for="unitName" class="label-style-one">Unit Name:</label>
                <input v-model="addFormData.unitName" id="unitName" type="text" class="value-style">
                <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.unitName ? errors.unitName[0] : '' }}</p>
            </div>
            <div class="flex flex-col w-full h-[76px] px-6 mb-4">
                <label for="description" class="label-style-one">Description:</label>
                <input v-model="addFormData.description" id="description" type="text" class="value-style">
                <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.description ? errors.description[0] : '' }}</p>
            </div>
            <div class="flex flex-row justify-between w-full space-x-4 px-6 pt-4 pb-6 border-t rounded-b-xl">
                <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
                    <ButtonYellow type="submit"  width="full">
                        <div class="relative flex flex-row items-center w-full">
                            <span class="mx-auto">Save</span>
                        </div>
                    </ButtonYellow>
                    <ButtonWhite @click="closeAddUpModal" type="button" width="full">
                        <div class="relative flex flex-row items-center w-full">
                            <span class="mx-auto">Cancel</span>
                        </div>
                    </ButtonWhite>
                </div>
            </div>
        </form>
    </Modal>

    <!-- UPDATE FORM -->
    <Modal :inUse="viewingUpdateUnitModal" @close="closeAddUpModal">
        <form @submit.prevent="submitUpdateForm(updateFormData.id)" class="w-full max-w-sm mb-4 bg-white shadow-[0px_2px_4px_rgba(0,0,0,0.1)] rounded-xl">
            <div class="px-6 pt-4 pb-2 rounded-t-xl border-b mb-4 flex flex-row justify-between items-center">
                <h4 class="font-semibold tracking-wide mb-1">Update Unit Measurement</h4>
                <div @click="closeAddUpModal" class="ml-auto hover:bg-gray-50 rounded-full size-10 flex items-center justify-center cursor-pointer" title="close">
                    <i class="fa-solid fa-xmark"></i>
                </div>
            </div>

            <div class="flex flex-col w-full h-[76px] px-6">
                <label for="updateUnitName" class="label-style-one">Unit Name:</label>
                <input v-model="updateFormData.unitName" id="updateUnitName" type="text" class="value-style">
                <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.unitName ? errors.unitName[0] : '' }}</p>
            </div>
            <div class="flex flex-col w-full h-[76px] px-6 mb-4">
                <label for="description" class="label-style-one">Description:</label>
                <input v-model="updateFormData.description" id="description" type="text" class="value-style">
                <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.description ? errors.description[0] : '' }}</p>
            </div>
            <div class="flex flex-row justify-between w-full space-x-4 px-6 pt-4 pb-6 border-t rounded-b-xl">
                <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
                    <ButtonYellow type="submit" width="full">
                        <div class="relative flex flex-row items-center w-full">
                            <span class="mx-auto">Save</span>
                        </div>
                    </ButtonYellow>
                    <ButtonWhite @click="closeAddUpModal" type="button" width="full">
                        <div class="relative flex flex-row items-center w-full">
                            <span class="mx-auto">Cancel</span>
                        </div>
                    </ButtonWhite>
                </div>
            </div>
        </form>
    </Modal>

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
        @yes="deleteUnit"
        >
            <template v-slot:title>{{ confirmTitle }}</template>
            <template v-slot:body>{{ confirmBody }}</template>
        </ConfirmModal>
    </teleport>
</template>

<script setup>
import { useMainContentTransition, usePopUpTransition, useAlertLoadingTransition } from '../../composables/useTransition';
import { onMounted, onBeforeUnmount, ref, computed } from 'vue';
import { useAlertMessages } from '../../composables/useAlertMessages';
import { useConfirmModal } from '../../composables/useConfirmModal';
import axiosClient from '../../axios.js'

import ButtonYellow from '../../components/buttons/ButtonYellow.vue';
import ButtonDark from '../../components/buttons/ButtonDark.vue';
import ButtonRed from '../../components/buttons/ButtonRed.vue';
import SuccessBanner from '../../components/alerts/SuccessBanner.vue';
import ErrorBanner from '../../components/alerts/ErrorBanner.vue';
import Loading from '../../components/alerts/Loading.vue';
import Modal from '../../components/Modal.vue';
import ConfirmModal from '../../components/ConfirmModal.vue';
import ButtonGreen from '../../components/buttons/ButtonGreen.vue';
import ButtonWhite from '../../components/buttons/ButtonWhite.vue';

const alertLoadingTransition = useAlertLoadingTransition()
const mainContentTransition = useMainContentTransition()
const popUpTransition = usePopUpTransition()

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

const dataStatus = ref(null)
const loading = ref(false)
const searchValue = ref('')
const currentUnitId = ref(null)

const viewingUnitsList = ref(null)
const viewingAddUnitModal = ref(false)
const viewingUpdateUnitModal = ref(false)

const unitsListData = ref([])

const addFormData = ref({
    unitName: '',
    description: '',
})

const updateFormData = ref({
    id: '',
    unitName: '',
    description: '',
})

const errors = ref({
    unitName: [],
    description: [],
})

const filteredUnitsListData = computed(() => {
  return unitsListData.value.filter(unit => {
    return unit.unitName.toLowerCase().includes(searchValue.value.toLowerCase())
  })
})

//FUNCTIONS API
function getUnitsListData() {
    dataStatus.value = "Loading..."
    
    axiosClient.get('/api/units-list')
        .then(response => {
            unitsListData.value = response.data.unitsListData
            console.log(response);

            if(unitsListData.value.length < 1){
                dataStatus.value = "No data"
            }else{
                dataStatus.value = null
            }
        })
        .catch(error => {
            console.log(error);
            
            if(unitsListData.value.length < 1){
                dataStatus.value = "No data"
            }else{
                dataStatus.value = null
            }
        })
}

function submitAddForm(){
    loading.value = true

    axiosClient.post('/api/add-unit', addFormData.value)
        .then(response => {
            loading.value = false
            showSuccessMsg("Success", response.data.message)
            getUnitsListData()
            closeAddUpModal()
        })
            .catch(error => {
            loading.value = false
            showErrorMsg(error.response.data.message)
            errors.value = error.response.data.errors
        })
}

function submitUpdateForm(id){
  loading.value = true

  axiosClient.put(`/api/update-unit/${id}`, updateFormData.value)
    .then(response => {
      loading.value = false
      showSuccessMsg("Success", response.data.message)
      getUnitsListData()
      closeAddUpModal()
    })
    .catch(error => {
      loading.value = false
      showErrorMsg(error.response.data.message)
      errors.value = error.response.data.errors
    })
}

function deleteUnit(){
    loading.value = true

    axiosClient.delete(`/api/delete-unit/${confirmId.value}`)
      .then(response => {
        loading.value = false
        viewingConfirmModal.value = false
        showSuccessMsg("Success", response.data.message)
        getUnitsListData()
      })
      .catch(error => {
        loading.value = false
        showErrorMsg(error.response.data.message)
      })
}

// OTHER FUNCTIONS
function closeAddUpModal () {
    viewingAddUnitModal.value = false
    viewingUpdateUnitModal.value = false

    clearInputsErrors()
}

function showAddUnitModal () {
    viewingAddUnitModal.value = true
}

function showUpdateUnitModal (unit) {
    viewingUpdateUnitModal.value = true

    updateFormData.value.id = unit.id
    updateFormData.value.unitName = unit.unitName
    updateFormData.value.description = unit.description
}

function clearInputsErrors(){
    errors.value.unitName = []
    errors.value.description = []
    addFormData.value.unitName = ''
    addFormData.value.description = ''
    updateFormData.value.id = ''
    updateFormData.value.unitName = ''
    updateFormData.value.description = ''
}

function toggleMoreActions (id){
  if (currentUnitId.value == id){
    currentUnitId.value = null
  }else{
    currentUnitId.value = id
  } 
}

function handleClickOutside(e) {
  
  if (!e.target.closest(".btnActions") && !e.target.closest('.modal')) {
    currentUnitId.value = null
  }
}

onMounted(() => {
    viewingUnitsList.value = true
    viewingAddUnitModal.value = false
    viewingUpdateUnitModal.value = false
    getUnitsListData()

    document.addEventListener("click", handleClickOutside)
    
})

onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside)
})
</script>

<style scoped>

</style>