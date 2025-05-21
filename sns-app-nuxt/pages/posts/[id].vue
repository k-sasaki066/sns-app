<template>
    <main class="main grid">
        <SideNav></SideNav>
        <div class="home-container white">
            <h2 class="home-ttl">コメント</h2>
            <Message :posts="[posts]" :fetchMessages="() => fetchMessages()"></Message>

            <div class="comment-wrap">
                <h3 class="comment--ttl">コメント</h3>
                <div class="comment-group">
                    <div class="comment-item flex" v-for="comment in comments" :key="comment.id">
                        <p class="comment-name">{{ comment.user.name }}</p>
                        <p class="comment-text">{{ comment.comment }}</p>
                    </div>
                </div>
            </div>

            <form class="comment-form flex white" @submit.prevent="sendComment">
                <div class="comment-form__input-group">
                    <textarea class="comment-form__textarea white" rows="1" v-model="comment"></textarea>
                    <div class="error-message">
                        <ErrorMessage name="comment" />
                    </div>
                </div>
                <button class="form-btn white comment-form-btn" type="submit" :disabled="isRunning">{{ isRunning ? '送信中...' : 'コメント' }}</button>
            </form>
        </div>
    </main>
</template>

<script setup>
import '~/assets/css/auth_form.css'
import '~/assets/css/message_detail.css'
import { useRouter, useRoute } from 'vue-router'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { getAuth, onAuthStateChanged } from 'firebase/auth'
import { useField, useForm, Field, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'
import { useSingleClick } from '~/composables/useSingleClick'
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { useLoadingStore } from '~/stores/useLoadingStore'
import { useCommentFormStore } from '~/stores/commentFormStore'


const config = useRuntimeConfig()
let channel = null

const { $axios } = useNuxtApp()
const { run, isRunning } = useSingleClick()

const route = useRoute()
const id = route.params.id

const router = useRouter()

const loadingStore = useLoadingStore()

const posts = ref([])
const comments = ref([])

const commentFormStore = useCommentFormStore()

const validationSchema = yup.object({
    comment: yup
        .string()
        .required('コメントは必須です')
        .max(120, 'コメントは120文字以内で入力してください'),
})

const { handleSubmit, validate, resetForm } = useForm({
    validationSchema,
    validateOnMount: false
})

//const { value: comment } = useField('comment')
const { value: comment } = useField('comment', undefined, {
    validateOnMount: false,
})

onMounted(() => {
    const auth = getAuth()
    const savedComment = commentFormStore.getContent(id)
    if (savedComment) {
        comment.value = savedComment
    }
    //comment.value = commentFormStore.getContent(id)
    onAuthStateChanged(auth, async (user) => {
        if (!user) {
            await router.push('/login')
            return
        }
        await fetchMessages()

        if (!window.Echo) {
            window.Pusher = Pusher
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: config.public.PUSHER_APP_KEY,
                cluster: config.public.PUSHER_APP_CLUSTER,
                forceTLS: true,
            })
        }

        if (!channel) {
            console.log('新しいチャンネルを登録')
            const channelName = 'post.' + id
            channel = window.Echo.channel(channelName)
            channel.listen('CommentSent', (event) => {
                const newComment = event.comment
                const exists = comments.value.some(
                c => String(c.id) === String(newComment.id)
                )
                if (!exists) {
                    comments.value.push(newComment)
                    console.log('新しいコメントを追加しました')
                } else {
                    console.log('重複コメントをスキップ:', newComment)
                }
            })
        }
    })
})

onBeforeUnmount(() => {
    if (channel) {
        channel.stopListening('CommentSent')
        window.Echo.leave('post.' + id)
        channel = null
    }
})

const fetchMessages = async () => {
    try {
        loadingStore.setLoading(true)
        const { data } = await $axios.get(`/posts/${id}/comments`)
        posts.value = data.posts
        comments.value = data.comments
    } catch (error) {
        console.error('メッセージ取得失敗', error)
    } finally {
        loadingStore.setLoading(false) // ← ここでローディング終了
    }
}

watch(comment, (newVal) => {
    commentFormStore.setContent(id, newVal)
})

const sendComment = () => {
    run(async () => {
        const result = await validate()
        if (!result.valid) {
            return // バリデーションエラーがある場合は送信中止
        }

        const auth = getAuth()
        const currentUser = auth.currentUser

        if (!currentUser) {
            console.error('ユーザーがログインしていません')
            return
        }

        try {
            const res = await $axios.post(`/posts/${id}/comments`, {
                comment: comment.value,
            })
            //comments.value.push(res.data.comment)
            resetForm()
            commentFormStore.setContent(id, '')

        } catch (error) {
            console.error('送信に失敗しました', error)
        }
    })
}
</script>