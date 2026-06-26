<script setup lang="ts">
import { ref } from 'vue'
import { Search, CreditCard, Receipt, Infinity as InfinityIcon } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card, CardContent } from '@/components/ui/card'
import {
  Select, SelectContent, SelectItem, SelectTrigger, SelectValue,
} from '@/components/ui/select'
import RecordPaymentDialog from '@/components/admin/RecordPaymentDialog.vue'
import { useAdminSubscriptions } from '@/composables/useAdminSubscriptions'
import type { SubscriptionSummary, SubscriptionStatus } from '@/types/admin'

const {
  data, isLoading, isError,
  search, statusFilter, page,
} = useAdminSubscriptions()

const dialogOpen = ref(false)
const selectedGym = ref<SubscriptionSummary | null>(null)

function openRecordDialog(gym: SubscriptionSummary) {
  selectedGym.value = gym
  dialogOpen.value = true
}

function onSearchInput() {
  page.value = 1
}

function onStatusChange() {
  page.value = 1
}

const STATUS_STYLES: Record<SubscriptionStatus, string> = {
  lifetime: 'bg-brand-500/10 text-brand-600 dark:text-brand-400',
  active: 'bg-green-500/10 text-green-600 dark:text-green-400',
  expiring_soon: 'bg-yellow-500/10 text-yellow-600 dark:text-yellow-400',
  expired: 'bg-danger-500/10 text-danger-600 dark:text-danger-400',
  none: 'bg-surface-500/10 text-surface-500',
}

function statusLabel(status: SubscriptionStatus, lang: any): string {
  return {
    lifetime: lang.adminSubsStatusLifetime,
    active: lang.adminSubsStatusActive,
    expiring_soon: lang.adminSubsStatusExpiring,
    expired: lang.adminSubsStatusExpired,
    none: lang.adminSubsStatusNone,
  }[status]
}

function planLabel(plan: string | null, lang: any): string {
  if (plan === 'lifetime') return lang.adminSubsPlanLifetime
  if (plan === 'monthly') return lang.adminSubsPlanMonthly
  return '—'
}
</script>

