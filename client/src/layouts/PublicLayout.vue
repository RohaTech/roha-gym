<script setup lang="ts">
import { RouterView } from 'vue-router'
import ThemeToggle from '@/components/ThemeToggle.vue'
</script>

<template>
  <div class="min-h-svh bg-surface-50 dark:bg-surface-950 text-surface-900 dark:text-surface-50">
    <!-- Theme Toggle for Public Pages -->
    <div class="fixed top-4 right-4 z-50">
      <ThemeToggle />
    </div>
    <RouterView />
  </div>
</template>
