<template>
    
    <!-- LOGIN -->
     <Transition
        :enter-from-class="authFormTransition.enterFrom"
        :enter-to-class="authFormTransition.enterTo"
        :enter-active-class="authFormTransition.enterActive"
        :leave-from-class="authFormTransition.leaveFrom"
        :leave-to-class="authFormTransition.leaveTo"
        :leave-active-class="authFormTransition.leaveActive"
    >
        <div v-show="loginMounted" class="flex flex-col justify-center flex-1 min-w-[300px] min-h-full px-6 pt-4 pb-12   lg:px-8">
            
            <div class="flex flex-col items-center justify-center w-full max-w-sm mx-auto mt-8 mb-4 text-center">
                <img src="/images/logo.png" alt="" class="rounded-full size-[90px]">
                <h3 class="text-2xl font-bold text-darkgray-sec">Inventory Management</h3>
            </div>
            <form @submit.prevent="submit" class="w-full max-w-sm p-6 mx-auto bg-white shadow-md rounded-xl">
                <h2 class="mb-10 text-xl font-bold text-center">Sign in to your account</h2>

                <div class="flex flex-col w-full mb-4">
                    <label for="email" class="label-style-one">Email</label>
                    <input v-model="data.email" id="email" type="email" name="email" autocomplete="email" class="value-style">
                </div>
                <div class="flex flex-col w-full mb-4">
                    <label for="password" class="label-style-one">Password</label>
                    <input v-model="data.password" id="password" type="password" class="value-style">
                </div>
                <button 
                    type="submit" 
                    class="flex w-full justify-center rounded-md bg-dark-pri px-3 py-1.5 text-sm/6 font-semibold text-white shadow-sm hover:bg-dark-sec active:bg-dark-ter focus-visible:outline-1 focus-visible:outline-offset-2  focus-visible:outline-indigo-600"
                >
                    Sign in
                </button>
                <p class="mt-10 text-xs text-center text-gray-500">
                    Don't have an account?
                    <router-link :to="{name: 'Signup'}" class="font-semibold text-indigo-600 hover:text-indigo-500 focus-visible:outline-1 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 ">Create your account here</router-link>
                </p>
            </form>
        </div>
    </Transition>

    <!-- LOADING ERROR SUCCESS -->
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
import { useAlertMessages } from '../composables/useAlertMessages';
import axiosClient from '../axios.js'
import router from '../router.js';

import SuccessBanner from '../components/alerts/SuccessBanner.vue';
import ErrorBanner from '../components/alerts/ErrorBanner.vue';
import Loading from '../components/alerts/Loading.vue';
import useUserStore from '../store/user.js';

const alertLoadingTransition = useAlertLoadingTransition()
const authFormTransition = useAuthFormTransition()

const { showSuccessMsg, closeSuccessMsg, showErrorMsg, closeErrorMsg, hasSuccessMsg, hasErrorMsg, successMsgTitle, successMsgBody, errorMsgBody } = useAlertMessages()

const loading = ref(false)
const loginMounted = ref(false)

const data = ref({
    email: '',
    password: ''
})

function submit(){
    loading.value = true

    axiosClient.get('/sanctum/csrf-cookie').then(response => {
        axiosClient.post('/login', data.value)
            .then(response => {
                loading.value = false
                showSuccessMsg("Success", "Please wait...")    
                
                // const userStore = useUserStore()
                // userStore.fetchUser()

                // const user = userStore.user
                // console.log(user);
                // console.log(user.role);

                

                router.push({ name: 'Dashboard' })
            })
            .catch(error => {
                loading.value = false
                showErrorMsg("These credentials do not match our records")
            })
    })
} 

onMounted(() => {
    const userStore = useUserStore()
    const user = userStore.user

    console.log(user);
    

    loginMounted.value = true
})

</script>

<style  scoped>

</style>