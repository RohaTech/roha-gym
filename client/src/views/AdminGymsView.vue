<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Plus, Search, Pencil, Trash2, Building2, ShieldOff, ShieldCheck, BadgeCheck } from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card, CardContent } from '@/components/ui/card'
import {
  Select, SelectContent, SelectItem, SelectTrigger, SelectValue,
} from '@/components/ui/select'
import {
  Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle,
} from '@/components/ui/dialog'
import { useAdminGyms } from '@/composables/useAdminGyms'
import type { GymSummary } from '@/types/admin'

const router = useRouter()

const {
  data, isLoading, isError,
  search, statusFilter, page,
  deleteGym, isDeleting,
  updateStatus, isUpdatingStatus,
} = useAdminGyms()

const gymToDelete = ref<GymSummary | null>(null)
const isDeleteDialogOpen = ref(false)

function openDeleteDialog(gym: GymSummary) {
  gymToDelete.value = gym
  isDeleteDialogOpen.value = true
}

async function confirmDelete() {
  if (!gymToDelete.value) return
  try {
    await deleteGym(gymToDelete.value.id)
    toast.success('Gym deleted successfully')
    isDeleteDialogOpen.value = false
    gymToDelete.value = null
  } catch (e: any) {
    toast.error(e?.response?.data?.message ?? 'Failed to delete gym')
  }
}

const gymToChangeStatus = ref<GymSummary | null>(null)
const isStatusDialogOpen = ref(false)

function openStatusDialog(gym: GymSummary) {
  gymToChangeStatus.value = gym
  isStatusDialogOpen.value = true
}

async function confirmStatusChange() {
  const gym = gymToChangeStatus.value
  if (!gym) return
  // Pending (2) and suspended (0) become active (1); active (1) becomes suspended (0).
  const newStatus = gym.status === 1 ? 0 : 1
  try {
    await updateStatus({ id: gym.id, status: newStatus })
    toast.success('Gym status updated')
    isStatusDialogOpen.value = false
    gymToChangeStatus.value = null
  } catch (e: any) {
    toast.error(e?.response?.data?.message ?? 'Failed to update status')
  }
}

function goToCreate() {
  router.push({ name: 'admin-gym-create' })
}

function goToEdit(id: number) {
  router.push({ name: 'admin-gym-edit', params: { gymId: id } })
}

function resetFilters() {
  search.value = ''
  statusFilter.value = 'all'
  page.value = 1
}

function onSearchInput() {
  page.value = 1
}

function onStatusChange() {
  page.value = 1
}
</script>

