// Nuxt 3 で Firebase 認証などの再利用可能な認証ロジックをまとめて管理するためのファイル。認証関連の処理を共通化してVueコンポーネントから簡単に呼び出せる
import { createUserWithEmailAndPassword, updateProfile, signInWithEmailAndPassword, signOut,
    onAuthStateChanged, } from 'firebase/auth'
import type { User } from 'firebase/auth'

export const useAuth = () => {
    const { $auth } = useNuxtApp()
    const router = useRouter()
    const user = useState<User | null>('user', () => null)

    const register = async (name: string, email: string, password: string) => {
    const cred = await createUserWithEmailAndPassword($auth, email, password)
    await updateProfile(cred.user, { displayName: name })
        user.value = cred.user
        router.push('/')
    }

    const login = async (email: string, password: string) => {
        try {
            const cred = await signInWithEmailAndPassword($auth, email, password)
            user.value = cred.user
            router.push('/')
        } catch (error) {
            console.error('ログインエラー:', error)
        }
    }

    const logout = async () => {
    await $auth.signOut()
        user.value = null
        router.push('/login')
    }

    return { user, register, login, logout }
}