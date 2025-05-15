// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },

  ssr: false,

  css: [
    '~/assets/css/reset.css',
    '~/assets/css/common.css',
  ],

  plugins: [
    '~/plugins/firebase.client.ts',
    '~/plugins/axios.ts',
    '~/plugins/pinia.ts',
  ],

  runtimeConfig: {
    public: {
      FIREBASE_API_KEY: process.env.NUXT_PUBLIC_FIREBASE_API_KEY,
      FIREBASE_AUTH_DOMAIN: process.env.NUXT_PUBLIC_FIREBASE_AUTH_DOMAIN,
      FIREBASE_PROJECT_ID: process.env.NUXT_PUBLIC_FIREBASE_PROJECT_ID,
      FIREBASE_STORAGE_BUCKET: process.env.NUXT_PUBLIC_FIREBASE_STORAGE_BUCKET,
      FIREBASE_MESSAGING_SENDER_ID: process.env.NUXT_PUBLIC_FIREBASE_MESSAGING_SENDER_ID,
      FIREBASE_APP_ID: process.env.NUXT_PUBLIC_FIREBASE_APP_ID,
      PUSHER_APP_KEY: process.env.NUXT_PUBLIC_PUSHER_APP_KEY,
      PUSHER_APP_CLUSTER: process.env.NUXT_PUBLIC_PUSHER_APP_CLUSTER,
    }
  }
})
