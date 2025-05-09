<template>
    <div class="message-wrap flex" v-for="(message, index) in messages" :key="index">
        <div class="message-inner flex">
            <p class="message__user-name">{{ message.user && message.user.name ? message.user.name : '匿名' }}</p>
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
                <NuxtLink class="message__detail-button-link">
                    <img class="message__detail-button-img" src="/icons/detail.png" alt="detail">
                </NuxtLink>
            </div>
        </div>
        <p class="message__user-name">{{ message.content }}</p>
    </div>
</template>

<script setup lang="ts">
import '~/assets/css/message.css'
import { computed, watch, ref, onMounted, defineProps } from 'vue'
import { getAuth } from 'firebase/auth'

const { $axios } = useNuxtApp()

const props = defineProps<{
    messages: any[]
    fetchMessages: () => void
}>()
const messages = ref([...props.messages])

const route = useRoute()
const hideDetailButton = computed(() => route.path === '/posts')

onMounted(() => {
    const auth = getAuth()
    props.fetchMessages()
})

watch(() => props.messages, (newMessages) => {
    messages.value = newMessages
})
</script>