<template>
    
    <section v-if="viewingCategoriesList" class="flex flex-col flex-1 h-full min-w-[100px] p-6 bg-white rounded-xl shadow-[0px_1px_4px_rgba(0,0,0,0.1)]">

        <div class="flex flex-row flex-wrap mb-4 px-1">
            <div class="flex flex-col items-start justify-center flex-1">
                <h5 class="font-semibold tracking-wider">Categories List</h5>
            </div>

            <ButtonYellow @click="showAddCategoryModal" type="button">
                <div class="relative flex flex-row items-center w-fit ">
                    <span class="text-sm px-2">
                        <i class="fa-solid fa-plus"></i>
                    </span>
                    <span class="pr-2">New Category</span>
                </div>
            </ButtonYellow>
        </div>
        
        <!-- Search bar -->
        <div class="flex mb-2">
            <input 
                v-model="searchValue" 
                type="text" 
                placeholder="Search Category" 
                class="search-bar"
            >
        </div>

        <div class="relative flex flex-1 overflow-hidden max-h-[500px] lg:max-h-full ">

            <!-- Gradient overlays -->
            <div class="absolute top-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-b from-white to-transparent"></div>
            <div class="absolute bottom-0 left-0 z-20 w-full h-2 pointer-events-none bg-gradient-to-t from-white to-transparent"></div>

            <div class="flex flex-1 overflow-auto">

                <div v-if="dataStatus" class="flex items-center justify-center w-full py-3">
                    <p>{{ dataStatus }}</p>
                </div>

                <!-- Card grid -->
                <div class="my-2 content-start flex-1 gap-4 p-1 overflow-hidden min-h-fit grid grid-cols-[repeat(auto-fill,minmax(250px,1fr))]">
                
                    <!-- Card items -->
                    <div 
                        v-if="!dataStatus" 
                        v-for="category in filteredCategoriesListData" 
                        class="relative flex flex-col rounded-lg shadow-[0px_1px_4px_rgba(0,0,0,0.1)] max-h-full hover:shadow-[0_1px_4px_rgba(0,0,0,0.25)] transition-all"
                    >
                    
                        <!-- Card head -->
                        <div class="flex items-start pt-5 pb-2 mx-5 overflow-hidden">
                            <h5 class="font-bold truncate" :title="category.categoryName">
                                {{ category.categoryName }}
                            </h5>

                            <button 
                                @click="toggleMoreActions(category.id)"
                                class="px-4 py-1 ml-auto rounded-md btnActions hover:bg-gray-300"
                                type="button"
                            >
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                        </div>

                        <!-- Card body -->
                        <div class="grid gap-2 px-5 pb-5">
                            <div class="flex h-fit overflow-hidden">
                                <p class="text-sm w-full py-1 line-clamp-2 " :title="category.description">
                                    {{ category.description ?? 'No description' }}
                                </p>
                            </div>
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
                            <div v-if="currentCategoryId == category.id" class="absolute flex flex-col items-center justify-center w-full h-full gap-1 rounded-lg bg-black/60">
                                <div class="grid w-3/5 grid-cols-1 gap-1  btnActions min-w-[150px]">
                                    <button 
                                        @click="showUpdateCategoryModal(category)" 
                                        class="w-full px-4 py-2 bg-white rounded-lg hover:shadow-[0_0_6px] hover:shadow-white/70" 
                                        type="button"
                                    >
                                        Update
                                    </button>

                                    <button 
                                        @click="openConfirmModal('Delete Category', 'This category will no longer be selectable for new items. Existing records will not be affected.', category.id)"
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
    </section>

    <Modal :inUse="viewingAddCategoryModal" @close="closeAddUpModal">
        <form @submit.prevent="submitAddForm" class="w-full max-w-sm mb-4 bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl">
            
            <div class="px-6 py-2 rounded-t-xl border-b flex flex-row justify-between items-center text-warmyellow-pri bg-darkgray-pri">
                <h5 class="font-semibold tracking-wide">
                    <i class="fas fa-tag"></i>
                    <span class="ml-2">New Category</span>
                </h5>
                <button 
                    @click="closeAddUpModal" 
                    class="ml-auto hover:bg-darkgray-sec rounded-full size-10 flex items-center justify-center cursor-pointer" 
                    title="close"
                    type="button"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="flex flex-col w-full px-6 pt-6 pb-4">
                <TextInput
                    v-model="addFormData.categoryName"
                    id="add-category" 
                    label="Category Name:"
                    :message="errors.categoryName ? errors.categoryName[0] : ''"
                    type="text" 
                />

                <TextArea
                    v-model="addFormData.description" 
                    id="add-description" 
                    label="Description:"
                    :message="errors.description ? errors.description[0] : ''"
                />
            </div>

            <div class="flex flex-row justify-between w-full space-x-4 px-6 pt-4 pb-6 border-t rounded-b-xl">
                <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
                    <ButtonYellow type="submit" width="full">
                        Save
                    </ButtonYellow>

                    <ButtonWhite @click="closeAddUpModal" type="button" width="full">
                        Cancel
                    </ButtonWhite>
                </div>
            </div>
        </form>
    </Modal>

    <Modal :inUse="viewingUpdateCategoryModal" @close="closeAddUpModal">
        <form @submit.prevent="submitUpdateForm(updateFormData.id)" class="w-full max-w-sm mb-4 bg-white shadow-[0px_1px_4px_rgba(0,0,0,0.1)] rounded-xl">
            
            <div class="px-6 py-2 rounded-t-xl border-b flex flex-row justify-between items-center text-warmyellow-pri bg-darkgray-pri">
                <h5 class="font-semibold tracking-wide">
                    <i class="fas fa-tag"></i>
                    <span class="ml-2">Update Category</span>
                </h5>
                <button 
                    @click="closeAddUpModal" 
                    class="ml-auto hover:bg-darkgray-sec rounded-full size-10 flex items-center justify-center cursor-pointer" 
                    title="close"
                    type="button"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="flex flex-col w-full px-6 pt-6 pb-4">
                <TextInput
                    v-model="updateFormData.categoryName"
                    id="update-category" 
                    label="Category Name:"
                    :message="errors.categoryName ? errors.categoryName[0] : ''"
                    type="text" 
                />

                <TextArea
                    v-model="updateFormData.description" 
                    id="update-description" 
                    label="Description:"
                    :message="errors.description ? errors.description[0] : ''"
                />
            </div>

            <div class="flex flex-row justify-between w-full space-x-4 px-6 pt-4 pb-6 border-t rounded-b-xl">
                <div class="grid grid-cols-2 w-full gap-2 lg:gap-4">
                    <ButtonYellow type="submit" width="full">
                        Save
                    </ButtonYellow>
                    
                    <ButtonWhite @click="closeAddUpModal" type="button" width="full">
                        Cancel
                    </ButtonWhite>
                </div>
            </div>
        </form>
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
            <SuccessBanner v-if="hasSuccessMsg" @closeSuccess="closeSuccessMsg">
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
            <ErrorBanner v-if="hasErrorMsg" @closeError="closeErrorMsg">
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
            @yes="deleteCategory"
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
import { useAlertLoadingTransition } from '../../composables/useTransition';
import { onMounted, onBeforeUnmount, ref, computed } from 'vue';
import { useAlertMessages } from '../../composables/useAlertMessages';
import { useConfirmModal } from '../../composables/useConfirmModal';
import axiosClient from '../../axios.js'
import ButtonYellow from '../../components/buttons/ButtonYellow.vue';
import ButtonWhite from '../../components/buttons/ButtonWhite.vue';
import SuccessBanner from '../../components/alerts/SuccessBanner.vue';
import ErrorBanner from '../../components/alerts/ErrorBanner.vue';
import Loading from '../../components/alerts/Loading.vue';
import ConfirmModal from '../../components/ConfirmModal.vue';
import Modal from '../../components/Modal.vue';
import TextInput from '../../components/TextInput.vue';
import TextArea from '../../components/TextArea.vue';

