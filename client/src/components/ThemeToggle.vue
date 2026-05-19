<script setup lang="ts">
import { Moon, Sun } from 'lucide-vue-next'
import { useThemeStore } from '@/store/themeStore'
import { Button } from '@/components/ui/button'

const themeStore = useThemeStore()

function toggleTheme() {
  themeStore.toggleTheme()
}
</script>

<template>
  <Button 
    variant="ghost" 
    size="icon" 
    @click="toggleTheme"
    class="relative"
    title="Toggle theme"
  >
    <Sun
      class="h-5 w-5 rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0"
    />
    <Moon
      class="absolute h-5 w-5 rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100"
    />
    <span class="sr-only">Toggle theme</span>
  </Button>
</template>
