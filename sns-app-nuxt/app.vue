<template>
    <div>
        <NuxtRouteAnnouncer />
        <LoadingSpinner />
        <NuxtPage />
    </div>
</template>

<script setup lang="ts">
    import '~/assets/css/reset.css'
    import '~/assets/css/common.css'
    import '~/assets/css/auth_form.css'
    import '~/assets/css/index.css'
    import '~/assets/css/side_nav.css'
    import '~/assets/css/message.css'
    import '~/assets/css/message_detail.css'
    import LoadingSpinner from '~/components/LoadingSpinner.vue'
    import { useAuthStore } from '~/stores/useAuthStore'

    const authStore = useAuthStore()

    authStore.initAuth()
</script>
