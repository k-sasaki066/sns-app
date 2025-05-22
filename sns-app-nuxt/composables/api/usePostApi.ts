import { useApi } from '~/composables/useApi'

export const usePostApi = () => {
    const $axios = useApi()

    const fetchPosts = async (offset = 0, limit = 20) => {
        const res = await $axios.get('/posts', { params: { offset, limit } })
        return res.data.posts
    }

    const createPost = async (content: string) => {
        const res = await $axios.post('/posts', { content })
        return res.data.post
    }

    const deletePost = async (postId: number) => {
        await $axios.delete(`/posts/${postId}`)
    }

    const toggleLike = async (postId: number) => {
        const res = await $axios.post(`/posts/${postId}/like`)
        return res.data
    }

    return {
        fetchPosts,
        createPost,
        deletePost,
        toggleLike,
    }
}