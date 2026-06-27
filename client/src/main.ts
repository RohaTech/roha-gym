import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import { VueQueryPlugin } from '@tanstack/vue-query'
import VueApexCharts from 'vue3-apexcharts'

import App from './App.vue'
import router from './router'
import { useLanguageStore } from './store/languageStore'

const app = createApp(App)

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

app.use(pinia)
app.use(VueQueryPlugin)
app.use(router)
app.use(VueApexCharts)

const languageStore = useLanguageStore()

Object.defineProperty(app.config.globalProperties, '$lang', {
  get() {
    const languageStore = useLanguageStore()
    return languageStore.translations
  },
})

app.mount('#app')
