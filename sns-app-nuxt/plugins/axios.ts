import axios from 'axios'
import { getAuth } from 'firebase/auth'
import type { NuxtApp } from '#app'

export default defineNuxtPlugin((nuxtApp) => {
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

    return {
        provide: {
            axios: instance
        }
    }
})