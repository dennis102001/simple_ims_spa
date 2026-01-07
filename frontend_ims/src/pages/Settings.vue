<template>
    <div class="relative h-full w-full min-h-fit min-w-[200px]">

    
        <transition
            :enter-from-class="mainContentTransition.enterFrom "
            :enter-to-class="mainContentTransition.enterTo"
            :enter-active-class="mainContentTransition.enterActive"
            :leave-from-class="mainContentTransition.leaveFrom"
            :leave-to-class="mainContentTransition.leaveTo"
            :leave-active-class="mainContentTransition.leaveActive"
        >
            <div v-if="viewingSettings" class="h-full flex flex-col max-h-[1200px] min-h-[500px] w-full max-w-[1000px] mx-auto">

                    <h1 class="mb-6 text-3xl font-bold">Settings</h1>

                    <div class="p-6 space-y-6 bg-white shadow-[0px_2px_4px_rgba(0,0,0,0.1)] rounded-xl">
                        <section>
                            <h3 class="pb-2 mb-3 text-lg font-semibold border-b">Item Level Colors</h3>
                            <div class="space-y-2">
                                <button 
                                @click="showColorUpdateModal('Low Stock')"
                                class="w-full px-3 py-3 text-left transition rounded-lg hover:bg-gray-100"
                                >
                                    Set Low Stock Level Color
                                </button>
                                <button 
                                @click="showColorUpdateModal('No Stock')"
                                class="w-full px-3 py-3 text-left transition rounded-lg hover:bg-gray-100"
                                >
                                    Set No Stock Level Color
                                </button>
                            </div>
                        </section>

                        <section>
                            <h3 class="pb-2 mb-3 text-lg font-semibold border-b">User Account</h3>
                            <div class="space-y-2">
                                <button 
                                @click="showUserUpdateModal"
                                class="w-full px-3 py-3 text-left transition rounded-lg hover:bg-gray-100"
                                >
                                    Update Account Information
                                </button>
                                <button 
                                @click="showChangePassModal"
                                class="w-full px-3 py-3 text-left transition rounded-lg hover:bg-gray-100"
                                >
                                    Change Account Password
                                </button>
                            </div>
                            </section>
                    </div>
            </div>
        </transition>

    </div>

    <Modal :inUse="viewingUpdateColorModal" @close="closeColorUpdateModal">
        
        <form @submit.prevent="submitUpdateColorForm()" class="w-full mb-4 max-w-sm bg-white shadow-[0px_2px_4px_rgba(0,0,0,0.1)] rounded-xl">
            <div class="flex flex-col size-full">
                <div class="px-6 pt-4 pb-2 rounded-t-xl border-b mb-6 flex flex-row justify-between items-center">
                    <h4 class="font-semibold tracking-wide mb-1">{{ colorFormData.type }} Color</h4>
                    <div @click="closeColorUpdateModal" class="ml-auto hover:bg-gray-50 rounded-full size-10 flex items-center justify-center cursor-pointer" title="close">
                        <i class="fa-solid fa-xmark"></i>
                    </div>
                </div>

                <div class="flex flex-col w-full px-6 mb-6">
                    <label class="label-style-one">Select color indicator</label>
                    <div @click="viewingColorsMenu = !viewingColorsMenu" class="customers-dropdown relative cursor-pointer value-style">
                        <div class="flex">
                            <p v-if="colorFormData.color">{{ colorFormData.color.colorName }}</p>
                            <p v-else class="text-gray-400 ">select color</p>
                            <span class="ml-auto">
                                <i class="transition-all transform fa-solid fa-caret-down" :class="viewingColorsMenu ? '-rotate-180 ' : ''"></i>
                            </span>
                        </div>

                        <Transition
                            enter-from-class="-translate-y-[20px] opacity-0"
                            enter-to-class="translate-y-0 opacity-100"
                            enter-active-class="transition duration-300 transform"
                            leave-from-class="opacity-100"
                            leave-to-class="opacity-0"
                            leave-active-class="duration-300 transform transtion"
                        >
                            <div v-if="viewingColorsMenu" class="z-30 p-3 absolute flex-col flex w-full bg-white rounded-md shadow-[0px_2px_4px_rgba(0,0,0,0.1)] translate-y-3 -translate-x-3 overflow-auto">
                                <div class="max-h-[150px] overflow-auto">
                                    <div 
                                        @click="colorFormData.color = null"
                                        class="p-2 hover:bg-gray-50 cursor-pointer"
                                    >
                                        none
                                    </div>
                                    <div
                                        v-for="color in colorsListData"
                                        @click="colorFormData.color = color"
                                        class="p-2 hover:bg-gray-50 cursor-pointer"
                                    >
                                        {{ color.colorName }}
                                    </div>
                                </div>
                            </div>
                        </Transition> 
                    </div>
                </div>

                <div class="flex flex-row justify-between w-full space-x-4 px-6 pt-4 pb-6 border-t rounded-b-xl">
                    <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
                        <ButtonYellow width="full">
                            <div class="relative flex flex-row items-center w-full">
                                <span class="mx-auto">Save</span>
                            </div>
                        </ButtonYellow>
                        <ButtonWhite type="button" width="full" @click="closeColorUpdateModal">
                            <div class="relative flex flex-row items-center w-full">
                                <span class="mx-auto">Cancel</span>
                            </div>
                        </ButtonWhite>
                    </div>
                </div>
            
            </div>
        </form>
    </Modal>
    
    <Modal :inUse="viewingUpdateInfoModal" @close="closeUserUpdateModal">
        <form @submit.prevent="submitUpdateInfoForm()" class="w-full mb-4 max-w-sm bg-white shadow-[0px_2px_4px_rgba(0,0,0,0.1)] rounded-xl">
            <div class="flex flex-col size-full">
                <div class="px-6 pt-4 pb-2 rounded-t-xl border-b mb-6 flex flex-row justify-between items-center">
                    <h4 class="font-semibold tracking-wide mb-1">Update Account</h4>
                    <div @click="closeUserUpdateModal" class="ml-auto hover:bg-gray-50 rounded-full size-10 flex items-center justify-center cursor-pointer" title="close">
                        <i class="fa-solid fa-xmark"></i>
                    </div>
                </div>

                <div class="flex flex-col w-full px-6">
                    <label for="" class="label-style-one">Full Name:</label>
                    <input v-model="updateInfoFormData.name" name="name" type="text" class="value-style">
                    <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.name ? errors.name[0] : '' }}</p>
                </div>
                <div class="flex flex-col w-full px-6 mb-2">
                    <label for="" class="label-style-one">Email:</label>
                    <input v-model="updateInfoFormData.email" name="email" type="text" class="value-style">
                    <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.email ? errors.email[0] : '' }}</p>
                </div>

                <div class="flex flex-row justify-between w-full space-x-4 px-6 pt-4 pb-6 border-t rounded-b-xl">
                    <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
                        <ButtonYellow width="full">
                            <div class="relative flex flex-row items-center w-full">
                                <span class="mx-auto">Save</span>
                            </div>
                        </ButtonYellow>
                        <ButtonWhite type="button" width="full" @click="closeUserUpdateModal">
                            <div class="relative flex flex-row items-center w-full">
                                <span class="mx-auto">Cancel</span>
                            </div>
                        </ButtonWhite>
                    </div>
                </div>
            </div>
        </form>
    </Modal>

    <Modal :inUse="viewingChangePassModal" @close="closeChangePassModal">
        <div class="w-full mb-4 max-w-sm bg-white shadow-[0px_2px_4px_rgba(0,0,0,0.1)] rounded-xl">
            <div class="flex flex-col size-full">
                <div class="px-6 pt-4 pb-2 rounded-t-xl border-b mb-6 flex flex-row justify-between items-center">
                    <h4 class="font-semibold tracking-wide mb-1">Change Password</h4>
                    <div @click="closeChangePassModal" class="ml-auto hover:bg-gray-50 rounded-full size-10 flex items-center justify-center cursor-pointer" title="close">
                        <i class="fa-solid fa-xmark"></i>
                    </div>
                </div>

                <div class="flex flex-col w-full px-6">
                    <label for="" class="label-style-one">New Password:</label>
                    <input v-model="changePassFormData.password" name="password" type="text" class="value-style">
                    <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.password ? errors.password[0] : '' }}</p>
                </div>
                <div class="flex flex-col w-full px-6 mb-6">
                    <label for="" class="label-style-one">Confirm New Password:</label>
                    <input v-model="changePassFormData.password_confirmation" name="password_confirmation" type="text" class="value-style">
                </div>

                <div class="flex flex-row justify-between w-full space-x-4 px-6 pt-4 pb-6 border-t rounded-b-xl">
                    <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
                        <ButtonYellow 
                            @click="openConfirmModal('Confirm Change Password', 'You will be redirected to login page after confirmation', changePassFormData.id)" 
                            width="full"
                        >
                            <div class="relative flex flex-row items-center w-full">
                                <span class="mx-auto">Save</span>
                            </div>
                        </ButtonYellow>
                        <ButtonWhite type="button" width="full" @click="closeChangePassModal">
                            <div class="relative flex flex-row items-center w-full">
                                <span class="mx-auto">Cancel</span>
                            </div>
                        </ButtonWhite>
                    </div>
                </div>
            </div>
        </div>
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
        @yes="submitChangePassForm"
        >
            <template v-slot:title>{{ confirmTitle }}</template>
            <template v-slot:body>{{ confirmBody }}</template>
        </ConfirmModal>
    </teleport>

