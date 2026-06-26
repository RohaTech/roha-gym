<script setup lang="ts">
import { computed } from 'vue'
import { RouterView } from 'vue-router'
import AppSidebar from '@/components/AppSidebar.vue'
import ThemeToggle from '@/components/ThemeToggle.vue'
import PendingApproval from '@/components/PendingApproval.vue'
import { SidebarInset, SidebarProvider, SidebarTrigger } from '@/components/ui/sidebar'
import { useAuthStore } from '@/store/authStore'

const authStore = useAuthStore()

// 2 = pending approval: gym owners awaiting admin approval get a limited state
// instead of the full dashboard.
const isPending = computed(() => authStore.user?.status === 2)
</script>

<template>
  <PendingApproval v-if="isPending" />
  <SidebarProvider v-else>
    <AppSidebar role="user" />
    <SidebarInset>
      <header class="border-b border-surface-200 dark:border-surface-800/60 px-4 py-3 bg-white dark:bg-surface-950">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <SidebarTrigger class="md:hidden" />
            <div>
              <p class="text-xs text-surface-400">{{ $lang.userLayoutOverline }}</p>
              <h1 class="text-lg font-semibold">{{ $lang.userLayoutTitle }}</h1>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <ThemeToggle />
            <p class="text-xs text-surface-500 hidden sm:block">{{ $lang.sidebarBrand }}</p>
          </div>
        </div>
      </header>
      <main class="px-4 py-6 bg-surface-50 dark:bg-surface-950 min-h-screen">
        <RouterView />
      </main>
    </SidebarInset>
  </SidebarProvider>
</template>
