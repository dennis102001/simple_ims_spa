import { defineStore } from 'pinia';
import axiosClient from '../axios';

const useUserStore = defineStore('user', {
    state: () => ({
        user: null,
    }),
    actions: {
        fetchUser () {
            return axiosClient.get('/api/user')
                .then(({data}) => {
                    this.user = data;
                })
        },

        clearUser() {
            this.user = null
            localStorage.removeItem('token')
        },

        async logoutUser () {
            await axiosClient.post('/api/logout')
            this.user = null
            localStorage.removeItem('token')
        }
    }
})

export default useUserStore;