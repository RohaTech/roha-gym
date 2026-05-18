<script setup lang="ts">
import { Building2, BarChart3, CreditCard, ShieldCheck } from 'lucide-vue-next'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'

const stats = [
  { key: 'adminStatGyms', icon: Building2, valueKey: 'adminStatGymsValue' },
  { key: 'adminStatRevenue', icon: CreditCard, valueKey: 'adminStatRevenueValue' },
  { key: 'adminStatActivePlans', icon: BarChart3, valueKey: 'adminStatActivePlansValue' },
  { key: 'adminStatCompliance', icon: ShieldCheck, valueKey: 'adminStatComplianceValue' },
]
</script>

<template>
  <section class="space-y-6">
    <div>
      <h2 class="text-xl font-semibold">{{ $lang.adminDashboardTitle }}</h2>
      <p class="text-sm text-surface-400">{{ $lang.adminDashboardSubtitle }}</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <Card v-for="stat in stats" :key="stat.key" class="border-surface-800 bg-surface-900/60">
        <CardHeader class="flex flex-row items-center justify-between">
          <CardTitle class="text-sm text-surface-300">{{ $lang[stat.key] }}</CardTitle>
          <component :is="stat.icon" class="size-4 text-surface-300" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-semibold text-white">{{ $lang[stat.valueKey] }}</div>
        </CardContent>
      </Card>
    </div>

    <Card class="border-surface-800 bg-surface-900/60">
      <CardHeader>
        <CardTitle class="text-base">{{ $lang.adminDashboardActivityTitle }}</CardTitle>
      </CardHeader>
      <CardContent class="text-sm text-surface-400">
        {{ $lang.adminDashboardActivityBody }}
      </CardContent>
    </Card>
  </section>
</template>
