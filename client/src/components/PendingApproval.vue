<script setup lang="ts">
import { Clock, LogOut } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import ThemeToggle from '@/components/ThemeToggle.vue'
import { useAuthStore } from '@/store/authStore'

const authStore = useAuthStore()

function logout() {
  authStore.logout()
}
</script>

<template>
  <div class="min-h-svh bg-surface-50 dark:bg-surface-950 text-surface-900 dark:text-white flex flex-col items-center justify-center px-6">
    <div class="absolute top-4 right-4">
      <ThemeToggle />
    </div>
    <div class="max-w-md text-center space-y-5">
      <div class="mx-auto w-16 h-16 rounded-full bg-blue-500/10 flex items-center justify-center">
        <Clock class="size-8 text-blue-500" />
      </div>
      <h1 class="text-2xl font-semibold">{{ $lang.pendingApprovalTitle }}</h1>
      <p class="text-sm text-surface-500 dark:text-surface-400">{{ $lang.pendingApprovalBody }}</p>
      <Button variant="outline" class="gap-2" @click="logout">
        <LogOut class="size-4" />
        {{ $lang.pendingApprovalLogout }}
      </Button>
    </div>
  </div>
</template>
