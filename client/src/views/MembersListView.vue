<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query'
import {
  Plus,
  Users,
  Search,
  Pencil,
  Trash2,
  Calendar,
  Phone,
  CreditCard,
  User as UserIcon,
} from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import axiosInstance from '@/api/axiosInstance'
import { storageUrl } from '@/constants'
import MemberCardModal from '@/components/membership/MemberCardModal.vue'
import { useAuthStore } from '@/store/authStore'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

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
const queryClient = useQueryClient()
const authStore = useAuthStore()

const searchQuery = ref('')
const statusFilter = ref<string>('all')
const deletingMember = ref<Member | null>(null)
const isDeleteDialogOpen = ref(false)
const cardMemberId = ref<number | null>(null)
const isCardDialogOpen = ref(false)

// Fetch members
const { data: members, isLoading } = useQuery({
  queryKey: ['members', searchQuery, statusFilter],
  queryFn: async () => {
    const params = new URLSearchParams()
    if (searchQuery.value) params.append('search', searchQuery.value)
    if (statusFilter.value !== 'all') params.append('status', statusFilter.value)

    const response = await axiosInstance.get<Member[]>(`/members?${params}`)
    return response.data
  },
})

// Delete mutation
const deleteMutation = useMutation({
  mutationFn: async (id: number) => {
    const response = await axiosInstance.delete(`/members/${id}`)
    return response.data
  },
  onSuccess: () => {
    queryClient.invalidateQueries({ queryKey: ['members'] })
    toast.success('Member deleted successfully')
    isDeleteDialogOpen.value = false
    deletingMember.value = null
  },
  onError: (error: any) => {
    const message = error?.response?.data?.message || 'Failed to delete member'
    toast.error(message)
  },
})

const isDeleting = computed(() => deleteMutation.isPending.value)

function goToAddMember() {
  router.push({ name: 'member-create' })
}

function goToMemberCard(id: number) {
  router.push({ name: 'member-card', params: { memberId: id } })
}

function openMemberCard(id: number) {
  cardMemberId.value = id
  isCardDialogOpen.value = true
}

function goToEditMember(id: number) {
  router.push({ name: 'member-edit', params: { memberId: id } })
}

function openDeleteDialog(member: Member) {
  deletingMember.value = member
  isDeleteDialogOpen.value = true
}

function handleDelete() {
  if (deletingMember.value) {
    deleteMutation.mutate(deletingMember.value.id)
  }
}

function getStatusColor(status: string) {
  switch (status) {
    case 'active':
      return 'text-green-500 bg-green-500/10'
    case 'expired':
      return 'text-red-500 bg-red-500/10'
    case 'suspended':
      return 'text-yellow-500 bg-yellow-500/10'
    default:
      return 'text-surface-400 bg-surface-800'
  }
}

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
</script>

