<template>
    <div class="message-wrap flex" v-for="(message, index) in messages" :key="index">
        <div class="message-inner flex">
            <p class="message__user-name">{{ message.user && message.user.name ? message.user.name : '匿名' }}</p>
            <div class="message__button-group flex">
                <button class="message__favorite-img-wrap" :disabled="message.isProcessing"  @click="toggleFavorite(message)">
                    <img class="message__favorite-img" :src="message.is_liked ? '/icons/heart-filled.png' : '/icons/heart.png'" alt="heart" :class="{ 'animate-pop': message.animate }"@animationend="message.animate = false">
                </button>
                <span class="message__favorite-count">{{ message.like_count }}</span>
                <div v-if="currentUser && message.user && message.user.uid === currentUser.uid">
                    <form class="message__delete-form" @submit.prevent="deleteMessage(message)">
                        <button class="message__delete-form-button" type="submit">
                            <img class="message__delete-form-img" src="/icons/cross.png" alt="delete">
                        </button>
                    </form>
                </div>
            </div>
            <div class="message__detail-button" v-if="!hideDetailButton">
                <NuxtLink :to="`/posts/${message.id}`" class="message__detail-button-link">
                    <img class="message__detail-button-img" src="/icons/detail.png" alt="detail">
                </NuxtLink>
            </div>
        </div>
        <p class="message__content">{{ message.content }}</p>
    </div>
</template>

<script setup lang="ts">
import '~/assets/css/message.css'
import { computed, watch, ref, onMounted, defineProps } from 'vue'
import { getAuth, onAuthStateChanged } from 'firebase/auth'
import { useRouter } from 'vue-router'

const { $axios } = useNuxtApp()

const props = defineProps<{
    messages: any[]
    fetchMessages: () => void
}>()
const messages = ref([...props.messages])

const router = useRouter()
const currentPath = computed(() => router.currentRoute.value.path)
const hideDetailButton = computed(() =>
    currentPath.value.startsWith('/posts/')
)

const currentUser = ref<{ uid: string } | null>(null)

onMounted(() => {
    const auth = getAuth()
    onAuthStateChanged(auth, (user) => {
        if (user) {
            const user = auth.currentUser
            currentUser.value = { uid: user.uid }
        }
    })
})

watch(() => props.messages, (newMessages) => {
    messages.value = newMessages
})

const toggleFavorite = async (message: any) => {
    if (message.isProcessing) return
    message.isProcessing = true

    const wasLiked = message.is_liked

    message.is_liked = !wasLiked
    message.like_count += wasLiked ? -1 : 1
    message.animate = true

    try {
        const res = await $axios.post(`/posts/${message.id}/like`)

        if (!res.data.success) {
        throw new Error('操作に失敗しました')
        }

        message.is_liked = res.data.is_liked
        message.like_count = res.data.like_count

    } catch (e) {
        message.is_liked = wasLiked
        message.like_count += wasLiked ? 1 : -1
        console.error('お気に入り切り替え失敗', e)
    } finally {
        message.isProcessing = false
    }
}

const deleteMessage = async (message: any) => {
    if (!confirm(`"${message.content}"\n\nこのメッセージを削除しますか？`)) return

    try {
        await $axios.delete(`/posts/${message.id}`)
        // 即時UIから削除
        messages.value = messages.value.filter(m => m.id !== message.id)
        router.push('/')
    } catch (e) {
        console.error('メッセージ削除失敗', e)
    }
}
</script>