</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import SuccessBanner from '../components/alerts/SuccessBanner.vue';
import ConfirmModal from '../components/ConfirmModal.vue';
import ErrorBanner from '../components/alerts/ErrorBanner.vue';
import ButtonDark from '../components/buttons/ButtonDark.vue';
import ButtonYellow from '../components/buttons/ButtonYellow.vue';
import ButtonRed from '../components/buttons/ButtonRed.vue';
import ButtonGreen from '../components/buttons/ButtonGreen.vue';
import ButtonWhite from '../components/buttons/ButtonWhite.vue';
import Loading from '../components/alerts/Loading.vue';
import Modal from '../components/Modal.vue';
import useUserStore from '../store/user';

import { useAlertLoadingTransition, useMainContentTransition } from '../composables/useTransition';
import { useAlertMessages } from '../composables/useAlertMessages';
import { useConfirmModal } from '../composables/useConfirmModal';
import axiosClient from '../axios';

const alertLoadingTransition = useAlertLoadingTransition()
const mainContentTransition = useMainContentTransition()
const userStore = useUserStore()
const user = computed(() => userStore.user)

const viewingSettings = ref(false)
const viewingUpdateColorModal = ref(false)
const viewingColorsMenu = ref(false)
const viewingUpdateInfoModal = ref(false)
const viewingChangePassModal = ref(false)

