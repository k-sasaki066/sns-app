import { defineStore } from 'pinia'

export const useCommentFormStore = defineStore('commentForm', {
    state: () => ({
        contents: {} as Record<string, string>
    }),
    actions: {
        setContent(postId: string, text: string) {
        this.contents[postId] = text
        },
        getContent(postId: string): string {
        return this.contents[postId] || ''
        }
    }
})