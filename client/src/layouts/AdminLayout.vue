<script setup lang="ts">
import { RouterView } from 'vue-router'
import AppSidebar from '@/components/AppSidebar.vue'
import { SidebarInset, SidebarProvider, SidebarTrigger } from '@/components/ui/sidebar'
</script>

<template>
  <SidebarProvider class="bg-surface-950 text-white">
    <AppSidebar role="admin" />
    <SidebarInset class="bg-surface-950 text-white">
      <header class="border-b border-surface-800/60 px-4 py-3">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <SidebarTrigger class="md:hidden text-surface-200 hover:text-white" />
            <div>
              <p class="text-xs text-surface-400">{{ $lang.adminLayoutOverline }}</p>
              <h1 class="text-lg font-semibold">{{ $lang.adminLayoutTitle }}</h1>
            </div>
          </div>
          <div>
            <p class="text-xs text-surface-500 hidden sm:block">{{ $lang.sidebarBrand }}</p>
          </div>
        </div>
      </header>
      <main class="px-4 py-6">
        <RouterView />
      </main>
    </SidebarInset>
  </SidebarProvider>
</template>
