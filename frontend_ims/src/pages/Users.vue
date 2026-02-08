<template>
  <div class="relative h-full w-full min-h-fit min-w-[200px]">
     
    <!-- Users list -->
    <transition
      :enter-from-class="mainContentTransition.enterFrom "
      :enter-to-class="mainContentTransition.enterTo"
      :enter-active-class="mainContentTransition.enterActive"
      :leave-from-class="mainContentTransition.leaveFrom"
      :leave-to-class="mainContentTransition.leaveTo"
      :leave-active-class="mainContentTransition.leaveActive"
      @after-leave="changeContent(pageContent)"
    >
      <section v-show="viewingUsersList" class="h-full flex flex-col max-h-[1200px] min-h-[750px] lg:min-h-[500px] w-full max-w-[1300px] mx-auto">
        
        <div class="flex flex-row items-start justify-between px-2 mt-2 mb-4">
          <h3 class="font-semibold tracking-wider">Users List</h3>
          <ButtonYellow @click="showAddForm" type="button">
            <div class="relative flex flex-row items-center w-fit ">
              <span class="text-sm px-2">
                  <i class="fa-solid fa-plus"></i>
              </span>
              <span class="pr-4">New User</span>
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
                placeholder="Search by name or email" 
                class="search-bar"
              >
            </div>

            <div class="flex flex-col flex-1 pb-2 overflow-hidden">

              <!-- Desktop table layout -->
              <div class="relative flex-1 hidden overflow-hidden lg:flex">

                <!-- Gradient overlay -->
                <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>

                <div class="flex-1 px-1 overflow-auto">
                  <div class="head-row grid grid-cols-[35%_35%_20%_10%]">
                      <div class="head-data">Name</div>
                      <div class="head-data">Email</div>
                      <div class="head-data">Status</div>
                      <div class="justify-center head-data">Action</div>
                  </div>
                  
                  <div v-if="dataStatus" class="flex items-center justify-center w-full py-3">
                    <p>{{ dataStatus }}</p>
                  </div>

                  <div v-if="!dataStatus" v-for="user in filteredUsersListData" class="body-row grid-cols-[35%_35%_20%_10%]">
                    <div class="body-data truncate" :title="user.name">
                      {{ user.name }}
                    </div>
                    <div class="body-data truncate" :title="user.email">
                      {{ user.email }}
                    </div>
                    <div class="body-data">
                      <p class="bg-cyan-100 rounded-full font-medium px-3 py-1 w-fit text-sm text-cyan-700 mb-1 capitalize">
                        {{ user.role }}
                      </p>
                    </div>
                    <div class="flex justify-center items-center body-data">
                      <button 
                        @click="toggleMoreActions(user.id)" 
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
                        <div v-if="currentUserId == user.id" class="absolute inset-0 flex items-center justify-end w-full h-full ">
                          <div class="bg-gradient-to-r flex-1 h-full from-[rgba(0,0,0,0.01)] to-darkgray-pri "></div>

                          <div class="flex items-center justify-end h-full p-2 bg-darkgray-pri w-fit btnActions">
                            <button 
                              @click="showUpdateForm(user)" 
                              class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-darkgray-sec"
                              type="button"
                            >
                              Update
                            </button>

                            <button 
                              @click="openConfirmModal('Delete User', 'This will deactivate the user and prevent future use. Existing records will not be affected.', user.id)"
                              class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-red-700"
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
                    <div v-if="!dataStatus" v-for="user in filteredUsersListData" class="relative flex flex-col rounded-lg shadow-[0px_1px_4px_rgba(0,0,0,0.1)] max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all">
                    
                      <!-- Card -->
                      <div class="relative flex items-start gap-2 p-5 mb-2 overflow-hidden rounded-t-md">
                        <div class="overflow-hidden">
                          <h5 class="font-semibold tracking-wide truncate mb-1" :title="user.name">
                            {{ user.name }}
                          </h5>

                          <p class="bg-cyan-100 rounded-full font-medium px-3 py-1 w-fit text-xs text-cyan-700 mb-1 capitalize">
                            {{ user.role }}
                          </p>

                          <p class="text-sm font-medium flex text-gray-500 mb-1">
                            <span><i class="fa-solid fa-envelope mr-3"></i></span>
                            <span :title="user.email" class="truncate">
                              {{ user.email }}
                            </span>
                          </p>
                        </div>

                        <button 
                          @click="toggleMoreActions(user.id)"
                          class="px-4 py-1 ml-auto rounded-md btnActions hover:bg-gray-300"
                          type="button"
                        >
                          <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
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
                        <div v-if="currentUserId == user.id" class="absolute flex flex-col items-center justify-center w-full h-full gap-1 rounded-lg bg-black/60">
                          <div class="grid w-3/5 grid-cols-1 gap-1 btnActions min-w-[150px]">
                            <button 
                              @click="showUpdateForm(user.id, user.name, user.email)" 
                              class="w-full px-4 py-2 bg-white rounded-lg hover:shadow-[0_0_6px] hover:shadow-white/70" 
                              type="button"
                            >
                              Update
                            </button>

                            <button 
                              @click="openConfirmModal('Delete User', 'This will deactivate the user and prevent future use. Existing records will not be affected.', user.id)"
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
      @after-leave="changeContent('Users List')"
    >
      <section v-show="viewingAddForm" class="w-full ">
        <form @submit.prevent="submitAddForm" class="w-full max-w-sm p-6 mx-auto mb-4 bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl">
          <h2 class="mb-10 text-2xl font-bold text-center">New User</h2>

          <div class="flex flex-col w-full h-[76px]">
              <label for="add-name" class="label-style-one">Full Name:</label>
              <input 
                v-model="addFormData.name" 
                id="add-name" 
                type="text" 
                class="value-style"
              >
              <p class="block ml-2 text-sm text-red-500 h-[20px]">
                {{ errors.name ? errors.name[0] : '' }}
              </p>
          </div>

          <div class="flex flex-col w-full h-[76px]">
              <label for="add-email" class="label-style-one">Email:</label>
              <input 
                v-model="addFormData.email" 
                id="add-email" 
                type="text" 
                class="value-style"
              >
              <p class="block ml-2 text-sm text-red-500 h-[20px]">
                {{ errors.email ? errors.email[0] : '' }}
              </p>
          </div>

          <div class="flex flex-col w-full h-[76px]">
            <div class="label-style-one">Role:</div>
            <div @click="viewingRolesMenu = !viewingRolesMenu" class="roles-dropdown relative cursor-pointer value-style">
              <div class="flex items-center">
                <div v-if="addFormData.role" class="flex gap-2 items-center capitalize">
                  {{ addFormData.role }}
                </div>
                <div v-else class="text-gray-400">
                  select user role
                </div>
                <div class="ml-auto">
                  <i class="transition-all transform fa-solid fa-caret-down" :class="viewingRolesMenu ? '-rotate-180 ' : ''"></i>
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
                <div v-if="viewingRolesMenu" class="z-30 p-3 absolute flex-col flex w-full bg-white rounded-md shadow-[0px_1px_4px_rgba(0,0,0,0.1)] translate-y-3 -translate-x-3 overflow-auto">
                  <ul class="max-h-[200px] overflow-auto capitalize">
                    <li
                      @click="addFormData.role = 'admin'"
                      class="p-2 hover:bg-gray-50 cursor-pointer flex items-center gap-2"
                    >
                      Admin
                    </li>

                    <li
                      @click="addFormData.role = 'user'"
                      class="p-2 hover:bg-gray-50 cursor-pointer flex items-center gap-2"
                    >
                      User
                    </li>
                  </ul>
                </div>
              </Transition> 
            </div>
          </div>

          <div class="flex flex-col w-full h-[76px]">
            <label for="add-password" class="label-style-one">Password:</label>
            <input 
              v-model="addFormData.password" 
              id="add-password" 
              type="text" 
              class="value-style"
            >
            <p class="block ml-2 text-sm text-red-500 h-[20px]">
              {{ errors.password ? errors.password[0] : '' }}
            </p>
          </div>

          <div class="flex flex-col w-full h-[76px] mb-4">
            <label for="add-confirmation" class="label-style-one">Confirm Password:</label>
            <input 
              v-model="addFormData.password_confirmation" 
              id="add-confirmation" 
              type="text" 
              class="value-style"
            >
          </div>
          
          <div class="flex items-end justify-end w-full">
            <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
              <ButtonYellow type="submit"  width="full">
                Save
              </ButtonYellow>

              <ButtonWhite @click="showUsersList" type="button" width="full">
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
      @after-leave="changeContent('Users List')"
    >
      <section v-show="viewingUpdateForm" class="w-full ">
        <form @submit.prevent="submitUpdateForm(updateFormData.id)" class="w-full max-w-sm p-6 mx-auto mb-4 bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl">
          <h2 class="mb-10 text-2xl font-bold text-center">Update User</h2>

          <div class="flex flex-col w-full h-[76px]">
              <label for="update-name" class="label-style-one">Full Name:</label>
              <input 
                v-model="updateFormData.name" 
                id="update-name" 
                type="text" 
                class="value-style"
              >
              <p class="block ml-2 text-sm text-red-500 h-[20px]">
                {{ errors.name ? errors.name[0] : '' }}
              </p>
          </div>

          <div class="flex flex-col w-full h-[76px]">
              <label for="update-email" class="label-style-one">Email:</label>
              <input 
                v-model="updateFormData.email" 
                id="update-email" 
                type="text" 
                class="value-style"
              >
              <p class="block ml-2 text-sm text-red-500 h-[20px]">
                {{ errors.email ? errors.email[0] : '' }}
              </p>
          </div>

          <div class="flex flex-col w-full h-[76px]">
            <div class="label-style-one">Role:</div>
            <div @click="viewingRolesMenu = !viewingRolesMenu" class="roles-dropdown relative cursor-pointer value-style">
              <div class="flex items-center">
                <div v-if="updateFormData.role" class="flex gap-2 items-center capitalize">
                  {{ updateFormData.role }}
                </div>
                <div v-else class="text-gray-400">
                  select user role
                </div>
                <div class="ml-auto">
                  <i class="transition-all transform fa-solid fa-caret-down" :class="viewingRolesMenu ? '-rotate-180 ' : ''"></i>
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
                <div v-if="viewingRolesMenu" class="z-30 p-3 absolute flex-col flex w-full bg-white rounded-md shadow-[0px_1px_4px_rgba(0,0,0,0.1)] translate-y-3 -translate-x-3 overflow-auto">
                  <ul class="max-h-[200px] overflow-auto capitalize">
                    <li
                      @click="updateFormData.role = 'admin'"
                      class="p-2 hover:bg-gray-50 cursor-pointer flex items-center gap-2"
                    >
                      Admin
                    </li>

                    <li
                      @click="updateFormData.role = 'user'"
                      class="p-2 hover:bg-gray-50 cursor-pointer flex items-center gap-2"
                    >
                      User
                    </li>
                  </ul>
                </div>
              </Transition> 
            </div>
          </div>

          <div class="flex flex-col w-full h-[76px]">
            <label for="update-password" class="label-style-one">New Password:</label>
            <input 
              v-model="updateFormData.password" 
              id="update-password" 
              type="text" 
              class="value-style"
            >
            <p class="block ml-2 text-sm text-red-500 h-[20px]">
              {{ errors.password ? errors.password[0] : '' }}
            </p>
          </div>

          <div class="flex flex-col w-full h-[76px] mb-4">
            <label for="update-confirmation" class="label-style-one">Confirm New Password:</label>
            <input 
              v-model="updateFormData.password_confirmation" 
              id="update-confirmation" 
              type="text" 
              class="value-style"
            >
          </div>

          <div class="flex items-end justify-end w-full">
            <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
              <ButtonYellow type="submit" width="full">
                Save
              </ButtonYellow>

              <ButtonWhite @click="showUsersList" type="button" width="full">
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
      @yes="deleteUser"
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

