import { defineStore } from 'pinia'

export const useErrorStore = defineStore('error', {
    state: () => ({
        message: '',
        status: 500,
    }),
    actions: {
        setError(message: string, status: number = 500) {
            this.message = message
            this.status = status
        },
        clearError() {
            this.message = ''
            this.status = 500
        }
    }
})