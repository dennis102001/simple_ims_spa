<template>
    
     <Transition
        :enter-from-class="authFormTransition.enterFrom"
        :enter-to-class="authFormTransition.enterTo"
        :enter-active-class="authFormTransition.enterActive"
        :leave-from-class="authFormTransition.leaveFrom"
        :leave-to-class="authFormTransition.leaveTo"
        :leave-active-class="authFormTransition.leaveActive"
    >
        <div v-show="loginMounted" class="flex flex-col justify-center flex-1 min-w-[300px] min-h-full px-6 pt-4 pb-12 lg:px-8">
            
            <div class="flex flex-col items-center justify-center w-full max-w-sm mx-auto mt-8 mb-4 text-center">
                <img src="/images/logo.png" alt="" class="rounded-full size-[90px]">
                <h3 class="font-bold text-darkgray-sec">Inventory Management</h3>
            </div>

            <form @submit.prevent="submit" class="w-full max-w-sm p-6 mx-auto bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl">
                <h4 class="mb-8 font-bold text-center">Sign in to your account</h4>

                <div class="flex flex-col w-full mb-4">
                    <label for="email" class="label-style-one">Email</label>
                    <input 
                        v-model="data.email" 
                        id="email" 
                        type="email" 
                        name="email" 
                        autocomplete="email" 
                        class="value-style"
                    >
                </div>

                <div class="flex flex-col w-full mb-4">
                    <label for="password" class="label-style-one">Password</label>
                    <input 
                        v-model="data.password" 
                        id="password" 
                        type="password" 
                        class="value-style"
                    >
                </div>

                <button type="submit" class="button-sign-in">
                    Sign in
                </button>

                <div class="pt-4 mt-6 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-600 mb-4">
                        Try a demo account:
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button
                            @click="useDemoAccount('admin')"
                            type="button"
                            class="button-demo-acc"
                        >
                            Admin Account
                        </button>
                        <button
                            @click="useDemoAccount('staff')"
                            type="button"
                            class="button-demo-acc"
                        >
                            Staff Account
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