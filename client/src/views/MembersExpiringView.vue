<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useQuery } from '@tanstack/vue-query'
import { Calendar, Phone, User as UserIcon, AlertCircle } from 'lucide-vue-next'
import axiosInstance from '@/api/axiosInstance'
import { storageUrl } from '@/constants'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'

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
  created_at: string
}

const router = useRouter()

// Fetch expiring members (7 days)
const { data: expiringMembers, isLoading } = useQuery({
  queryKey: ['members-expiring'],
  queryFn: async () => {
    const response = await axiosInstance.get<Member[]>('/members/expiring?days=7')
    return response.data
  },
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
  const expiry = new Date(expiryDate)
  const diffTime = expiry.getTime() - today.getTime()
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  return diffDays
}

function getUrgencyColor(days: number) {
  if (days <= 0) return 'text-red-500 bg-red-500/10'
  if (days <= 3) return 'text-orange-500 bg-orange-500/10'
  return 'text-yellow-500 bg-yellow-500/10'
}
</script>

<template>
  <div class="p-6 space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-display font-bold tracking-tight">Expiring Soon</h1>
      <p class="text-surface-400 mt-1">Members with 7 days or less remaining on their membership</p>
    </div>

    <!-- Stats Card -->
    <Card class="bg-gradient-to-r from-yellow-500/10 to-orange-500/10 border-yellow-500/20">
      <CardContent class="flex items-center gap-4 py-6">
        <div
          class="w-12 h-12 rounded-full bg-yellow-500/20 flex items-center justify-center flex-shrink-0"
        >
          <AlertCircle class="w-6 h-6 text-yellow-500" />
        </div>
        <div>
          <p class="text-2xl font-bold">
            {{ isLoading ? '...' : expiringMembers?.length || 0 }}
          </p>
          <p class="text-sm text-surface-400">
            Member{{ (expiringMembers?.length || 0) !== 1 ? 's' : '' }} expiring in the next 7 days
          </p>
        </div>
      </CardContent>
    </Card>

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
    <Card v-else-if="!expiringMembers || expiringMembers.length === 0" class="border-dashed border-2">
      <CardContent class="flex flex-col items-center justify-center py-16">
        <div
          class="w-16 h-16 rounded-full bg-surface-800/50 flex items-center justify-center mb-4"
        >
          <Calendar class="w-8 h-8 text-surface-400" />
        </div>
        <h3 class="text-xl font-semibold mb-2">No Expiring Memberships</h3>
        <p class="text-surface-400 text-center max-w-md">
          Great! No memberships are expiring in the next 7 days.
        </p>
      </CardContent>
    </Card>

    <!-- Expiring Members List -->
    <div v-else class="space-y-4">
      <Card
        v-for="member in expiringMembers"
        :key="member.id"
        class="hover:border-yellow-500/50 transition-colors"
      >
        <CardContent class="py-4">
          <div class="flex items-center gap-4">
            <!-- Avatar -->
            <div
              class="w-14 h-14 rounded-full bg-surface-800 flex items-center justify-center flex-shrink-0 overflow-hidden"
            >
              <img
                v-if="member.photo_path"
                :src="storageUrl(member.photo_path)"
                :alt="member.full_name"
                class="w-full h-full object-cover"
              />
              <UserIcon v-else class="w-7 h-7 text-surface-400" />
            </div>

            <!-- Member Info -->
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                  <h3 class="font-semibold text-lg truncate">{{ member.full_name }}</h3>
                  <p class="text-sm text-surface-400">{{ member.membership_type.name }}</p>
                </div>

                <!-- Urgency Badge -->
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-sm font-medium whitespace-nowrap',
                    getUrgencyColor(getDaysUntilExpiry(member.expiry_date)),
                  ]"
                >
                  {{
                    getDaysUntilExpiry(member.expiry_date) === 0
                      ? 'Expires today'
                      : getDaysUntilExpiry(member.expiry_date) < 0
                        ? 'Expired'
                        : `${getDaysUntilExpiry(member.expiry_date)} days left`
                  }}
                </span>
              </div>

              <!-- Details -->
              <div class="flex flex-wrap items-center gap-4 mt-3 text-sm text-foreground/80">
                <div class="flex items-center gap-2">
                  <Phone class="w-4 h-4" />
                  <span>{{ member.phone }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <Calendar class="w-4 h-4" />
                  <span>Expires: {{ formatDate(member.expiry_date) }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <span class="text-xs font-mono bg-surface-800 px-2 py-1 rounded">
                    {{ member.unique_code }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Action Button -->
            <Button variant="outline" size="sm" class="flex-shrink-0">Renew</Button>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
