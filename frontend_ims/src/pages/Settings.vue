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
            <section v-if="viewingSettings" class="h-full flex flex-col max-h-[1200px] min-h-[500px] w-full max-w-[1000px] mx-auto">

                <h1 class="mb-6 text-3xl font-bold">Settings</h1>

                <div class="p-6 space-y-6 bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl">
                    <section>
                        <h5 class="pb-2 mb-3 font-semibold border-b">Item Level Colors</h5>

                        <div class="space-y-2">
                            <button 
                                @click="showColorUpdateModal('Low Stock')" 
                                class="w-full px-3 py-3 text-left transition rounded-lg hover:bg-gray-100"
                                type="button"
                            >
                                Set Low Stock Level Color
                            </button>

                            <button 
                                @click="showColorUpdateModal('No Stock')" 
                                class="w-full px-3 py-3 text-left transition rounded-lg hover:bg-gray-100"
                                type="button"
                            >
                                Set No Stock Level Color
                            </button>
                        </div>
                    </section>

                    <section>
                        <h5 class="pb-2 mb-3 font-semibold border-b">User Account</h5>

                        <div class="space-y-2">
                            <button 
                                @click="showUserUpdateModal" 
                                class="w-full px-3 py-3 text-left transition rounded-lg hover:bg-gray-100"
                                type="button"
                            >
                                Update Account Information
                            </button>

                            <button 
                                @click="showChangePassModal" 
                                class="w-full px-3 py-3 text-left transition rounded-lg hover:bg-gray-100"
                                type="button"
                            >
                                Change Account Password
                            </button>
                        </div>
                    </section>
                </div>
            </section>
        </transition>
    </div>

    <Modal :inUse="viewingUpdateColorModal">
        <form @submit.prevent="submitUpdateColorForm()" class="w-full mb-4 max-w-sm bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl">
            <div class="flex flex-col size-full">
                
                <div class="px-6 pt-4 pb-2 rounded-t-xl border-b mb-6 flex flex-row justify-between items-center">
                    <h4 class="font-semibold tracking-wide mb-1">
                        {{ colorFormData.type }} Color
                    </h4>

                    <button 
                        @click="closeColorUpdateModal" 
                        class="ml-auto hover:bg-gray-50 rounded-full size-10 flex items-center justify-center cursor-pointer" 
                        title="close"
                        type="button"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="flex flex-col w-full px-6 mb-6">
                    <div class="label-style-one">Select color indicator</div>
                    <div @click="viewingColorsMenu = !viewingColorsMenu" class="colors-dropdown relative cursor-pointer p-2 h-[42px] w-full block bg-gray-50 rounded-md text-base outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 ">
                        <div class="flex items-center">
                            <div v-if="colorFormData.color" class="flex gap-2 items-center capitalize">
                                <div class="flex items-center">
                                    <div class="size-6 block rounded-md" :class="colorClass[colorFormData.color.colorName]"></div>
                                </div>
                                {{ colorFormData.color.colorName }}
                            </div>
                            <div v-else class="text-gray-400 ml-2">
                                select color
                            </div>
                            <div class="ml-auto">
                                <i class="transition-all transform fa-solid fa-caret-down" :class="viewingColorsMenu ? '-rotate-180 ' : ''"></i>
                            </div>
                        </div>

                        <Transition
                            enter-from-class="opacity-0"
                            enter-to-class="opacity-100"
                            enter-active-class="transition duration-200 transform"
                            leave-from-class="opacity-100"
                            leave-to-class="opacity-0"
                            leave-active-class="duration-200 transform transtion"
                        >
                            <div v-if="viewingColorsMenu" class="z-30 p-3 absolute flex-col flex w-full bg-white rounded-md shadow-[0px_1px_4px_rgba(0,0,0,0.1)] translate-y-4 -translate-x-2 overflow-auto">
                                <ul class="max-h-[200px] overflow-auto capitalize">
                                    <li @click="colorFormData.color = null" class="p-2 hover:bg-gray-50 cursor-pointer flex items-center gap-2">
                                        <div class="text-gray-500 flex items-center justify-center bg-gray-100 size-8  rounded-md pt-[1px]">
                                            <i class="fa-solid fa-ban"></i>
                                        </div>

                                        <span>
                                            none
                                        </span>
                                    </li>
                                    
                                    <li
                                        v-for="color in colorsListData"
                                        @click="colorFormData.color = color"
                                        class="p-2 hover:bg-gray-50 cursor-pointer flex items-center gap-2"
                                    >
                                        <div class="size-8 block rounded-md" :class="colorClass[color.colorName]"></div>

                                        <span>
                                            {{ color.colorName }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </Transition> 
                    </div>
                </div>

                <div class="flex flex-row justify-between w-full space-x-4 px-6 pt-4 pb-6 border-t rounded-b-xl">
                    <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
                        <ButtonYellow width="full" type="submit">
                            Save
                        </ButtonYellow>

                        <ButtonWhite @click="closeColorUpdateModal" type="button" width="full">
                            Cancel
                        </ButtonWhite>
                    </div>
                </div>
            </div>
        </form>
    </Modal>
    
    <Modal :inUse="viewingUpdateInfoModal" @close="closeUserUpdateModal">
        <form @submit.prevent="submitUpdateInfoForm()" class="w-full mb-4 max-w-sm bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl">
            <div class="flex flex-col size-full">
                
                <div class="px-6 pt-4 pb-2 rounded-t-xl border-b mb-6 flex flex-row justify-between items-center">
                    <h4 class="font-semibold tracking-wide mb-1">Update Account</h4>

                    <button 
                        @click="closeUserUpdateModal" 
                        class="ml-auto hover:bg-gray-50 rounded-full size-10 flex items-center justify-center cursor-pointer" 
                        title="close"
                        type="button"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="flex flex-col w-full px-6">
                    <label for="name" class="label-style-one">Full Name:</label>
                    <input 
                        v-model="updateInfoFormData.name" 
                        id="name" 
                        name="name" 
                        type="text" 
                        class="value-style"
                    >
                    <p class="block ml-2 text-sm text-red-500 h-[20px]">
                        {{ errors.name ? errors.name[0] : '' }}
                    </p>
                </div>
                <div class="flex flex-col w-full px-6 mb-2">
                    <label for="email" class="label-style-one">Email:</label>
                    <input 
                        v-model="updateInfoFormData.email" 
                        id="email" 
                        name="email" 
                        type="text" 
                        class="value-style"
                    >
                    <p class="block ml-2 text-sm text-red-500 h-[20px]">
                        {{ errors.email ? errors.email[0] : '' }}
                    </p>
                </div>

                <div class="flex flex-row justify-between w-full space-x-4 px-6 pt-4 pb-6 border-t rounded-b-xl">
                    <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
                        <ButtonYellow type="submit" width="full">
                            Save
                        </ButtonYellow>

                        <ButtonWhite @click="closeUserUpdateModal" type="button" width="full">
                            Cancel
                        </ButtonWhite>
                    </div>
                </div>
            </div>
        </form>
    </Modal>

    <Modal :inUse="viewingChangePassModal" @close="closeChangePassModal">
        <div class="w-full mb-4 max-w-sm bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl">
            <div class="flex flex-col size-full">
                
                <div class="px-6 pt-4 pb-2 rounded-t-xl border-b mb-6 flex flex-row justify-between items-center">
                    <h4 class="font-semibold tracking-wide mb-1">Change Password</h4>
                    
                    <button 
                        @click="closeChangePassModal" 
                        class="ml-auto hover:bg-gray-50 rounded-full size-10 flex items-center justify-center cursor-pointer" 
                        title="close"
                        type="button"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="flex flex-col w-full px-6">
                    <label for="password" class="label-style-one">New Password:</label>
                    <input 
                        v-model="changePassFormData.password" 
                        id="password" 
                        name="password" 
                        type="text" 
                        class="value-style"
                    >
                    <p class="block ml-2 text-sm text-red-500 h-[20px]">
                        {{ errors.password ? errors.password[0] : '' }}
                    </p>
                </div>

                <div class="flex flex-col w-full px-6 mb-6">
                    <label for="confirmation" class="label-style-one">Confirm New Password:</label>
                    <input 
                        v-model="changePassFormData.password_confirmation" 
                        id="confirmation" 
                        name="password_confirmation" 
                        type="text" 
                        class="value-style"
                    >
                </div>

                <div class="flex flex-row justify-between w-full space-x-4 px-6 pt-4 pb-6 border-t rounded-b-xl">
                    <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
                        <ButtonYellow 
                            @click="openConfirmModal('Confirm Change Password', 'You will be redirected to login page after confirmation', changePassFormData.id)" 
                            type="button"
                            width="full"
                        >
                            Save
                        </ButtonYellow>

                        <ButtonWhite 
                            @click="closeChangePassModal"
                            type="button" 
                            width="full" 
                        >
                            Cancel
                        </ButtonWhite>
                    </div>
                </div>
            </div>
        </div>
    </Modal>

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
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import SuccessBanner from '../components/alerts/SuccessBanner.vue';
import ConfirmModal from '../components/ConfirmModal.vue';
import ErrorBanner from '../components/alerts/ErrorBanner.vue';
import ButtonYellow from '../components/buttons/ButtonYellow.vue';
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
const loading = ref(false)

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

async function getColorsListData() {
    loading.value = true

    try {
        const response = await axiosClient.get('/api/colors-list')

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
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed load colors list data')
    }
    finally{
        loading.value = false
    }
}

async function submitUpdateColorForm() {
    loading.value = true

    try {
        const response = await axiosClient.put('/api/set-color', colorFormData.value)

        showSuccessMsg('Success', response.data.message)

        viewingUpdateColorModal.value = false
        colorFormData.value.type = null
        colorFormData.value.color = null
        await getColorsListData()
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to update color')
    }
    finally{
        loading.value = false
    }
}

function closeColorUpdateModal(){
    viewingUpdateColorModal.value = false
    viewingColorsMenu.value = false
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

async function submitUpdateInfoForm(){
    loading.value = true

    try {
        const response = await axiosClient.put('/update-account-info', updateInfoFormData.value)
        
        showSuccessMsg("Success", "Account updated successfully")      

        viewingUpdateInfoModal.value = false
        updateInfoFormData.value.id = null
        updateInfoFormData.value.name = null
        updateInfoFormData.value.email = null
        userStore.fetchUser()

        errors.value.name = []
        errors.value.email = []
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to update user information')
        errors.value = error.response?.data?.errors || {}
    }
    finally{
        loading.value = false
    }
}

async function submitChangePassForm(){
    loading.value = true

    try {
        const response = await axiosClient.put('/update-account-pass', changePassFormData.value)

        showSuccessMsg("Success", "Redirecting to login page...")      

        viewingChangePassModal.value = false
        changePassFormData.value.id = null
        changePassFormData.value.password = null
        changePassFormData.value.password_confirmation = null
        userStore.fetchUser()

        errors.value.password = []
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to change password')
        errors.value = error.response?.data?.errors || {}
    }
    finally{
        loading.value = false
        viewingConfirmModal.value = false
    }
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

function handleClickOutside(e){
    if(!e.target.closest('.colors-dropdown')){
        viewingColorsMenu.value = false
    }
}

onMounted(() => {
    viewingSettings.value = true
    getColorsListData()

    document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
})

</script>

<style scoped>

</style>