<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { Button } from '@/components/ui/button'
</script>

<template>
  <div class="min-h-svh bg-surface-950 text-white flex items-center justify-center px-6">
    <div class="max-w-md text-center space-y-4">
      <h1 class="text-2xl font-semibold">{{ $lang.forbiddenTitle }}</h1>
      <p class="text-sm text-surface-400">{{ $lang.forbiddenBody }}</p>
      <Button as-child class="bg-brand-500 text-white">
        <RouterLink to="/">{{ $lang.forbiddenCta }}</RouterLink>
      </Button>
    </div>
  </div>
</template>
