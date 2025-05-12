<template>
    <main class="main grid">
        <SideNav></SideNav>
        <div class="home-container white">
            <h2 class="home-ttl">コメント</h2>
            <Message :messages="messages" :fetchMessages="() => fetchMessages()"></Message>

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
                <button class="form-btn white comment-form-btn" type="submit">コメント</button>
            </form>
        </div>
    </main>
</template>

<script setup>
import '~/assets/css/auth_form.css'
import '~/assets/css/message_detail.css'
import { useRouter, useRoute } from 'vue-router'
import { ref, onMounted } from 'vue'
import { getAuth, onAuthStateChanged } from 'firebase/auth'
import { useField, useForm, Field, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'

const { $axios } = useNuxtApp()

const route = useRoute()
const id = route.params.id

const messages = ref([])
const comments = ref([])

const validationSchema = yup.object({
    comment: yup.string().required('コメントは必須です').max(120, 'コメントは120文字以内で入力してください'),
})

const { resetForm } = useForm({
    validationSchema,
    validateOnMount: false
})

const { value: comment } = useField('comment')

onMounted(() => {
    const auth = getAuth()
    onAuthStateChanged(auth, async (user) => {
        if (user) {
            const idToken = await user.getIdToken()
            await fetchMessages(idToken)
        } else {
            console.error('ユーザーがログインしていません')
        }
    })
})

const fetchMessages = async (idToken) => {
    try {
        const { data } = await $axios.get(`/posts/${id}/comments`, {
            headers: {
                Authorization: `Bearer ${idToken}`,
            },
        })
        messages.value = data.messages
        comments.value = data.comments
    } catch (error) {
        console.error('メッセージ取得失敗', error)
    }
}

const sendComment = async () => {
    if (!comment.value.trim()) return;

    const auth = getAuth()
    const currentUser = auth.currentUser

    if (!currentUser) {
        console.error('ユーザーがログインしていません')
        return
    }

    try {
        const idToken = await currentUser.getIdToken() // トークン取得
        console.log('取得したトークン:', idToken)
        
        const res = await $axios.post(`/posts/${id}/comments`, {
            comment: comment.value,
        }, {
            headers: {
                Authorization: `Bearer ${idToken}`,
            },
        })
        resetForm()
        await fetchMessages(idToken)

    } catch (error) {
        console.error('送信に失敗しました', error)
    }
}
</script>