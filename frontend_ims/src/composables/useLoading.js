import { ref } from "vue";

const isLoading = ref(false)

export function useLoading(){

    function showLoading(){
        isLoading.value = true
    }

    function closeLoading(){
        isLoading.value = false
    }

    return {
        isLoading,
        showLoading,
        closeLoading
    }
}