<template>
  <section class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-1">
      <h2 class="text-xl font-semibold">{{ $lang.adminSubsTitle }}</h2>
      <p class="text-sm text-surface-400">{{ $lang.adminSubsSubtitle }}</p>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-3">
      <div class="relative flex-1">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-surface-500" />
        <Input
          v-model="search"
          :placeholder="$lang.adminSubsSearch"
          class="pl-10"
          @input="onSearchInput"
        />
      </div>
      <Select v-model="statusFilter" @update:model-value="onStatusChange">
        <SelectTrigger class="w-full sm:w-48">
          <SelectValue :placeholder="$lang.adminSubsFilterAll" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="all">{{ $lang.adminSubsFilterAll }}</SelectItem>
          <SelectItem value="active">{{ $lang.adminSubsFilterActive }}</SelectItem>
          <SelectItem value="expiring_soon">{{ $lang.adminSubsFilterExpiring }}</SelectItem>
          <SelectItem value="expired">{{ $lang.adminSubsFilterExpired }}</SelectItem>
          <SelectItem value="lifetime">{{ $lang.adminSubsFilterLifetime }}</SelectItem>
          <SelectItem value="none">{{ $lang.adminSubsFilterNone }}</SelectItem>
        </SelectContent>
      </Select>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="text-surface-400 text-sm py-8 text-center">
      {{ $lang.adminSubsLoading }}
    </div>

    <!-- Error -->
    <div v-else-if="isError" class="text-danger-500 text-sm py-8 text-center">
      {{ $lang.adminSubsError }}
    </div>

    <!-- Empty -->
    <Card v-else-if="!data?.data.length" class="border-dashed border-2">
      <CardContent class="flex flex-col items-center justify-center py-16">
        <div class="w-16 h-16 rounded-full bg-surface-200 dark:bg-surface-800 flex items-center justify-center mb-4">
          <CreditCard class="size-8 text-surface-400" />
        </div>
        <p class="text-surface-400 text-sm">{{ $lang.adminSubsEmpty }}</p>
      </CardContent>
    </Card>

    <!-- Table -->
    <div v-else class="rounded-xl border border-surface-200 dark:border-surface-800 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-surface-50 dark:bg-surface-900 text-surface-500 text-xs uppercase tracking-wide">
            <tr>
              <th class="px-4 py-3 text-left">{{ $lang.adminSubsTableGym }}</th>
              <th class="px-4 py-3 text-left hidden sm:table-cell">{{ $lang.adminSubsTablePlan }}</th>
              <th class="px-4 py-3 text-right">{{ $lang.adminSubsTableTotalPaid }}</th>
              <th class="px-4 py-3 text-left hidden md:table-cell">{{ $lang.adminSubsTableExpiry }}</th>
              <th class="px-4 py-3 text-center">{{ $lang.adminSubsTableDaysLeft }}</th>
              <th class="px-4 py-3 text-center">{{ $lang.adminSubsTableStatus }}</th>
              <th class="px-4 py-3 text-right">{{ $lang.adminSubsTableActions }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-surface-100 dark:divide-surface-800">
            <tr
              v-for="gym in data.data"
              :key="gym.id"
              class="bg-white dark:bg-surface-950 hover:bg-surface-50 dark:hover:bg-surface-900 transition-colors"
            >
              <!-- Gym -->
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <img
                    v-if="gym.logo"
                    :src="gym.logo"
                    :alt="gym.name"
                    class="w-8 h-8 rounded-full object-cover shrink-0"
                  />
                  <div
                    v-else
                    class="w-8 h-8 rounded-full bg-surface-200 dark:bg-surface-700 shrink-0 flex items-center justify-center text-xs font-semibold text-surface-500"
                  >
                    {{ gym.name.charAt(0) }}
                  </div>
                  <span class="font-medium truncate max-w-[140px]">{{ gym.name }}</span>
                </div>
              </td>

              <!-- Plan -->
              <td class="px-4 py-3 text-surface-500 hidden sm:table-cell">
                {{ planLabel(gym.plan_type, $lang) }}
              </td>

              <!-- Total paid -->
              <td class="px-4 py-3 text-right font-semibold tabular-nums">
                {{ gym.total_paid.toLocaleString() }}
                <span class="text-xs font-normal text-surface-400">{{ gym.currency }}</span>
              </td>

              <!-- Expiry -->
              <td class="px-4 py-3 text-surface-500 hidden md:table-cell">
                <span v-if="gym.subscription_status === 'lifetime'" class="inline-flex items-center gap-1">
                  <InfinityIcon class="size-4" /> {{ $lang.adminSubsStatusLifetime }}
                </span>
                <span v-else>{{ gym.expiry_date ?? '—' }}</span>
              </td>

              <!-- Days left -->
              <td class="px-4 py-3 text-center tabular-nums">
                <template v-if="gym.subscription_status === 'lifetime' || gym.days_until_expiry === null">
                  <span class="text-surface-400">—</span>
                </template>
                <template v-else-if="gym.days_until_expiry < 0">
                  <span class="text-danger-500">{{ Math.abs(gym.days_until_expiry) }}d {{ $lang.adminSubsExpiredDays }}</span>
                </template>
                <template v-else>
                  <span :class="gym.days_until_expiry <= 7 ? 'text-yellow-600 dark:text-yellow-400' : ''">
                    {{ gym.days_until_expiry }}d
                  </span>
                </template>
              </td>

              <!-- Status -->
              <td class="px-4 py-3 text-center">
                <span
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                  :class="STATUS_STYLES[gym.subscription_status]"
                >
                  {{ statusLabel(gym.subscription_status, $lang) }}
                </span>
              </td>

              <!-- Actions -->
              <td class="px-4 py-3">
                <div class="flex items-center justify-end">
                  <Button variant="outline" size="sm" class="gap-2" @click="openRecordDialog(gym)">
                    <Receipt class="size-4" />
                    <span class="hidden sm:inline">{{ $lang.adminSubsRecordPayment }}</span>
                  </Button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        v-if="data && data.last_page > 1"
        class="flex items-center justify-between px-4 py-3 border-t border-surface-100 dark:border-surface-800 bg-surface-50 dark:bg-surface-900"
      >
        <p class="text-xs text-surface-500">
          Page {{ data.current_page }} of {{ data.last_page }} ({{ data.total }})
        </p>
        <div class="flex gap-2">
          <Button variant="outline" size="sm" :disabled="page <= 1" @click="page--">Previous</Button>
          <Button variant="outline" size="sm" :disabled="page >= data.last_page" @click="page++">Next</Button>
        </div>
      </div>
    </div>

    <!-- Record Payment Dialog -->
    <RecordPaymentDialog
      v-model:open="dialogOpen"
      :gym-id="selectedGym?.id ?? null"
      :gym-name="selectedGym?.name"
    />
  </section>
</template>
