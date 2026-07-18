<template>
    <div  class="flex flex-row h-screen">
        <aside class="sidebar z-50 overflow-y-auto overflow-x-hidden absolute h-[100dvh] pb-[env(safe-area-inset-bottom)] pt-[env(safe-area-inset-top)] sm:static flex-col flex bg-darkgray-pri shadow-md transform transition-all duration-500"
            :class="isSidebarVisible ? 'w-72' : 'w-0 '"
        >
            <div class="sticky top-0 w-full mb-4 bg-darkgray-pri px-4 z-10">
                <h3 class="text-warmyellow-pri font-bold text-center border-b py-2 xl:py-3">IMS</h3>
            </div>

            <router-link 
                :to="{ name: 'Dashboard' }" 
                :class="route.name === 'Dashboard' ? 'activeSidebarBtn' : 'sidebarBtn'" 
                class="sidebar"
            >
                <i class="flex items-center justify-center mr-2 fa-solid fa-house size-5"></i>
                <span class="">Dashboard</span>
            </router-link>

            <div 
                @click="arePOBtnsVisible = !arePOBtnsVisible" 
                :class="arePOBtnsVisible ? 'font-bold' : ''" 
                class="flex flex-row items-center justify-between sidebar sidebarBtn"
            >
                <div class="flex">
                    <i class="flex items-center justify-center mr-2 fa-solid fa-file-arrow-down size-5"></i>
                    <span>Purchase Order</span>
                </div>
                <i class="transition-all transform fa-solid fa-caret-down" :class="arePOBtnsVisible ? '-rotate-180 ' : ''"></i>
            </div>
            <div :class="arePOBtnsVisible ? 'scale-y-100 opacity-100 h-[88px]' : 'scale-y-0 opacity-0 h-0'"  class="flex flex-col pl-4 transition-all duration-300 origin-top transform">
                <router-link 
                    :to="{ name: 'CreatePO' }" 
                    :class="route.name === 'CreatePO' ? 'activeSidebarBtn' : 'sidebarBtn'" 
                    class="sidebar"
                >
                    Create PO
                </router-link>
                <router-link 
                    :to="{ name: 'ManagePO' }" 
                    :class="route.name === 'ManagePO' ? 'activeSidebarBtn' : 'sidebarBtn'" 
                    class="sidebar"
                >
                    Manage PO
                </router-link>
            </div>
            
            <div 
                @click="areSOBtnsVisible = !areSOBtnsVisible" 
                :class="areSOBtnsVisible ? 'font-bold' : ''" 
                class="flex flex-row items-center justify-between sidebar sidebarBtn"
            >
                <div class="flex">
                    <i class="flex items-center justify-center mr-2 fa-solid fa-file-arrow-up size-5"></i>
                    <span>Sale Order</span>
                </div>
                <i class="transition-all transform fa-solid fa-caret-down" :class="areSOBtnsVisible ? '-rotate-180 ' : ''"></i>
            </div>
            <div :class="areSOBtnsVisible ? 'scale-y-100 opacity-100 h-[88px]' : 'scale-y-0 opacity-0 h-0'"  class="flex flex-col pl-4 transition-all duration-300 origin-top transform">
                <router-link 
                    :to="{ name: 'CreateSO' }" 
                    :class="route.name === 'CreateSO' ? 'activeSidebarBtn' : 'sidebarBtn'" 
                    class="sidebar"
                >
                    Create SO
                </router-link>

                <router-link 
                    :to="{ name: 'ManageSO' }" 
                    :class="route.name === 'ManageSO' ? 'activeSidebarBtn' : 'sidebarBtn'" 
                    class="sidebar"
                >
                    Manage SO
                </router-link>
            </div>
            
            <router-link 
                :to="{ name: 'Items' }" 
                :class="route.name === 'Items' ? 'activeSidebarBtn' : 'sidebarBtn'" 
                class="sidebar"
            >
                <i class="flex items-center justify-center mr-2 fa-solid fa-boxes-stacked size-5"></i>
                <span class="">Items</span>
            </router-link>

            <router-link 
                :to="{ name: 'Suppliers' }" 
                :class="route.name === 'Suppliers' ? 'activeSidebarBtn' : 'sidebarBtn'" 
                class="sidebar"
            >
                <i class="flex items-center justify-center mr-2 fa-solid fa-industry size-5"></i>
                <span class="">Suppliers</span>
            </router-link>

            <router-link 
                :to="{ name: 'Customers' }" 
                :class="route.name === 'Customers' ? 'activeSidebarBtn' : 'sidebarBtn'" 
                class="sidebar"
            >
                <i class="flex items-center justify-center mr-2 fa-solid fa-users size-5"></i>
                <span class="">Customers</span>
            </router-link>

            <router-link 
                :to="{ name: 'Attributes' }" 
                :class="route.name === 'Attributes' ? 'activeSidebarBtn' : 'sidebarBtn'" 
                class="sidebar"
            >
                <i class="flex items-center justify-center mr-2 fa-solid fa-tag size-5"></i>
                <span class="">Attributes</span>
            </router-link>

            <router-link 
                v-if="user?.role == 'admin'" 
                :to="{ name: 'Users' }" 
                :class="route.name === 'Users' ? 'activeSidebarBtn' : 'sidebarBtn'" 
                class="sidebar"
            >
                <i class="flex items-center justify-center mr-2 fa-solid fa-user size-5"></i>
                <span class="">Users</span>
            </router-link>

            <router-link 
                :to="{ name: 'Settings' }" 
                :class="route.name === 'Settings' ? 'activeSidebarBtn' : 'sidebarBtn'" 
                class="sidebar"
            >
                <i class="flex items-center justify-center mr-2 fa-solid fa-gear size-5"></i>
                <span class="">Settings</span>
            </router-link>
        </aside>

        <div class="flex flex-col w-full overflow-hidden">

            <nav class="relative z-40 flex flex-row justify-between w-full px-6 py-2 bg-white shadow-md xl:py-3">
                
                <!-- Menu button -->
                <button @click="isSidebarVisible = !isSidebarVisible" class="menu-button px-4 text-2xl rounded-md hover:bg-gray-50">
                    <i class="menu-button fa-solid fa-bars "></i>
                </button>

                <button 
                    @click="viewingLogoutModal = true" 
                    title="Logout" 
                    type="button" 
                    class="px-4 text-2xl rounded-md hover:bg-gray-50"
                >
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </nav>

            <main class="py-4 px-2 sm:px-3 md:px-4 h-[calc(100vh-48px)] xl:h-[calc(100vh-56px)] w-full overflow-auto ">
                <router-view></router-view>
            </main>
        </div>
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
            :inUse="viewingLogoutModal" 
            btnRedText="Confirm" 
            @cancel="viewingLogoutModal = !viewingLogoutModal"
            @yes="logout"
        >
            <template v-slot:title>
                {{ confirmLogout.title }}
            </template>

            <template v-slot:body>
                {{ confirmLogout.body }}
            </template>
        </ConfirmModal>
    </teleport>
    
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { useAlertMessages } from '../composables/useAlertMessages';
import { useAlertLoadingTransition } from '../composables/useTransition';
import { useRoute } from 'vue-router';
import ErrorBanner from './alerts/ErrorBanner.vue';
import axiosClient from '../axios';
import router from '../router';
import useUserStore from '../store/user';
import ConfirmModal from './ConfirmModal.vue';

const route = useRoute()
const isSidebarVisible = ref(false)
const arePOBtnsVisible = ref(false)
const areSOBtnsVisible = ref(false)

const userStore = useUserStore()
const user = computed(() => userStore.user)

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

const confirmLogout = ref({
    title: "Confirm Logout",
    body: "Are you sure you want to logout?"
})

const viewingLogoutModal = ref(false)

const alertLoadingTransition = useAlertLoadingTransition()

function handleClickOutside(e) {
    if(!e.target.closest('.sidebar') && !e.target.closest('.menu-button') && (window.innerWidth < 640)){
        isSidebarVisible.value = false 
    }
}

async function logout(){
    try {
        const response = await axiosClient.post('/api/logout')
        
        userStore.logoutUser()
        router.push({name: 'Login'})
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to log out')
    }
}

onMounted(() => {
    window.addEventListener("click", handleClickOutside)
})

onBeforeUnmount(() => {
    window.removeEventListener("click", handleClickOutside)
})

</script>

<style scoped>

</style>