import { defineStore } from 'pinia'

export const usePostFormStore = defineStore('postFormStore', {
    state: () => ({
        content: ''
    }),
    actions: {
        setContent(value: string) {
            this.content = value
        },
        getContent() {
            return this.content
        },
        clearContent() {
            this.content = ''
        }
    }
})