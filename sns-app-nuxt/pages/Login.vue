<template>
    <AuthHeader></AuthHeader>
    <main>
        <div class="form-container radius5 flex">
            <h1 class="form-header">ログイン</h1>
            <form class="form-group flex" @submit.prevent="handleLogin">
                <div class="form-group__item">
                    <input class="form-group__item-input" type="text" v-model="email" placeholder="メールアドレス">
                    <div class="error-message">
                        <ErrorMessage name="email" />
                    </div>
                </div>

                <div class="form-group__item">
                    <input class="form-group__item-input" type="password" v-model="password" placeholder="パスワード">
                    <div class="error-message">
                        <ErrorMessage name="password" />
                    </div>
                </div>

                <button class="form-btn white" type="submit">
                    ログイン
                </button>
            </form>
        </div>
    </main>
</template>

<script setup>
import '~/assets/css/auth_form.css'

import { useField, useForm, ErrorMessage, Field } from 'vee-validate'
import * as yup from 'yup'
import { ref } from 'vue'
import { useAuth } from '~/composables/useAuth'

const validationSchema = yup.object({
    email: yup.string().required('メールアドレスは必須です'),
    password: yup.string().required('パスワードは必須です')
})

useForm({
    validationSchema,
    validateOnMount: false
})

const { value: email } = useField('email')
const { value: password } = useField('password')

const { login } = useAuth()

const handleLogin = async () => {
    try {
        await login(email.value, password.value)
        alert('ログイン成功')
    } catch (e) {
        console.error(e)
        alert('ログインに失敗しました。再度お試しください。')
    }
}
</script>