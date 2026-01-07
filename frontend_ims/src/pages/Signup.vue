<template>
    
    <!-- SIGN UP -->
    <Transition
        :enter-from-class="authFormTransition.enterFrom"
        :enter-to-class="authFormTransition.enterTo"
        :enter-active-class="authFormTransition.enterActive"
        :leave-from-class="authFormTransition.leaveFrom"
        :leave-to-class="authFormTransition.leaveTo"
        :leave-active-class="authFormTransition.leaveActive"
    >

        <div v-show="signupMounted" class="flex flex-col lg:flex-row min-w-[300px] min-h-full max-w-[980px] mx-auto px-6 pt-4 pb-12 lg:py-24 lg:px-8">
            <div class="flex flex-col items-center flex-1 lg:items-start">
                <div class="w-full max-w-sm mb-8 lg:mb-0">
                    <div class="flex justify-center w-full max-w-sm mx-auto text-center ">
                        <img src="/images/logo.png" alt="" class="rounded-full size-[120px]">
                    </div>
                    <div class="flex flex-col items-center lg:items-start">
                        <h3 class="mb-4 text-3xl font-bold text-center lg:text-left text-darkgray-sec">Inventory Management System</h3>
                        <h1 class="mb-2 text-4xl font-semibold tracking-wide">Welcome!</h1>
                        <p class="text-base">Create your account to open and use the system.</p>
                    </div>
                </div>
                
            </div>

            <div class="flex flex-col items-center justify-start flex-1 lg:items-end">
                
                <form @submit.prevent="submit" class="w-full max-w-sm p-6 bg-white shadow-md rounded-xl">
                    <h2 class="mb-10 text-2xl font-bold text-center">Create new account</h2>

                    <div class="flex flex-col w-full mb-1 ">
                        <label for="name" class="mb-1 ml-2 text-sm font-semibold tracking-wider text-gray-500">Full Name:</label>
                        <input v-model="data.name" id="name" name="name" type="text" class="bg-gray-100 block w-full rounded-md px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 ">
                        <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.name ? errors.name[0] : '' }}</p>
                    </div>
                    <div class="flex flex-col w-full mb-1">
                        <label for="email" class="mb-1 ml-2 text-sm font-semibold tracking-wider text-gray-500">Email:</label>
                        <input v-model="data.email" id="email" name="email" type="email" class="bg-gray-100 block w-full rounded-md px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 ">
                        <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.email ? errors.email[0] : '' }}</p>
                    </div>
                    <div class="flex flex-col w-full mb-1">
                        <label for="password" class="mb-1 ml-2 text-sm font-semibold tracking-wider text-gray-500">Password:</label>
                        <input v-model="data.password" id="password" name="password" type="password" class="bg-gray-100 block w-full rounded-md px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 ">
                        <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.password ? errors.password[0] : '' }}</p>
                    </div>
                    <div class="flex flex-col w-full mb-4">
                        <label for="confirmation" class="mb-1 ml-2 text-sm font-semibold tracking-wider text-gray-500">Confirm Password:</label>
                        <input v-model="data.password_confirmation" id="confirmation" name="password_confirmation" type="password" class="bg-gray-100 block w-full rounded-md px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 ">
                    </div>
                    <button 
                        type="submit" 
                        class="flex w-full justify-center rounded-md bg-dark-pri px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-dark-sec active:bg-dark-ter focus-visible:outline-1 focus-visible:outline-offset-2  focus-visible:outline-indigo-600"
                    >SUBMIT</button>
                    <p class="mt-10 text-xs text-center text-gray-500">
                        Already have an account?
                        <router-link :to="{name: 'Login'}" class="font-semibold text-indigo-600 hover:text-indigo-500 focus-visible:outline-1 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 ">Log in here</router-link>
                    </p>
                </form>

            </div>
        </div>
    </Transition>

    <!-- LOADING SUCCESS ERROR -->
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
    </teleport>
</template>

<script setup>
import { useAlertLoadingTransition, useAuthFormTransition } from '../composables/useTransition.js';
import { onMounted, ref } from 'vue';
import { useAlertMessages } from '../composables/useAlertMessages';
import axiosClient from '../axios';
import router from '../router';

import ErrorBanner from '../components/alerts/ErrorBanner.vue';
import Loading from '../components/alerts/Loading.vue';

const { showSuccessMsg, closeSuccessMsg, showErrorMsg, closeErrorMsg, hasSuccessMsg, hasErrorMsg, successMsgTitle, successMsgBody, errorMsgBody } = useAlertMessages()

const alertLoadingTransition = useAlertLoadingTransition()
const authFormTransition = useAuthFormTransition()

const signupMounted = ref(false)
const loading = ref(false)

const data = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
})

const errors = ref({
    name: [],
    email: [],
    password: []
})

function submit() {
    loading.value = true

    axiosClient.get('/sanctum/csrf-cookie').then(response => {
        axiosClient.post("/register", data.value)
            .then(response => {
                router.push({name: 'Dashboard'})
                loading.value = false
            })
            .catch(error => {
                errors.value = error.response.data.errors;
                showErrorMsg(error.response.data.message)
                loading.value = false
            })
    });
}


onMounted(() => {
    signupMounted.value = true
})

</script>

<style scoped>

</style>