const viewingUsersList = ref(null)
const viewingAddForm = ref(null)
const viewingUpdateForm = ref(null)
const pageContent = ref(null)
const viewingRolesMenu = ref(false)

const loading = ref(false)
const usersListData = ref([])
const currentUserId = ref(null)
const searchValue = ref("")
const dataStatus = ref(null)

const filteredUsersListData = computed(() => {
  return usersListData.value.filter(user => {
    return user.name.toLowerCase().includes(searchValue.value.toLowerCase()) ||
      user.email.toLowerCase().includes(searchValue.value.toLowerCase())
  })
})

const addFormData = ref({
  name: '',
  email: '',
  role: null,
  password: '',
  password_confirmation: ''
})

const updateFormData = ref({
  id: '',
  name: '',
  email: '',
  role: null,
  password: '',
  password_confirmation: ''
})

const errors = ref({
  name: [],
  email: [],
  password: []
})

async function getUsersListData() {
  dataStatus.value = "Loading..."

  try {
    const response = await axiosClient.get('/api/users-list')

    usersListData.value = response.data
  } 
  catch (error) {
    showErrorMsg(error.response?.data?.message || 'Failed to load users list data')
  }
  finally{
    if(usersListData.value.length < 1){
      dataStatus.value = "No data"
    }else{
      dataStatus.value = null
    }
  }
}

