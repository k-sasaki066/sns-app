import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getAuth, onAuthStateChanged, type User } from 'firebase/auth'

export const useAuthStore = defineStore('auth', () => {
    const firebaseUser = ref<User | null>(null)
    const isInitialized = ref(false)

    const initAuth = () => {
        const auth = getAuth()
        onAuthStateChanged(auth, (user) => {
            firebaseUser.value = user
            isInitialized.value = true
        })
    }

    return {
        firebaseUser,
        isInitialized,
        initAuth,
    }
})