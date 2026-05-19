<script setup lang="ts">
import { RouterView } from 'vue-router'
import AppSidebar from '@/components/AppSidebar.vue'
import ThemeToggle from '@/components/ThemeToggle.vue'
import { SidebarInset, SidebarProvider, SidebarTrigger } from '@/components/ui/sidebar'
</script>

<template>
  <SidebarProvider>
    <AppSidebar role="admin" />
    <SidebarInset>
      <header class="border-b border-surface-200 dark:border-surface-800/60 px-4 py-3 bg-white dark:bg-surface-950">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <SidebarTrigger class="md:hidden" />
            <div>
              <p class="text-xs text-surface-400">{{ $lang.adminLayoutOverline }}</p>
              <h1 class="text-lg font-semibold">{{ $lang.adminLayoutTitle }}</h1>
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