async function submitUpdateForm(id){
  loading.value = true

  try {
    const response = await axiosClient.put(`/api/update-user/${id}`, updateFormData.value)
    
    showSuccessMsg("Success", "User updated successfully")      
    await getUsersListData()
    showUsersList()
  } 
  catch (error) {
    showErrorMsg(error.response?.data?.message || 'Failed to update user')
    errors.value = error.response?.data?.errors || {}
  }
  finally{
    loading.value = false
  }
}

async function submitAddForm(){
  loading.value = true

  try {
    const response = await axiosClient.post('/api/add-user', addFormData.value)

    showSuccessMsg("Success", "User added successfully")
    await getUsersListData()
    showUsersList()
  } 
  catch (error) {
    showErrorMsg(error.response?.data?.message || 'Failed to add user')
    errors.value = error.response?.data?.errors || {}
  }
  finally{
    loading.value = false
  }
}

async function deleteUser(){
  loading.value = true

  try {
    const response = await axiosClient.delete(`/api/delete-user/${confirmId.value}`)

    viewingConfirmModal.value = false
    showSuccessMsg("Success", "User deleted successfully")
    await getUsersListData()
  } 
  catch (error) {
    showErrorMsg(error.response?.data?.message || 'Failed to delete user')
  }
  finally{
    loading.value = false
  }
}

