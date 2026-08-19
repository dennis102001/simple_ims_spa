import { ref } from "vue";

const isLoading = ref(false)

export function useLoading(){

    function showLoading(){
        isLoading.value = true
    }

    function closeLoading(){
        setTimeout(function(){
            isLoading.value = false

        }, 7000)
    }

    return {
        isLoading,
        showLoading,
        closeLoading
    }
}
