<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useQuery } from '@tanstack/vue-query'
import { Calendar, Phone, User as UserIcon, AlertCircle, Filter } from 'lucide-vue-next'
import axiosInstance from '@/api/axiosInstance'
import { storageUrl } from '@/constants'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Label } from '@/components/ui/label'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import RenewMembershipModal from '@/components/membership/RenewMembershipModal.vue'

interface MembershipType {
  id: number
  name: string
  duration_days: number
}

interface Member {
  id: number
  full_name: string
  phone: string
  unique_code: string
  photo_path: string | null
  gender: string | null
  start_date: string
  expiry_date: string
  status: 'active' | 'expired' | 'suspended'
  notes: string | null
  membership_type: MembershipType
  membership_type_id: number
  created_at: string
}

const router = useRouter()

// Filter state: 'all', 'expiring', 'expired'
const filterType = ref<'all' | 'expiring' | 'expired'>('all')

// Renewal modal state
const renewModalOpen = ref(false)
const selectedMemberForRenewal = ref<Member | null>(null)

// Fetch members based on filter
const {
  data: members,
  isLoading,
  refetch,
} = useQuery({
  queryKey: ['members-expiring', filterType],
  queryFn: async () => {
    const response = await axiosInstance.get<Member[]>(
      `/members/expiring?days=7&filter=${filterType.value}`,
    )
    return response.data
  },
})

// Computed stats
const expiringCount = computed(() => {
  if (!members.value) return 0
  return members.value.filter((m) => {
    const days = getDaysUntilExpiry(m.expiry_date)
    return days >= 0
  }).length
})

const expiredCount = computed(() => {
  if (!members.value) return 0
  return members.value.filter((m) => {
    const days = getDaysUntilExpiry(m.expiry_date)
    return days < 0
  }).length
})