const alertLoadingTransition = useAlertLoadingTransition()

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
const categoriesListData = ref([])
const currentCategoryId = ref(null)

const viewingCategoriesList = ref(null)
const viewingAddCategoryModal = ref(false)
const viewingUpdateCategoryModal = ref(false)

const addFormData = ref({
    categoryName: '',
    description: '',
})

const updateFormData = ref({
    id: '',
    categoryName: '',
    description: '',
})

const errors = ref({
    categoryName: [],
    description: [],
})

const filteredCategoriesListData = computed(() => {
    return categoriesListData.value.filter(item => {
        return item.categoryName.toLowerCase().includes(searchValue.value.toLowerCase())
    })
})

async function getCategoriesListData() {
    dataStatus.value = "Loading..."

    try {
        const response = await axiosClient.get('/api/categories-list')
        
        categoriesListData.value = response.data.categories_list
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to load categories list data')
    }
    finally{
        if(categoriesListData.value.length < 1){
            dataStatus.value = "No data"
        }else{
            dataStatus.value = null
        }
    }
}

async function submitAddForm(){
    loading.value = true

    try {
        const response = await axiosClient.post('/api/add-category', addFormData.value)

        showSuccessMsg("Success", response.data.message)
        await getCategoriesListData()
        closeAddUpModal()
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to add category')
        errors.value = error.response?.data?.errors || {}
    }
    finally{
        loading.value = false
    }
}

