<template>
  <div class="relative h-full w-full min-h-fit min-w-[200px]">
     
    <!-- USERS LIST -->
    <transition
      :enter-from-class="mainContentTransition.enterFrom "
      :enter-to-class="mainContentTransition.enterTo"
      :enter-active-class="mainContentTransition.enterActive"
      :leave-from-class="mainContentTransition.leaveFrom"
      :leave-to-class="mainContentTransition.leaveTo"
      :leave-active-class="mainContentTransition.leaveActive"
      @after-leave="changeContent(pageContent)"
    >
      <div v-show="viewingUsersList" class="h-full flex flex-col max-h-[1200px] min-h-[500px] w-full max-w-[1300px] mx-auto">
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
          <div class="flex flex-col flex-1 overflow-hidden min-h-[350px] bg-white rounded-xl shadow-[0px_2px_4px_rgba(0,0,0,0.1)] p-6">

            <!-- search -->
            <div class="flex mb-4">
              <input v-model="searchValue" type="text" placeholder="Search by name or email" class="search-bar">
            </div>

            <section class="flex flex-col flex-1 pb-2 overflow-hidden">

              <!-- div-based table -->
              <div class="relative flex-1 hidden overflow-hidden lg:flex">
                  <!-- gradient overlays -->
                  <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>

                  <div class="flex-1 px-1 overflow-auto">
                    <!-- head -->
                    <div class="head-row grid grid-cols-[35%_35%_20%_10%]">
                        <div class="head-data">Name</div>
                        <div class="head-data">Email</div>
                        <div class="head-data">Status</div>
                        <div class="justify-center head-data">Action</div>
                    </div>
                    <!-- body -->
                    <div v-if="dataStatus" class="flex items-center justify-center w-full py-3">
                      <p>{{ dataStatus }}</p>
                    </div>
                    <div v-if="!dataStatus" v-for="user in filteredUsersListData" class="body-row grid-cols-[35%_35%_20%_10%]">
                      <div class="body-data truncate" :title="user.name">{{ user.name }}</div>
                      <div class="body-data truncate" :title="user.email">{{ user.email }}</div>
                      <div class="body-data">
                        <p class="bg-cyan-100 rounded-full font-medium px-3 py-1 w-fit text-sm text-cyan-700 mb-1">{{ user.role }}</p>
                      </div>
                      <div class="flex justify-center items-center body-data">
                        <button @click="toggleMoreActions(user.id)" class="px-4 py-1 rounded-md btnActions hover:bg-gray-300">
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
                                type="button"
                                @click="showUpdateForm(user.id, user.name, user.email)" 
                                class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-darkgray-sec"
                              >
                                Update
                              </button>
                              <button 
                                type="button" 
                                @click="openConfirmModal('Delete User', 'Are you sure you want to delete this user?', user.id)"
                                class="h-full px-4 font-semibold text-white rounded-md w-fit hover:bg-red-700"
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
              
              <!-- grid items for small screen -->
              <div class="relative flex flex-1 overflow-hidden lg:hidden">
                  <!-- gradient overlays -->
                  <div class="absolute top-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-b from-white to-transparent"></div>
                  <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>
      
                  <div class="flex flex-1 overflow-auto">
                      <div v-if="dataStatus" class="flex items-center justify-center w-full py-3">
                          <p>{{ dataStatus }}</p>
                      </div>

                      <!-- grid - margin 2 for gradient overlays -->
                      <div class="my-2 content-start flex-1 gap-3 p-1 overflow-hidden min-h-fit grid grid-cols-[repeat(auto-fill,minmax(250px,1fr))]">
                      
                        <!-- grid items -->
                        <div v-if="!dataStatus" v-for="user in filteredUsersListData" class="relative flex flex-col rounded-lg shadow-emerald-300 shadow-[0_2px_4px] max-h-full">
                        
                            <!-- card -->
                            <div class="relative flex items-start gap-2 p-5 mb-2 overflow-hidden rounded-t-md">
                              <div class="overflow-hidden">
                                  <h5 class="font-semibold tracking-wide truncate mb-1" :title="user.name">{{ user.name }}</h5>
                                  <p class="bg-cyan-100 rounded-full font-medium px-3 py-1 w-fit text-sm text-cyan-700 mb-1">{{ user.role }}</p>
                                  <p class="text-sm font-medium flex text-gray-500 mb-1">
                                    <span><i class="fa-solid fa-envelope mr-3"></i></span>
                                    <span :title="user.email" class="truncate">{{ user.email }}</span>
                                  </p>
                              </div>
                              <button 
                                  type="button"
                                  @click="toggleMoreActions(user.id)"
                                  class="px-4 py-1 ml-auto rounded-md btnActions hover:bg-gray-300"
                              >
                                  <i class="fa-solid fa-ellipsis-vertical"></i>
                              </button>
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
                              <div v-if="currentUserId == user.id" class="absolute flex flex-col items-center justify-center w-full h-full gap-1 rounded-lg bg-black/60">
                                <div class="grid w-3/5 grid-cols-1 gap-1 btnActions min-w-[150px]">
                                  <button 
                                    type="button"
                                    @click="showUpdateForm(user.id, user.name, user.email)" 
                                    class="w-full px-4 py-2 bg-white rounded-lg hover:shadow-[0_0_6px] hover:shadow-white/70" 
                                  >
                                    Edit
                                  </button>
                                  <button 
                                    type="button" 
                                    @click="openConfirmModal('Delete User', 'Are you sure you want to delete this user?', user.id)"
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
              
            </section>

          </div>
        </div>
      </div>
    </transition>
    
    <!-- ADD FORM -->
    <transition
      :enter-from-class="formTransition.enterFrom"
      :enter-to-class="formTransition.enterTo"
      :enter-active-class="formTransition.enterActive"
      :leave-from-class="formTransition.leaveFrom"
      :leave-to-class="formTransition.leaveTo"
      :leave-active-class="formTransition.leaveActive"
      @after-leave="changeContent('Users List')"
    >
      <div v-show="viewingAddForm" class="w-full ">
                
        <form @submit.prevent="submitAddForm" class="w-full max-w-sm p-6 mx-auto mb-4 bg-white shadow-[0px_2px_4px_rgba(0,0,0,0.1)] rounded-xl">
          <h2 class="mb-10 text-2xl font-bold text-center">New User</h2>

          <div class="flex flex-col w-full h-[76px]">
              <label for="" class="label-style-one">Full Name:</label>
              <input v-model="addFormData.name" type="text" class="value-style">
              <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.name ? errors.name[0] : '' }}</p>
          </div>
          <div class="flex flex-col w-full h-[76px]">
              <label for="" class="label-style-one">Email:</label>
              <input v-model="addFormData.email" type="text" class="value-style">
              <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.email ? errors.email[0] : '' }}</p>
          </div>
          <div class="flex flex-col w-full h-[76px]">
              <label for="" class="label-style-one">Password:</label>
              <input v-model="addFormData.password" type="text" class="value-style">
              <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.password ? errors.password[0] : '' }}</p>
          </div>
          <div class="flex flex-col w-full h-[76px] mb-4">
              <label for="" class="label-style-one">Confirm Password:</label>
              <input v-model="addFormData.password_confirmation" type="text" class="value-style">
          </div>
          <div class="flex items-end justify-end w-full">
            <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
              <ButtonYellow type="submit"  width="full">
                <div class="relative flex flex-row items-center w-full">
                  <span class="mx-auto">Save</span>
                </div>
              </ButtonYellow>
              <ButtonWhite @click="showUsersList" type="button" width="full">
                <div class="relative flex flex-row items-center w-full">
                  <span class="mx-auto">Cancel</span>
                </div>
              </ButtonWhite>
            </div>
          </div>
        </form>
        
      </div>
    </transition>

    <!-- UPDATE FORM -->
    <transition
      :enter-from-class="formTransition.enterFrom"
      :enter-to-class="formTransition.enterTo"
      :enter-active-class="formTransition.enterActive"
      :leave-from-class="formTransition.leaveFrom"
      :leave-to-class="formTransition.leaveTo"
      :leave-active-class="formTransition.leaveActive"
      @after-leave="changeContent('Users List')"
    >
      <div v-show="viewingUpdateForm" class="w-full ">

        <form @submit.prevent="submitUpdateForm(updateFormData.id)" class="w-full max-w-sm p-6 mx-auto mb-4 bg-white shadow-[0px_2px_4px_rgba(0,0,0,0.1)] rounded-xl">
          <h2 class="mb-10 text-2xl font-bold text-center">Update User</h2>

          <div class="flex flex-col w-full h-[76px]">
              <label for="" class="label-style-one">Full Name:</label>
              <input v-model="updateFormData.name" type="text" class="value-style">
              <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.name ? errors.name[0] : '' }}</p>
          </div>
          <div class="flex flex-col w-full h-[76px]">
              <label for="" class="label-style-one">Email:</label>
              <input v-model="updateFormData.email" type="text" class="value-style">
              <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.email ? errors.email[0] : '' }}</p>
          </div>
          <div class="flex flex-col w-full h-[76px]">
              <label for="" class="label-style-one">New Password:</label>
              <input v-model="updateFormData.password" type="text" class="value-style">
              <p class="block ml-2 text-sm text-red-500 h-[20px]">{{ errors.password ? errors.password[0] : '' }}</p>
          </div>
          <div class="flex flex-col w-full h-[76px] mb-4">
              <label for="" class="label-style-one">Confirm New Password:</label>
              <input v-model="updateFormData.password_confirmation" type="text" class="value-style">
          </div>
          <div class="flex items-end justify-end w-full">
            <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
              <ButtonYellow type="submit" width="full">
                <div class="relative flex flex-row items-center w-full">
                  <span class="mx-auto">Save</span>
                </div>
              </ButtonYellow>
              <ButtonWhite @click="showUsersList" type="button" width="full">
                <div class="relative flex flex-row items-center w-full">
                  <span class="mx-auto">Cancel</span>
                </div>
              </ButtonWhite>
            </div>
          </div>
        </form>
      </div>
    </transition>
    
  </div>

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
    @yes="deleteUser"
    >
      <template v-slot:title>{{ confirmTitle }}</template>
      <template v-slot:body>{{ confirmBody }}</template>
    </ConfirmModal>
  </teleport>

