import { ref } from "vue"

const viewingConfirmModal = ref(false)
const confirmTitle = ref('')
const confirmBody = ref('')
const confirmButtonText = ref('Confirm')
const onConfirm = ref(null)

export function useConfirmModal () {
    

    function openConfirmModal(title, body, callback, buttonText = 'Confirm'){
        confirmTitle.value = title
        confirmBody.value = body
        confirmButtonText.value = buttonText
        onConfirm.value = callback

        viewingConfirmModal.value = true
    }

    async function confirm(){
        const callback = onConfirm.value

        closeConfirmModal()

        if(callback){
            await callback()
        }
    }

    function closeConfirmModal(){
        viewingConfirmModal.value = false
        confirmTitle.value = ''
        confirmBody.value = ''
        confirmButtonText.value = 'Confirm'
        onConfirm.value = null
    }
    
    return {
        viewingConfirmModal, 
        confirmTitle,
        confirmBody,
        confirmButtonText,
        openConfirmModal,
        closeConfirmModal,
        confirm
    }
}