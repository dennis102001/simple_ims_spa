<template>
    
     <Transition
        :enter-from-class="authFormTransition.enterFrom"
        :enter-to-class="authFormTransition.enterTo"
        :enter-active-class="authFormTransition.enterActive"
        :leave-from-class="authFormTransition.leaveFrom"
        :leave-to-class="authFormTransition.leaveTo"
        :leave-active-class="authFormTransition.leaveActive"
    >
        <div v-show="loginMounted" class="flex flex-col justify-center flex-1 min-w-[300px] min-h-full px-6 pt-6 lg:pt-12 pb-12 lg:px-8">

            <form @submit.prevent="submit" class="w-full max-w-md p-10 mx-auto bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-3xl">
                <div class="flex flex-col items-center mb-8 text-center">
                    <div class="w-20 h-20 mb-3 overflow-hidden bg-gradient-to-br from-indigo-50 to-gray-50 rounded-full shadow-sm">
                        <img 
                            src="/images/logo.png" 
                            alt="Company Logo" 
                            class="object-cover w-full h-full"
                            loading="lazy"
                        >
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Inventory Management</h2>
                    <p class="mt-1 text-sm text-gray-500">Sign in to access your dashboard</p>
                </div>

                <div class="flex flex-col w-full mb-4 relative ">
                    <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input 
                        v-model="data.email" 
                        id="email" 
                        type="email" 
                        name="email" 
                        placeholder="Email address"
                        autocomplete="email" 
                        class="pl-12 pr-5 py-3 w-full block bg-white rounded-md
                            text-base
                            outline outline-1 -outline-offset-1 outline-gray-300  
                            focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 "
                    >
                </div>

                <div class="flex flex-col w-full mb-4 relative ">
                    <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input 
                        v-model="data.password" 
                        id="password" 
                        type="password" 
                        placeholder="Password"
                        class="pl-12 pr-5 py-3 w-full block bg-white rounded-md
                            text-base
                            outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 
                            focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 "
                    >
                </div>

                <button type="submit" class="button-sign-in">
                    Sign in
                </button>

                <div class="pt-6 mt-6 border-t border-gray-200">
                    <p class="text-center text-sm text-darkgray-sec mb-6">
                        Try a demo account:
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button
                            @click="useDemoAccount('admin')"
                            type="button"
                            class="button-demo-acc"
                        >
                            <i class="fa-solid fa-user-shield text-lg mr-2"></i>
                            Admin
                        </button>
                        <button
                            @click="useDemoAccount('staff')"
                            type="button"
                            class="button-demo-acc"
                        >
                            <i class="fa-solid fa-user text-lg mr-2"></i>
                            Staff
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </Transition>

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
                    {{ successMsgTitle }} 
                </template>

                <template v-slot:body>
                    {{ successMsgBody }}
                </template>
            </SuccessBanner>
        </Transition>
    </teleport>
    
</template>

<script setup>
import { useAlertLoadingTransition, useAuthFormTransition } from '../composables/useTransition.js';
import { onMounted, ref } from 'vue';
import axiosClient from '../axios.js'
import router from '../router.js';
import { useAlertMessages } from '../composables/useAlertMessages';
import SuccessBanner from '../components/alerts/SuccessBanner.vue';
import ErrorBanner from '../components/alerts/ErrorBanner.vue';
import Loading from '../components/alerts/Loading.vue';
import ButtonDark from '../components/buttons/ButtonDark.vue';

const alertLoadingTransition = useAlertLoadingTransition()
const authFormTransition = useAuthFormTransition()

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

const loading = ref(false)
const loginMounted = ref(false)

const data = ref({
    email: '',
    password: ''
})

async function submit() {
    loading.value = true

    try {
        const response = await axiosClient.post('/api/login', data.value)

        localStorage.setItem('token', response.data.token);

        showSuccessMsg("Success", "Please wait...")
        router.push({ name: 'Dashboard' })
    } 
    catch (error) {
        if(error.code === 'ERR_NETWORK'){
            showErrorMsg('Server is unavailable')
        }
        else{
            showErrorMsg(error.response?.data?.message || "These credentials do not match our records")
        }
    }
    finally{
        loading.value = false
    }
}

function useDemoAccount(role){

    if (role === 'admin'){
        data.value.email = 'admin@example.com'
        data.value.password = 'aaaaaaaa'
    }

    if (role === 'staff'){
        data.value.email = 'staff@example.com'
        data.value.password = 'aaaaaaaa'
    }

}

onMounted(() => {
    loginMounted.value = true
})

</script>

<style  scoped>

</style>