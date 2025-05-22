<template>
    <main class="main grid">
        <SideNav @onMessagePosted="addMessage"></SideNav>
        <div class="home-container white">
            <h2 class="home-ttl">ホーム</h2>
            <div class="message-container">
                <Message :posts="posts" :fetchMessages="fetchMessages"></Message>
                <div class="loading-trigger" ref="loader"></div>
            </div>
        </div>
    </main>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick, computed } from 'vue'
import Message from '~/components/Message.vue'
import SideNav from '~/components/SideNav.vue'
import { useRouter } from 'vue-router'
import { useInfiniteScroll } from '~/composables/useInfiniteScroll'
import { useCurrentUser } from '~/composables/useCurrentUser'
import { waitForAuthReady } from '~/composables/useAuthReady'
import { usePostApi } from '~/composables/api/usePostApi'
const { fetchPosts } = usePostApi()
import { useLoadingStore } from '~/stores/useLoadingStore'

const { currentUser, currentUid, authStore } = useCurrentUser()
console.log(currentUser)

// useRouter を使ってページ遷移を制御
const router = useRouter()

const loadingStore = useLoadingStore()

const posts = ref<any[]>([]);
const offset = ref(0)
const limit = 20
const loading = ref(false)
const allLoaded = ref(false)
const loader = ref<HTMLElement | null>(null)

const fetchMessages = async () => {
    if (loading.value || allLoaded.value) return
    loading.value = true
    loadingStore.setLoading(true)

    try {
        const newPosts = await fetchPosts(offset.value, limit)
        if (newPosts.length < limit) {
            allLoaded.value = true
        }

        posts.value = [...posts.value, ...newPosts]
        offset.value += newPosts.length
    } catch (error) {
        console.error('メッセージ取得に失敗しました', error)
    } finally {
        loading.value = false
        loadingStore.setLoading(false)
    }
}

// 子コンポーネントから emit されたときの追加処理
const addMessage = (newPost: any) => {
    posts.value.unshift(newPost)
}

//useInfiniteScroll(loader, fetchMessages)
useInfiniteScroll(loader, async () => {
    await waitForAuthReady()

    await fetchMessages()
})

onMounted(async () => {
    await waitForAuthReady()

    await fetchMessages()
})
</script>