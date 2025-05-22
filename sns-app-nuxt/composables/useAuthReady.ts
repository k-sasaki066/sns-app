import { useAuthStore } from '~/stores/useAuthStore'

export const waitForAuthReady = () => {
    const authStore = useAuthStore()
    return new Promise<void>((resolve) => {
        if (authStore.isInitialized) return resolve()
        const stop = watch(
        () => authStore.isInitialized,
        (val) => {
            if (val) {
            stop()
            resolve()
            }
        }
        )
    })
}