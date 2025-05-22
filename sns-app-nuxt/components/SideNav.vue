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
import { useField, useForm, Field, ErrorMessage } from 'vee-validate'
import { useRouter } from 'vue-router'
import { ref, onMounted } from 'vue'
import { useSingleClick } from '~/composables/useSingleClick'
import { useCurrentUser } from '~/composables/useCurrentUser'
import { createMessageSchema } from '~/composables/validations/messageValidation'
import { useAuth } from '~/composables/useAuth'
import { usePostApi } from '~/composables/api/usePostApi'
import { usePostFormStore } from '~/stores/postFormStore'

const { createPost } = usePostApi()
const postFormStore = usePostFormStore()
const { run, isRunning } = useSingleClick()

const { currentUser, currentUid } = useCurrentUser()

const emit = defineEmits<{
    (e: 'onMessagePosted', post: any): void
}>()
const router = useRouter()
const { logout } = useAuth()

const { handleSubmit, validate, resetForm } = useForm({
    validationSchema: createMessageSchema('content', '投稿メッセージ'),
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

onMounted(() => {
    const savedPost = postFormStore.getContent()
    if (savedPost) {
        content.value = savedPost
    }
})

watch(content, (newVal) => {
    postFormStore.setContent(newVal)
})

const sendMessage = () => {
    run(async () => {
        const result = await validate()
        if (!result.valid) {
            return // バリデーションエラーがある場合は送信中止
        }

        if (!currentUser) {
            console.error('ユーザーがログインしていません')
            return
        }

        try {
            const newPost = await createPost(content.value)
            resetForm()
            emit('onMessagePosted', newPost)
            postFormStore.clearContent()

        } catch (error) {
            console.error('送信に失敗しました', error)
        }
    })
}
</script>