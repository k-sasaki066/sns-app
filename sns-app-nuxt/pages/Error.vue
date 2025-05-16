<template>
    <div class="flex items-center justify-center min-h-screen bg-gray-100 p-8 text-center">
        <div class="error-wrap white">
            <template v-if="errorStore.status === 404">
                <h1 class="error-status">404</h1>
                <p class="error-title">Page Not Found</p>
                <p class="error-text">
                    申し訳ございません
                    <br>
                    お探しのページは見つかりませんでした
                </p>
            </template>

            <template v-else-if="errorStore.status === 500">
                <h1 class="error-status">500</h1>
                <p class="error-title">Internal Server Error</p>
                <p class="error-text">
                    サーバー内部でエラーが発生しました
                    <br>
                    再度お試しください
                </p>
            </template>

            <template v-else>
                <h1 class="error-status">{{ errorStore.status }}</h1>
                <p class="error-text">{{ errorStore.message }}</p>
            </template>

            <NuxtLink to="/" class="form-btn back-home__btn">ホームに戻る</NuxtLink>
        </div>
    </div>
</template>

<script setup>
import '~/assets/css/error.css'
import '~/assets/css/auth_form.css'

import { useErrorStore } from '~/stores/error'
const errorStore = useErrorStore()
</script>