function changeContent(content){
  if(content === 'Users List'){
    viewingUsersList.value = true
  }else if(content === 'Add User'){
    viewingAddForm.value = true
  }else if(content === 'Update User'){
    viewingUpdateForm.value = true
  }else{
      return
  }

  mainContentTransition.value.enterFrom = "-translate-x-1/2 opacity-0"
}

function showUsersList () {
  viewingAddForm.value = false
  viewingUpdateForm.value = false

  clearInputsErrors()
}

function showAddForm () {
  viewingUsersList.value = false
  pageContent.value = "Add User"
  addFormData.value.role = 'user'
}

function showUpdateForm (user) {
  viewingUsersList.value = false
  pageContent.value = "Update User"

  updateFormData.value.id = user.id
  updateFormData.value.name = user.name
  updateFormData.value.email = user.email
  updateFormData.value.role = user.role
}

function clearInputsErrors(){
  errors.value.name = []
  errors.value.email = []
  errors.value.password = []

  addFormData.value.name = ''
  addFormData.value.email = ''
  addFormData.value.role = null
  addFormData.value.password = ''
  addFormData.value.password_confirmation = ''

  updateFormData.value.id = ''
  updateFormData.value.name = ''
  updateFormData.value.email = ''
  updateFormData.value.role = null
  updateFormData.value.password = ''
  updateFormData.value.password_confirmation = ''

  viewingRolesMenu.value = false
}

function toggleMoreActions (id){
  if (currentUserId.value == id){
    currentUserId.value = null
  }else{
    currentUserId.value = id
  } 
}

function handleClickOutside(e) {
  if (!e.target.closest(".btnActions")) {
    currentUserId.value = null
  }

  if(!e.target.closest('.roles-dropdown')){
    viewingRolesMenu.value = false
  }
}
 
onMounted(() => {
  viewingUsersList.value = true
  viewingAddForm.value = false
  viewingUpdateForm.value = false
  getUsersListData()

  document.addEventListener("click", handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside)
})

</script>

<style scoped>

</style>