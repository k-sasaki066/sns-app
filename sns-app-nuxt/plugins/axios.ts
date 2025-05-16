import axios from 'axios'
import { getAuth } from 'firebase/auth'
import type { NuxtApp } from '#app'

export default defineNuxtPlugin((nuxtApp) => {
    const router = useRouter()
    const instance = axios.create({
        baseURL: 'http://localhost/api/v1', // Laravel API のベースURL
        headers: {
            'Content-Type': 'application/json',
        },
    })

  // 認証トークンを自動で付ける（Firebase使用時）
    instance.interceptors.request.use(async (config) => {
        const user = getAuth().currentUser
        if (user) {
            const token = await user.getIdToken()
            config.headers.Authorization = `Bearer ${token}`
        }
        return config
    })

    instance.interceptors.response.use(
        response => response,
        async (error) => {
            if (error.response?.status === 401) {
                alert('ログインが必要です')
                await router.push('/login')
            }
            return Promise.reject(error)
        }
    )

    return {
        provide: {
            axios: instance
        }
    }
})