<template>
    <div class="side-nav__container flex">
        <div class="side-nav__img-wrap">
            <img class="side-nav__img" src="/icons/logo.png" alt="share">
        </div>

        <nav class="side-var-nav__menu">
            <div class="side-nav__list flex white">
                <div class="side-nav__home-img-wrap">
                    <img class="side-nav__img" src="/icons/home.png" alt="home">
                </div>
                <NuxtLink class="side-var-nav__link" to ="/">
                    ホーム
                </NuxtLink>
            </div>
            <form class="side-nav__logout-form flex" @submit.prevent="handleLogout">
                <div class="side-nav__logout-img-wrap">
                    <img class="side-nav__img" src="/icons/logout.png" alt="home">
                </div>
                <button class="side-nav__logout-button white" type="submit">
                    ログアウト
                </button>
            </form>
        </nav>

        <form class="post-form flex white" @submit.prevent="sendMessage">
            <h2 class="post-form__ttl">シェア</h2>
            <div class="post-form__input-group">
                <textarea class="post-form__textarea white" rows="8" v-model="content"></textarea>
                <div class="error-message">
                    <ErrorMessage name="content" />
                </div>
            </div>
            <button class="form-btn white post-form-btn" type="submit" :disabled="isRunning">{{ isRunning ? '送信中...' : 'シェアする' }}</button>
        </form>
    </div>
</template>

<script setup lang="ts">
import '~/assets/css/side_nav.css'
import { useField, useForm, Field, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'
import { useRouter } from 'vue-router'
import { getAuth } from 'firebase/auth'
import { ref, onMounted } from 'vue'
import { useSingleClick } from '~/composables/useSingleClick'

const { $axios } = useNuxtApp()
const { run, isRunning } = useSingleClick()

const emit = defineEmits<{
    (e: 'onMessagePosted', post: any): void
}>()
const router = useRouter()
const { logout } = useAuth()

const validationSchema = yup.object({
    content: yup
        .string()
        .required('投稿メッセージは必須です')
        .max(120, '投稿メッセージは120文字以内で入力してください'),
})

const { handleSubmit, validate, resetForm } = useForm({
    validationSchema,
    validateOnMount: false
})

const { value: content } = useField('content')

const handleLogout = async () => {
    if (!confirm('ログアウトしますか？')) return

    try {
        await logout()
    } catch (e) {
        console.error('ログアウトエラー:', e)
    }
}

const sendMessage = () => {
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
            const res = await $axios.post('/posts', {
                content: content.value,
            })

            const newPost = res.data.post
            resetForm()
            emit('onMessagePosted', newPost)

        } catch (error) {
            console.error('送信に失敗しました', error)
        }
    })
}
</script>