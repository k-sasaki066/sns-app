<template>
    <div class="message-wrap flex">
        <div class="message-inner flex">
            <p class="message__user-name">usernameusernameusername</p>
            <div class="message__button-group flex">
                <div class="message__favorite-img-wrap">
                    <img class="message__favorite-img" src="/icons/heart.png" alt="heart">
                </div>
                <span class="message__favorite-count">100</span>
                <form class="message__delete-form" onsubmit="return confirm('このメッセージを削除しますか？');">
                    <button class="message__delete-form-button" type="submit">
                        <img class="message__delete-form-img" src="/icons/cross.png" alt="delete">
                    </button>
                </form>
            </div>
            <div class="message__detail-button">
                <NuxtLink class="message__detail-button-link" :to="`/posts/${id}`">
                    <img class="message__detail-button-img" src="/icons/detail.png" alt="detail">
                </NuxtLink>
            </div>
        </div>
        <p class="message__user-name">メッセージ内容はここに表示メッセージ内容はここに表示メッセージ内容はここに表示</p>
    </div>
</template>

<script setup>
import '~/assets/css/message.css'

const route = useRoute()

const hideDetailButton = computed(() => route.path === '/posts')
</script>