async function submitUpdateForm(id){
    loading.value = true

    try {
        const response = await axiosClient.put(`/api/update-category/${id}`, updateFormData.value)

        showSuccessMsg("Success", response.data.message)
        await getCategoriesListData()
        closeAddUpModal()
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to update category')
        errors.value = error.response?.data?.errors || {}
    }
    finally{
        loading.value = false
    }
}

async function deleteCategory(){
    loading.value = true

    try {
        const response = await axiosClient.delete(`/api/delete-category/${confirmId.value}`)

        viewingConfirmModal.value = false
        showSuccessMsg("Success", response.data.message)
        await getCategoriesListData()
    } 
    catch (error) {
        showErrorMsg(error.response?.data?.message || 'Failed to delete category')
    }
    finally{
        loading.value = false
    }
}

function closeAddUpModal () {
    viewingAddCategoryModal.value = false
    viewingUpdateCategoryModal.value = false

    clearInputsErrors()
}

function showAddCategoryModal () {
    viewingAddCategoryModal.value = true
}

function showUpdateCategoryModal (category) {
    viewingUpdateCategoryModal.value = true

    updateFormData.value.id = category.id
    updateFormData.value.categoryName = category.categoryName
    updateFormData.value.description = category.description
}

function clearInputsErrors(){
    errors.value.categoryName = []
    errors.value.description = []
    addFormData.value.categoryName = ''
    addFormData.value.description = ''
    updateFormData.value.id = ''
    updateFormData.value.categoryName = ''
    updateFormData.value.description = ''
}

function toggleMoreActions (id){
    if (currentCategoryId.value == id){
        currentCategoryId.value = null
    }else{
        currentCategoryId.value = id
    } 
}

function handleClickOutside(e) {
    if (!e.target.closest(".btnActions") && !e.target.closest('.modal')) {
        currentCategoryId.value = null
    }
}

onMounted(() => {
    viewingCategoriesList.value = true
    viewingAddCategoryModal.value = false
    viewingUpdateCategoryModal.value = false
    getCategoriesListData()

    document.addEventListener("click", handleClickOutside)
})

onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside)
})
</script>

<style scoped>

</style>