</template>

<script setup>
import { useAlertLoadingTransition, usePopUpTransition, useMainContentTransition, useFormTransition } from '../composables/useTransition';
import { onMounted, ref, computed, onBeforeUnmount } from 'vue';
import { useAlertMessages } from '../composables/useAlertMessages';
import { useConfirmModal } from '../composables/useConfirmModal';
import axiosClient from '../axios';

import Loading from '../components/alerts/Loading.vue';
import SuccessBanner from '../components/alerts/SuccessBanner.vue';
import ErrorBanner from '../components/alerts/ErrorBanner.vue';
import ButtonYellow from '../components/buttons/ButtonYellow.vue';
import ButtonDark from '../components/buttons/ButtonDark.vue';
import ButtonRed from '../components/buttons/ButtonRed.vue';
import ConfirmModal from '../components/ConfirmModal.vue';
import ButtonGreen from '../components/buttons/ButtonGreen.vue';
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
const popUpTransition = usePopUpTransition()
const formTransition = useFormTransition()

const viewingUsersList = ref(null)
const viewingAddForm = ref(null)
const viewingUpdateForm = ref(null)
const pageContent = ref(null)

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
    password: '',
    password_confirmation: ''
})

const updateFormData = ref({
    id: '',
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

// FUNCTIONS API 
function getUsersListData() {
  dataStatus.value = "Loading..."

  axiosClient.get('/api/users-list')
    .then(response => {
      usersListData.value = response.data

      if(usersListData.value.length < 1){
        dataStatus.value = "No data"
      }else{
        dataStatus.value = null
      }
    })
    .catch(error => {
      console.log(error.response);
      
      if(usersListData.value.length < 1){
        dataStatus.value = "No data"
      }else{
        dataStatus.value = null
      }
    })
}

function submitUpdateForm(id){
  loading.value = true

  axiosClient.put(`/update-user/${id}`, updateFormData.value)
    .then(response => {
      loading.value = false
      showSuccessMsg("Success", "User updated successfully")      
      getUsersListData()
      showUsersList()
    })
    .catch(error => {
      loading.value = false
      showErrorMsg(error.response.data.message)
      errors.value = error.response.data.errors
    })
}

function submitAddForm(){
  loading.value = true

  axiosClient.post('/add-user', addFormData.value)
    .then(response => {
      loading.value = false
      showSuccessMsg("Success", "User added successfully")
      getUsersListData()
      showUsersList()
    })
    .catch(error => {
      loading.value = false
      showErrorMsg(error.response.data.message)
      errors.value = error.response.data.errors
    })
}

function deleteUser(){
    loading.value = true

    axiosClient.delete(`/delete-user/${confirmId.value}`)
      .then(response => {
        loading.value = false
        viewingConfirmModal.value = false
        showSuccessMsg("Success", "User deleted successfully")
        getUsersListData()
      })
      .catch(error => {
        loading.value = false
        showErrorMsg(error.response.data.message)
        console.log(error.response)
      })
}

//OTHER FUNCTIONS
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
}

function showUsersList () {
    viewingAddForm.value = false
    viewingUpdateForm.value = false

    clearInputsErrors()
}

function showAddForm () {
  viewingUsersList.value = false
  pageContent.value = "Add User"
}

function showUpdateForm (id, name, email) {
  viewingUsersList.value = false
  pageContent.value = "Update User"

  //to bind selected user
  updateFormData.value.id = id
  updateFormData.value.name = name
  updateFormData.value.email = email
}

function clearInputsErrors(){
    errors.value.name = []
    errors.value.email = []
    errors.value.password = []

    addFormData.value.name = ''
    addFormData.value.email = ''
    addFormData.value.password = ''
    addFormData.value.password_confirmation = ''

    updateFormData.value.id = ''
    updateFormData.value.name = ''
    updateFormData.value.email = ''
    updateFormData.value.password = ''
    updateFormData.value.password_confirmation = ''
}

function toggleMoreActions (id){
  if (currentUserId.value == id){
    currentUserId.value = null
  }else{
    currentUserId.value = id
  } 
}

function handleClickOutside(e) {
  // close if clicking outside the dropdown
  if (!e.target.closest(".btnActions")) {
    currentUserId.value = null
  }
}
 
onMounted(() => {
  viewingUsersList.value = true
  viewingAddForm.value = false
  viewingUpdateForm.value = false

  // GET USERS LIST
  getUsersListData()

  document.addEventListener("click", handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside)
})

</script>

<style scoped>

</style>