export function useFormatDate(){

    function formatDate(itemDate){
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        const date = new Date(itemDate)

        const month = months[date.getMonth()]
        const day = date.getDate()
        const year = date.getFullYear()

        return `${month} ${day}, ${year}`

    }

    return {
        formatDate
    }
}

