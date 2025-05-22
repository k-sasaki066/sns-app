import { useApi } from '~/composables/useApi'

export const useCommentApi = () => {
    const { $axios } = useNuxtApp()

    const fetchComments = async (postId: string | number) => {
        return await $axios.get(`/posts/${postId}/comments`)
    }

    const postComment = async (postId: string | number, comment: string) => {
        return await $axios.post(`/posts/${postId}/comments`, { comment })
    }

    return {
        fetchComments,
        postComment,
    }
}