const { 
    viewingConfirmModal, 
    confirmTitle,
    confirmBody,
    confirmId,
    openConfirmModal 
} = useConfirmModal()

const loading = ref(false)

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

const colorsListData = ref([])
const colorFormData = ref({
    type: null, 
    color: null,
    currentLow: null,
    currentNo: null
})

const updateInfoFormData = ref({
    id: '',
    name: '',
    email: '',
})

const errors = ref({
    name: [],
    email: [],
    password: []
})

const changePassFormData = ref({
    id: '',
    password: '',
    password_confirmation: ''
})

function showColorUpdateModal(type){
    colorFormData.value.type = type

    if(type === "Low Stock"){
        colorFormData.value.color = colorFormData.value.currentLow ?? null
    }
    else(
        colorFormData.value.color = colorFormData.value.currentNo ?? null
    )

    viewingUpdateColorModal.value = !viewingUpdateColorModal.value
}

function getColorsListData() {
    loading.value = true

    axiosClient.get('/api/colors-list')
        .then(response => {
            colorsListData.value = response.data.colorsListData

            colorFormData.value.currentLow = null
            colorFormData.value.currentNo = null
            colorsListData.value.forEach(color => {
                if(color.isSelectedForLow){
                    colorFormData.value.currentLow = color
                }

                if(color.isSelectedForNo){
                    colorFormData.value.currentNo = color
                }
            })

            loading.value = false
        })
        .catch(error => {
            showErrorMsg(error.response.data.message)
            console.log(error);
            loading.value = false
        })
}

function submitUpdateColorForm() {
    loading.value = true

    axiosClient.put('/api/set-color', colorFormData.value)
        .then(response => {
            showSuccessMsg('Success', response.data.message)
            loading.value = false

            viewingUpdateColorModal.value = false
            colorFormData.value.type = null
            colorFormData.value.color = null
            getColorsListData()
        })
        .catch(error => {
            loading.value = false
            showErrorMsg(error.response.data.message)
            console.log(error);
        })
}

function closeColorUpdateModal(){
    viewingUpdateColorModal.value = false
    colorFormData.value.type = null
    colorFormData.value.color = null
}

function showUserUpdateModal(){
    updateInfoFormData.value.id = user.value.id
    updateInfoFormData.value.name = user.value.name
    updateInfoFormData.value.email = user.value.email
    viewingUpdateInfoModal.value = true
}

function showChangePassModal(){
    changePassFormData.value.id = user.value.id
    viewingChangePassModal.value = true
}

function submitUpdateInfoForm(){
    loading.value = true

    axiosClient.put('/update-account-info', updateInfoFormData.value)
        .then(response => {
            loading.value = false
            showSuccessMsg("Success", "Account updated successfully")      
            
            viewingUpdateInfoModal.value = false
            updateInfoFormData.value.id = null
            updateInfoFormData.value.name = null
            updateInfoFormData.value.email = null
            userStore.fetchUser()

            errors.value.name = []
            errors.value.email = []
        })
        .catch(error => {
            loading.value = false
            showErrorMsg(error.response.data.message)
            errors.value = error.response.data.errors
        })
}

function submitChangePassForm(){
    loading.value = true

    axiosClient.put('/update-account-pass', changePassFormData.value)
        .then(response => {
            loading.value = false
            showSuccessMsg("Success", "Redirecting to login page...")      
            
            viewingChangePassModal.value = false
            changePassFormData.value.id = null
            changePassFormData.value.password = null
            changePassFormData.value.password_confirmation = null
            userStore.fetchUser()

            errors.value.password = []
        })
        .catch(error => {
            loading.value = false
            showErrorMsg(error.response.data.message)
            errors.value = error.response.data.errors
        })

    viewingConfirmModal.value = false
}

function closeUserUpdateModal(){
    updateInfoFormData.value.id = null
    updateInfoFormData.value.name = null
    updateInfoFormData.value.email = null
    
    errors.value.name = []
    errors.value.email = [] 
    viewingUpdateInfoModal.value = false
}

function closeChangePassModal(){
    changePassFormData.value.id = null
    changePassFormData.value.password = null
    changePassFormData.value.password_confirmation = null

    errors.value.password = []
    viewingChangePassModal.value = false
}

onMounted(() => {
    viewingSettings.value = true
    getColorsListData()
})

</script>

<style scoped>

</style>