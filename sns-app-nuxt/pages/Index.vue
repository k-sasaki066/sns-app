<template>
    <main class="main grid">
        <SideNav @onMessagePosted="addMessage"></SideNav>
        <div class="home-container white">
            <h2 class="home-ttl">ホーム</h2>
            <div class="message-container">
                <Message :messages="messages" :fetchMessages="fetchMessages"></Message>
                <div class="loading-trigger" ref="loader"></div>
            </div>
        </div>
    </main>
</template>

<script setup lang="ts">
import '~/assets/css/auth_form.css'
import '~/assets/css/index.css'
import { ref, onMounted, nextTick } from 'vue'
import Message from '~/components/Message.vue'
import SideNav from '~/components/SideNav.vue'
import { useRouter } from 'vue-router'
import { getAuth, onAuthStateChanged } from 'firebase/auth'
import { useInfiniteScroll } from '~/composables/useInfiniteScroll'
import { useLoadingStore } from '~/stores/useLoadingStore'

// Nuxt のプラグインで登録した axios を取得
const { $axios } = useNuxtApp()

// useRouter を使ってページ遷移を制御
const router = useRouter()

const loadingStore = useLoadingStore()

const messages = ref<any[]>([]);
const offset = ref(0)
const limit = 20
const loading = ref(false)
const allLoaded = ref(false)
const loader = ref<HTMLElement | null>(null)

const fetchMessages = async () => {
    loadingStore.setLoading(true)
    const auth = getAuth()
    const currentUser = auth.currentUser
    if (!currentUser) return
    const idToken = await currentUser.getIdToken()

    if (loading.value || allLoaded.value) return
    loading.value = true

    try {
        const res = await $axios.get('/posts', {
            headers: {
                Authorization: `Bearer ${idToken}`,
            },
            params: {
                offset: offset.value,
                limit,
            },
        })
        const newMessages = res.data.messages
        if (newMessages.length < limit) {
            allLoaded.value = true
        }

        messages.value = [...messages.value, ...newMessages]
        offset.value += newMessages.length
    } catch (error) {
        console.error('メッセージ取得に失敗しました', error)
    } finally {
        loading.value = false
        loadingStore.setLoading(false)
    }
}

// 子コンポーネントから emit されたときの追加処理
const addMessage = (newMessage: any) => {
    messages.value.unshift(newMessage)
}

useInfiniteScroll(loader, fetchMessages)

onMounted(async () => {
    const auth = getAuth()
    onAuthStateChanged(auth, (user) => {
        if (user) {
            fetchMessages()
            // ユーザーがログインしている場合、メッセージを取得
        } else {
            router.push('/login')
        }
    })

    await fetchMessages()
    // 初期メッセージ取得
})

</script>