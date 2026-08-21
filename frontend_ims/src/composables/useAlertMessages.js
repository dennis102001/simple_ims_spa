import { ref } from "vue";

const hasSuccessMsg = ref(false)
const successMsgTitle = ref("")
const successMsgBody = ref("")
const hasErrorMsg = ref(false)
const errorMsgBody = ref("")

export function useAlertMessages(){

    function showSuccessMsg(title, body){
        successMsgTitle.value = title
        successMsgBody.value = body
        hasSuccessMsg.value = true
    }

    function closeSuccessMsg () {
        hasSuccessMsg.value = false
        successMsgTitle.value = ""
        successMsgBody.value = ""
    }

    function showErrorMsg(msg){
        errorMsgBody.value = msg
        hasErrorMsg.value = true
    }

    function closeErrorMsg () {
        hasErrorMsg.value = false
        errorMsgBody.value = ""
    }

    return {
        showSuccessMsg, 
        closeSuccessMsg,
        showErrorMsg,
        closeErrorMsg,
        hasSuccessMsg,
        hasErrorMsg,
        successMsgTitle,
        successMsgBody,
        errorMsgBody
    }
}