<template>
  <div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-3xl font-display font-bold tracking-tight">Members</h1>
        <p class="text-surface-400 mt-1">Manage all your gym members</p>
      </div>
      <Button
        size="lg"
        @click="goToAddMember"
        class="gap-2 shrink-0 self-start font-semibold shadow-lg shadow-brand-500/30 sm:self-auto"
      >
        <Plus class="w-5 h-5" />
        Add Member
      </Button>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-4">
      <div class="relative flex-1">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-500" />
        <Input
          v-model="searchQuery"
          placeholder="Search by name, phone, or code..."
          class="pl-10"
        />
      </div>
      <Select v-model="statusFilter">
        <SelectTrigger class="w-full sm:w-[180px]">
          <SelectValue placeholder="All Status" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="all">All Status</SelectItem>
          <SelectItem value="active">Active</SelectItem>
          <SelectItem value="expired">Expired</SelectItem>
          <SelectItem value="suspended">Suspended</SelectItem>
        </SelectContent>
      </Select>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <Card v-for="i in 6" :key="i" class="animate-pulse">
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
          <Users class="w-8 h-8 text-surface-400" />
        </div>
        <h3 class="text-xl font-semibold mb-2">
          {{ searchQuery ? 'No members found' : 'No members yet' }}
        </h3>
        <p class="text-surface-400 text-center mb-6 max-w-md">
          {{
            searchQuery
              ? 'Try adjusting your search or filters'
              : 'Add your first member to start managing memberships'
          }}
        </p>
        <Button v-if="!searchQuery" @click="goToAddMember" class="gap-2">
          <Plus class="w-4 h-4" />
          Add Your First Member
        </Button>
      </CardContent>
    </Card>

    <!-- Members Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <Card
        v-for="member in members"
        :key="member.id"
        class="hover:border-brand-500/50 transition-colors"
      >
        <CardHeader class="pb-2">
          <div class="flex items-start gap-3">
            <!-- Avatar -->
            <div
              class="w-12 h-12 rounded-full bg-surface-800 flex items-center justify-center flex-shrink-0 overflow-hidden"
            >
              <img
                v-if="member.photo_path"
                :src="storageUrl(member.photo_path)"
                :alt="member.full_name"
                class="w-full h-full object-cover"
              />
              <UserIcon v-else class="w-6 h-6 text-surface-400" />
            </div>

            <!-- Info -->
            <div class="flex-1 min-w-0">
              <h3 class="font-semibold truncate">{{ member.full_name }}</h3>
              <p class="text-sm text-surface-400">{{ member.membership_type.name }}</p>
            </div>
          </div>

          <!-- Actions row -->
          <div class="flex gap-1 mt-2 justify-end">
            <Button
              variant="ghost"
              size="icon"
              class="h-8 w-8"
              title="View membership card"
              @click="openMemberCard(member.id)"
            >
              <CreditCard class="w-4 h-4" />
            </Button>
            <Button variant="ghost" size="icon" class="h-8 w-8" @click="goToEditMember(member.id)">
              <Pencil class="w-4 h-4" />
            </Button>
            <Button
              variant="ghost"
              size="icon"
              class="h-8 w-8 text-danger-500 hover:text-danger-400"
              @click="openDeleteDialog(member)"
            >
              <Trash2 class="w-4 h-4" />
            </Button>
          </div>
        </CardHeader>

        <CardContent class="space-y-3">
          <!-- Status Badge -->
          <div class="flex items-center justify-between">
            <span
              :class="[
                'px-2 py-1 rounded-full text-xs font-medium capitalize',
                getStatusColor(member.status),
              ]"
            >
              {{ member.status }}
            </span>
            <span class="text-xs text-surface-500 font-mono">{{ member.unique_code }}</span>
          </div>

          <!-- Phone -->
          <div class="flex items-center gap-2 text-sm text-foreground/80">
            <Phone class="w-4 h-4" />
            <span>{{ member.phone }}</span>
          </div>

          <!-- Expiry -->
          <div class="flex items-center gap-2 text-sm">
            <Calendar class="w-4 h-4" />
            <span
              v-if="member.status === 'expired'"
              class="text-red-600 dark:text-red-500 font-medium"
            >
              Expired on: {{ formatDate(member.expiry_date) }}
            </span>
            <span
              v-else
              :class="
                getDaysUntilExpiry(member.expiry_date) <= 7 && member.status === 'active'
                  ? 'text-yellow-600 dark:text-yellow-500 font-medium'
                  : 'text-foreground/80'
              "
            >
              Expires: {{ formatDate(member.expiry_date) }}
              <span
                v-if="getDaysUntilExpiry(member.expiry_date) > 0 && member.status === 'active'"
                class="text-xs"
              >
                ({{ getDaysUntilExpiry(member.expiry_date) }} days)
              </span>
            </span>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Membership Card Dialog -->
    <MemberCardModal
      :open="isCardDialogOpen"
      :member-id="cardMemberId"
      @update:open="(val) => (isCardDialogOpen = val)"
    />

    <!-- Delete Confirmation Dialog -->
    <Dialog :open="isDeleteDialogOpen" @update:open="(val) => (isDeleteDialogOpen = val)">
      <DialogContent class="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Delete Member</DialogTitle>
          <DialogDescription>
            Are you sure you want to delete "{{ deletingMember?.full_name }}"? This action cannot be
            undone.
          </DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button
            type="button"
            variant="outline"
            @click="isDeleteDialogOpen = false"
            :disabled="isDeleting"
          >
            Cancel
          </Button>
          <Button type="button" variant="destructive" @click="handleDelete" :disabled="isDeleting">
            {{ isDeleting ? 'Deleting...' : 'Delete' }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
