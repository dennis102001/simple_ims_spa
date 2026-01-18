import { ref } from "vue"

export function useUsersListTransition(){
    return ref({
        enterFrom: "opacity-0 -translate-x-20",
        enterTo: "translate-x-0 opacity-100",
        enterActive: "transition duration-500 ease-in-out",
        leaveFrom: "translate-x-0 opacity-100",
        leaveTo: "-translate-x-full opacity-0",
        leaveActive: "transition duration-[1000ms] ease",
    })
}

export function useItemsListTransition(){
    return ref({
        enterFrom: "opacity-0 -translate-x-20",
        enterTo: "translate-x-0 opacity-100",
        enterActive: "transition duration-500 ease-in-out",
        leaveFrom: "translate-x-0 opacity-100",
        leaveTo: "-translate-x-full opacity-0",
        leaveActive: "transition duration-[1000ms] ease",
    })
}

export function usePopUpTransition(){
    return {
        enterFrom: "scale-90 opacity-0",
        enterTo: "scale-100 opacity-100",
        enterActive: "transform transition duration-200 ease-out",
        leaveFrom: "scale-100 opacity-100",
        leaveTo: "scale-90 opacity-0",
        leaveActive: "transform transition duration-200 ease-in",
    }
}

export function useAlertLoadingTransition() {
    return {
        enterFrom: "-translate-y-20 opacity-0",
        enterTo: "translate-y-0 opacity-100",
        enterActive: "transition duration-500 ease-in-out transform",
        leaveFrom: "translate-y-0 opacity-100",
        leaveTo: "-translate-y-20 opacity-0",
        leaveActive: "transition duration-500 ease-in-out transform",
    }
}

export function useMainContentTransition(){
    return ref({
        enterFrom: "opacity-0 ",
        enterTo: "translate-x-0 opacity-100",
        enterActive: "transition  duration-300 ease-out",
        leaveFrom: "translate-x-0 opacity-100",
        leaveTo: "-translate-x-1/2 opacity-0",
        leaveActive: "transition  duration-300 ease-in",
    })
}

export function useFormTransition(){
    return {
        enterFrom: "translate-x-1/2 opacity-0",
        enterTo: "translate-x-0 opacity-100",
        enterActive: "transition  duration-300 ease-out",
        leaveFrom: "translate-x-0 opacity-100",
        leaveTo: "translate-x-1/2 opacity-0",
        leaveActive: "transition  duration-300 ease-in",
    }
}

export function useAuthFormTransition() {
    return {
        enterFrom: "-translate-y-10 opacity-0",
        enterTo: "translate-y-0 opacity-100",
        enterActive: "transition duration-700  ease transform",
        leaveFrom: "translate-y-0 opacity-100",
        leaveTo: "-translate-y-10 opacity-0",
        leaveActive: "transition duration-500 ease transform",
    }
}