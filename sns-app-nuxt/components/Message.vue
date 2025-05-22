<template>
    <div class="message-wrap flex" v-for="(post, index) in posts" :key="index">
        <div class="message-inner flex">
            <p class="message__user-name">{{ post.user && post.user.name ? post.user.name : '匿名' }}</p>
            <div class="message__button-group flex">
                <button class="message__favorite-img-wrap" :disabled="post.isProcessing"  @click="toggleFavorite(post)">
                    <img class="message__favorite-img" :src="post.is_liked ? '/icons/heart-filled.png' : '/icons/heart.png'" alt="heart" :class="{ 'animate-pop': post.animate }"@animationend="post.animate = false">
                </button>
                <span class="message__favorite-count">{{ post.like_count }}</span>
                <div v-if="currentUser && post.user && post.user.uid === currentUser.uid">
                    <form class="message__delete-form" @submit.prevent="deleteMessage(post)">
                        <button class="message__delete-form-button" type="submit">
                            <img class="message__delete-form-img" src="/icons/cross.png" alt="delete">
                        </button>
                    </form>
                </div>
            </div>
            <div class="message__detail-button" v-if="!hideDetailButton">
                <NuxtLink :to="`/posts/${post.id}`" class="message__detail-button-link">
                    <img class="message__detail-button-img" src="/icons/detail.png" alt="detail">
                </NuxtLink>
            </div>
        </div>
        <p class="message__content">{{ post.content }}</p>
    </div>
</template>

<script setup lang="ts">
import { computed, watch, ref, onMounted, defineProps } from 'vue'
import { useRouter } from 'vue-router'
import { useCurrentUser } from '~/composables/useCurrentUser'
import { usePostApi } from '~/composables/api/usePostApi'
const { toggleLike, deletePost } = usePostApi()

const { currentUser, currentUid, authStore } = useCurrentUser()

const props = defineProps<{
    posts: any[]
    fetchMessages: () => void
}>()
const posts = ref([...props.posts])

const router = useRouter()
const currentPath = computed(() => router.currentRoute.value.path)
const hideDetailButton = computed(() =>
    currentPath.value.startsWith('/posts/')
)

watch(() => props.posts, (newPosts) => {
    posts.value = newPosts
})

const toggleFavorite = async (post: any) => {
    if (post.isProcessing) return
    post.isProcessing = true

    const wasLiked = post.is_liked

    post.is_liked = !wasLiked
    post.like_count += wasLiked ? -1 : 1
    post.animate = true

    try {
        const res = await toggleLike(post.id)

        if (!res.success) {
        throw new Error('操作に失敗しました')
        }

        post.is_liked = res.is_liked
        post.like_count = res.like_count

    } catch (e) {
        post.is_liked = wasLiked
        post.like_count += wasLiked ? 1 : -1
        console.error('お気に入り切り替え失敗', e)
    } finally {
        post.isProcessing = false
    }
}

const deleteMessage = async (post: any) => {
    if (!confirm(`"${post.content}"\n\nこのメッセージを削除しますか？`)) return

    try {
        await deletePost(post.id)
        // 即時UIから削除
        posts.value = posts.value.filter(m => m.id !== post.id)
        router.push('/')
    } catch (e) {
        console.error('メッセージ削除失敗', e)
    }
}
</script>