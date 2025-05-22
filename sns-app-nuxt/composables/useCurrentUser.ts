import { computed } from 'vue'
import { useAuthStore } from '~/stores/useAuthStore'

export const useCurrentUser = () => {
    const authStore = useAuthStore()
    const currentUser = computed(() => authStore.firebaseUser)
    const currentUid = computed(() => currentUser.value?.uid ?? null)
    return { currentUser, currentUid, authStore }
}