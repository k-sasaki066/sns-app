import { onAuthStateChanged, getIdToken } from 'firebase/auth'
import type { User } from 'firebase/auth'

export const useFirebaseAuth = () => {
    const { $auth } = useNuxtApp()
    const user = useState<User | null>('firebase-user', () => null)
    const token = useState<string | null>('firebase-token', () => null)

    const refreshToken = async () => {
        if (user.value) {
        token.value = await user.value.getIdToken(true)
        }
    }

    onAuthStateChanged($auth, async (u) => {
        user.value = u
        token.value = u ? await u.getIdToken() : null
    })

    return { user, token, refreshToken }
}