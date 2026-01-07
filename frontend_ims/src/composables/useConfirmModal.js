import { ref } from "vue"

export function useConfirmModal () {
    const viewingConfirmModal = ref(false)
    const confirmTitle = ref(null)
    const confirmBody = ref(null)
    const confirmId = ref(null)

    function openConfirmModal(title, body, id){
        confirmTitle.value = title
        confirmBody.value = body
        confirmId.value = id
        viewingConfirmModal.value = true
    }
    
    return {
        viewingConfirmModal, 
        confirmTitle,
        confirmBody,
        confirmId,
        openConfirmModal
    }
}