function formatDate(date: string) {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

function getDaysUntilExpiry(expiryDate: string) {
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  const expiry = new Date(expiryDate)
  expiry.setHours(0, 0, 0, 0)
  const diffTime = expiry.getTime() - today.getTime()
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  return diffDays
}

function getUrgencyColor(days: number) {
  if (days < 0) return 'text-red-600 bg-red-500/10'
  if (days === 0) return 'text-red-500 bg-red-500/10'
  if (days <= 3) return 'text-orange-500 bg-orange-500/10'
  return 'text-yellow-500 bg-yellow-500/10'
}

function getStatusText(days: number) {
  if (days < 0) {
    const absDays = Math.abs(days)
    return `Expired ${absDays} ${absDays === 1 ? 'day' : 'days'} ago`
  }
  if (days === 0) return 'Expires today'
  return `${days} ${days === 1 ? 'day' : 'days'} left`
}

function openRenewModal(member: Member) {
  selectedMemberForRenewal.value = member
  renewModalOpen.value = true
}

function handleRenewalComplete() {
  refetch()
}
</script>

<template>
  <div class="p-6 space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-display font-bold tracking-tight">Expiring & Expired Members</h1>
      <p class="text-surface-400 mt-1">
        Members expiring in the next 7 days or expired in the last 30 days
      </p>
    </div>

    <!-- Filter and Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Filter Dropdown -->
      <Card class="border-surface-200 dark:border-surface-800">
        <CardContent class="py-4">
          <div class="space-y-2">
            <Label class="text-sm font-medium flex items-center gap-2">
              <Filter class="w-4 h-4" />
              Filter View
            </Label>
            <Select v-model="filterType">
              <SelectTrigger>
                <SelectValue placeholder="Select filter" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Members</SelectItem>
                <SelectItem value="expiring">Expiring Soon</SelectItem>
                <SelectItem value="expired">Recently Expired</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </CardContent>
      </Card>

      <!-- Stats Cards -->
      <Card class="bg-gradient-to-br from-yellow-500/10 to-orange-500/10 border-yellow-500/20">
        <CardContent class="py-4">
          <div class="flex items-center gap-3">
            <div
              class="w-10 h-10 rounded-lg bg-yellow-500/20 flex items-center justify-center flex-shrink-0"
            >
              <AlertCircle class="w-5 h-5 text-yellow-500" />
            </div>
            <div>
              <p class="text-2xl font-bold">{{ isLoading ? '...' : expiringCount }}</p>
              <p class="text-xs text-surface-400">Expiring Soon</p>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card class="bg-gradient-to-br from-red-500/10 to-red-600/10 border-red-500/20">
        <CardContent class="py-4">
          <div class="flex items-center gap-3">
            <div
              class="w-10 h-10 rounded-lg bg-red-500/20 flex items-center justify-center flex-shrink-0"
            >
              <Calendar class="w-5 h-5 text-red-500" />
            </div>
            <div>
              <p class="text-2xl font-bold">{{ isLoading ? '...' : expiredCount }}</p>
              <p class="text-xs text-surface-400">Recently Expired</p>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card class="border-surface-200 dark:border-surface-800">
        <CardContent class="py-4">
          <div class="flex items-center gap-3">
            <div
              class="w-10 h-10 rounded-lg bg-brand-500/20 flex items-center justify-center flex-shrink-0"
            >
              <UserIcon class="w-5 h-5 text-brand-500" />
            </div>
            <div>
              <p class="text-2xl font-bold">
                {{ isLoading ? '...' : members?.length || 0 }}
              </p>
              <p class="text-xs text-surface-400">Total Showing</p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <Card v-for="i in 3" :key="i" class="animate-pulse">
        <CardHeader>
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-surface-800"></div>
            <div class="flex-1 space-y-2">
              <div class="h-5 bg-surface-800 rounded w-3/4"></div>
              <div class="h-4 bg-surface-800 rounded w-1/2"></div>
            </div>
          </div>
        </CardHeader>
      </Card>
    </div>

    <!-- Empty State -->
    <Card v-else-if="!members || members.length === 0" class="border-dashed border-2">
      <CardContent class="flex flex-col items-center justify-center py-16">
        <div class="w-16 h-16 rounded-full   flex items-center justify-center mb-4">
          <Calendar class="w-8 h-8 text-surface-400" />
        </div>
        <h3 class="text-xl font-semibold mb-2">No Members Found</h3>
        <p class="text-surface-400 text-center max-w-md">
          {{
            filterType === 'expiring'
              ? 'No memberships are expiring in the next 7 days.'
              : filterType === 'expired'
                ? 'No memberships expired in the last 30 days.'
                : 'No expiring or expired memberships found.'
          }}
        </p>
      </CardContent>
    </Card>

    <!-- Members List -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      <Card
        v-for="member in members"
        :key="member.id"
        :class="[
          'transition-all hover:shadow-lg',
          getDaysUntilExpiry(member.expiry_date) < 0
            ? 'hover:border-red-500/50 border-red-500/20'
            : 'hover:border-yellow-500/50 border-yellow-500/20',
        ]"
      >
        <CardContent class="p-4">
          <!-- Header with Avatar and Name -->
          <div class="flex items-start gap-3 mb-4">
            <!-- Avatar -->
            <div
              class="w-16 h-16 rounded-full bg-surface-800 flex items-center justify-center flex-shrink-0 overflow-hidden ring-2 ring-surface-700"
            >
              <img
                v-if="member.photo_path"
                :src="storageUrl(member.photo_path)"
                :alt="member.full_name"
                class="w-full h-full object-cover"
              />
              <UserIcon v-else class="w-8 h-8 text-surface-400" />
            </div>

            <!-- Name and Status Badge -->
            <div class="flex-1 min-w-0">
              <h3 class="font-semibold text-lg truncate mb-1">{{ member.full_name }}</h3>
                <span class="font-mono text-xs bg-surface-800 text-white px-2.5 py-1 rounded">
                {{ member.unique_code }}
              </span>
              <span
                :class="[
                  'inline-flex px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap',
                  getUrgencyColor(getDaysUntilExpiry(member.expiry_date)),
                ]"
              >
                {{ getStatusText(getDaysUntilExpiry(member.expiry_date)) }}
              </span>
            </div>
          </div>

          <!-- Membership Type Badge -->
          <div class="mb-3">
            <div
              class="inline-flex items-center px-3 py-1.5 rounded-md bg-brand-500/10 border border-brand-500/20"
            >
              <span class="text-sm font-medium text-brand-500">
                {{ member.membership_type.name }}
              </span>
            </div>
          </div>

          <!-- Details Section -->
          <div class="space-y-2.5 mb-4">
            <!-- Phone -->
            <div class="flex items-center gap-2.5 text-sm">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0">
                <Phone class="w-4 h-4 text-surface-400" />
              </div>
              <span class="text-foreground/80 truncate">{{ member.phone }}</span>
            </div>

            <!-- Expiry Date -->
            <div class="flex items-center gap-2.5 text-sm">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0">
                <Calendar class="w-4 h-4 text-surface-400" />
              </div>
              <div class="flex-1 min-w-0">
                <span class="text-surface-400 text-xs">
                  {{ getDaysUntilExpiry(member.expiry_date) < 0 ? 'Expired on' : 'Expires on' }}
                </span>
                <p class="text-foreground/80 font-medium">{{ formatDate(member.expiry_date) }}</p>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <Button variant="default" size="sm" class="flex-1" @click="openRenewModal(member)">
            Renew
          </Button>
        </CardContent>
      </Card>
    </div>

    <!-- Renewal Modal -->
    <RenewMembershipModal
      :member="selectedMemberForRenewal"
      :open="renewModalOpen"
      @update:open="renewModalOpen = $event"
      @renewed="handleRenewalComplete"
    />
  </div>
</template>
