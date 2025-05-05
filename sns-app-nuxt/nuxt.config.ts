// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },

  ssr: false,

  css: [
    '~/assets/css/reset.css',
    '~/assets/css/common.css',
  ],

  plugins: ['~/plugins/firebase.client.ts'],
})