<template>
  <section class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h2 class="text-xl font-semibold">{{ $lang.adminGymsTitle }}</h2>
        <p class="text-sm text-surface-400">{{ $lang.adminGymsSubtitle }}</p>
      </div>
      <Button @click="goToCreate" class="gap-2 shrink-0 self-start sm:self-auto">
        <Plus class="size-4" />
        {{ $lang.adminGymAddNew }}
      </Button>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-3">
      <div class="relative flex-1">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-surface-500" />
        <Input
          v-model="search"
          :placeholder="$lang.adminGymSearch"
          class="pl-10"
          @input="onSearchInput"
        />
      </div>
      <Select v-model="statusFilter" @update:model-value="onStatusChange">
        <SelectTrigger class="w-full sm:w-44">
          <SelectValue :placeholder="$lang.adminGymFilterAll" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="all">{{ $lang.adminGymFilterAll }}</SelectItem>
          <SelectItem value="active">{{ $lang.adminGymFilterActive }}</SelectItem>
          <SelectItem value="pending">{{ $lang.adminGymFilterPending }}</SelectItem>
          <SelectItem value="suspended">{{ $lang.adminGymFilterSuspended }}</SelectItem>
        </SelectContent>
      </Select>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="text-surface-400 text-sm py-8 text-center">
      {{ $lang.adminGymLoading }}
    </div>

    <!-- Error -->
    <div v-else-if="isError" class="text-danger-500 text-sm py-8 text-center">
      {{ $lang.adminGymError }}
    </div>

    <!-- Empty -->
    <Card v-else-if="!data?.data.length" class="border-dashed border-2">
      <CardContent class="flex flex-col items-center justify-center py-16">
        <div class="w-16 h-16 rounded-full bg-surface-200 dark:bg-surface-800 flex items-center justify-center mb-4">
          <Building2 class="size-8 text-surface-400" />
        </div>
        <p class="text-surface-400 text-sm mb-4">{{ $lang.adminGymEmpty }}</p>
        <Button variant="outline" size="sm" @click="resetFilters">Clear filters</Button>
      </CardContent>
    </Card>

    <!-- Table -->
    <div v-else class="rounded-xl border border-surface-200 dark:border-surface-800 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-surface-50 dark:bg-surface-900 text-surface-500 text-xs uppercase tracking-wide">
            <tr>
              <th class="px-4 py-3 text-left">{{ $lang.adminGymTableName }}</th>
              <th class="px-4 py-3 text-left hidden sm:table-cell">{{ $lang.adminGymTablePhone }}</th>
              <th class="px-4 py-3 text-left hidden md:table-cell">{{ $lang.adminGymTableAddress }}</th>
              <th class="px-4 py-3 text-center">{{ $lang.adminGymTableMembers }}</th>
              <th class="px-4 py-3 text-center">{{ $lang.adminGymTableStatus }}</th>
              <th class="px-4 py-3 text-left hidden lg:table-cell">{{ $lang.adminGymTableCreated }}</th>
              <th class="px-4 py-3 text-right">{{ $lang.adminGymTableActions }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-surface-100 dark:divide-surface-800">
            <tr
              v-for="gym in data.data"
              :key="gym.id"
              class="bg-white dark:bg-surface-950 hover:bg-surface-50 dark:hover:bg-surface-900 transition-colors"
            >
              <!-- Name + Logo -->
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

              <!-- Phone -->
              <td class="px-4 py-3 text-surface-500 hidden sm:table-cell">{{ gym.phone }}</td>

              <!-- Address -->
              <td class="px-4 py-3 text-surface-500 hidden md:table-cell max-w-[160px] truncate">
                {{ gym.address ?? '—' }}
              </td>

              <!-- Members -->
              <td class="px-4 py-3 text-center">
                <span class="text-sm font-semibold">{{ gym.members_count }}</span>
                <span class="text-xs text-green-500 ml-1">({{ gym.active_members_count }})</span>
              </td>

              <!-- Status -->
              <td class="px-4 py-3 text-center">
                <span
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                  :class="gym.status === 1
                    ? 'bg-green-500/10 text-green-600 dark:text-green-400'
                    : gym.status === 2
                      ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400'
                      : 'bg-yellow-500/10 text-yellow-600 dark:text-yellow-400'"
                >
                  {{ gym.status === 1
                    ? $lang.adminGymStatusActive
                    : gym.status === 2
                      ? $lang.adminGymStatusPending
                      : $lang.adminGymStatusSuspended }}
                </span>
              </td>

              <!-- Created -->
              <td class="px-4 py-3 text-surface-500 text-xs hidden lg:table-cell">
                {{ gym.created_at ?? '—' }}
              </td>

              <!-- Actions -->
              <td class="px-4 py-3">
                <div class="flex items-center justify-end gap-1">
                  <!-- Approve a pending gym -->
                  <Button
                    v-if="gym.status === 2"
                    variant="ghost"
                    size="icon"
                    class="size-8"
                    :title="$lang.adminGymApprove"
                    :disabled="isUpdatingStatus"
                    @click="openStatusDialog(gym)"
                  >
                    <BadgeCheck class="size-4 text-green-500" />
                  </Button>
                  <!-- Suspend / re-activate an approved gym -->
                  <Button
                    v-else
                    variant="ghost"
                    size="icon"
                    class="size-8"
                    :title="gym.status === 1 ? $lang.adminGymSuspend : $lang.adminGymActivate"
                    :disabled="isUpdatingStatus"
                    @click="openStatusDialog(gym)"
                  >
                    <ShieldOff v-if="gym.status === 1" class="size-4 text-yellow-500" />
                    <ShieldCheck v-else class="size-4 text-green-500" />
                  </Button>
                  <Button
                    variant="ghost"
                    size="icon"
                    class="size-8"
                    :title="$lang.adminGymEdit"
                    @click="goToEdit(gym.id)"
                  >
                    <Pencil class="size-4" />
                  </Button>
                  <Button
                    variant="ghost"
                    size="icon"
                    class="size-8 text-danger-500 hover:text-danger-400"
                    :title="$lang.adminGymDelete"
                    @click="openDeleteDialog(gym)"
                  >
                    <Trash2 class="size-4" />
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
          Page {{ data.current_page }} of {{ data.last_page }}
          ({{ data.total }} gyms)
        </p>
        <div class="flex gap-2">
          <Button
            variant="outline"
            size="sm"
            :disabled="page <= 1"
            @click="page--"
          >Previous</Button>
          <Button
            variant="outline"
            size="sm"
            :disabled="page >= data.last_page"
            @click="page++"
          >Next</Button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <Dialog :open="isDeleteDialogOpen" @update:open="(v) => (isDeleteDialogOpen = v)">
      <DialogContent class="sm:max-w-md">
        <DialogHeader>
          <DialogTitle>{{ $lang.adminGymConfirmDelete }}</DialogTitle>
          <DialogDescription>
            {{ $lang.adminGymDeleteMsg }}
          </DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button variant="outline" :disabled="isDeleting" @click="isDeleteDialogOpen = false">
            {{ $lang.adminGymCancel }}
          </Button>
          <Button variant="destructive" :disabled="isDeleting" @click="confirmDelete">
            {{ isDeleting ? $lang.adminGymDeleting : $lang.adminGymDelete }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Status Change Confirmation Dialog -->
    <Dialog :open="isStatusDialogOpen" @update:open="(v) => (isStatusDialogOpen = v)">
      <DialogContent class="sm:max-w-md">
        <DialogHeader>
          <DialogTitle>{{ $lang.adminGymConfirmStatus }}</DialogTitle>
          <DialogDescription>
            {{ $lang.adminGymStatusMsg }} "{{ gymToChangeStatus?.name }}"?
          </DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button variant="outline" :disabled="isUpdatingStatus" @click="isStatusDialogOpen = false">
            {{ $lang.adminGymCancel }}
          </Button>
          <Button :disabled="isUpdatingStatus" @click="confirmStatusChange">
            {{ isUpdatingStatus ? $lang.adminGymUpdating : $lang.adminGymConfirm }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </section>
</template>
