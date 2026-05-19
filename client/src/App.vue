<script setup lang="ts">
import { RouterView } from 'vue-router'
import { useThemeStore } from '@/store/themeStore'

// Initialize theme store
const themeStore = useThemeStore()
</script>

<template>
  <RouterView />
</template>

<style scoped>
</style>
