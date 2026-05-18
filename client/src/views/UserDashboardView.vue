<script setup lang="ts">
import { BarChart3, CalendarCheck, IdCard, Users } from 'lucide-vue-next'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'

const stats = [
  { key: 'userStatMembers', icon: Users, valueKey: 'userStatMembersValue' },
  { key: 'userStatCheckIns', icon: CalendarCheck, valueKey: 'userStatCheckInsValue' },
  { key: 'userStatMemberships', icon: IdCard, valueKey: 'userStatMembershipsValue' },
  { key: 'userStatAnalytics', icon: BarChart3, valueKey: 'userStatAnalyticsValue' },
]
</script>

<template>
  <section class="space-y-6">
    <div>
      <h2 class="text-xl font-semibold">{{ $lang.userDashboardTitle }}</h2>
      <p class="text-sm text-surface-400">{{ $lang.userDashboardSubtitle }}</p>
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
        <CardTitle class="text-base">{{ $lang.userDashboardFocusTitle }}</CardTitle>
      </CardHeader>
      <CardContent class="text-sm text-surface-400">
        {{ $lang.userDashboardFocusBody }}
      </CardContent>
    </Card>
  </section>
</template>
