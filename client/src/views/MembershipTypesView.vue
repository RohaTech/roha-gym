<script setup lang="ts">
import { ref, computed } from 'vue'
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query'
import { Plus, Pencil, Trash2, Users, Calendar, CheckCircle2 } from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import axiosInstance from '@/api/axiosInstance'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'

interface MembershipType {
  id: number
  name: string
  duration_days: number
  allowed_checkins_per_day: number | null
  description: string | null
  members_count: number
  created_at: string
  updated_at: string
}

interface MembershipTypeForm {
  name: string
  duration_days: number | string
  allowed_checkins_per_day: number | string
  description: string
}

const queryClient = useQueryClient()

// State
const isDialogOpen = ref(false)
const isDeleteDialogOpen = ref(false)
const editingType = ref<MembershipType | null>(null)
const deletingType = ref<MembershipType | null>(null)

const form = ref<MembershipTypeForm>({
  name: '',
  duration_days: '',
  allowed_checkins_per_day: '',
  description: '',
})

const errors = ref<Record<string, string>>({})

// Fetch membership types
const { data: membershipTypes, isLoading } = useQuery({
  queryKey: ['membership-types'],
  queryFn: async () => {
    const response = await axiosInstance.get<MembershipType[]>('/membership-types')
    return response.data
  },
})

// Create mutation
const createMutation = useMutation({
  mutationFn: async (data: MembershipTypeForm) => {
    const response = await axiosInstance.post('/membership-types', {
      name: data.name,
      duration_days: Number(data.duration_days),
      allowed_checkins_per_day: data.allowed_checkins_per_day
        ? Number(data.allowed_checkins_per_day)
        : null,
      description: data.description || null,
    })
    return response.data
  },
  onSuccess: () => {
    queryClient.invalidateQueries({ queryKey: ['membership-types'] })
    toast.success('Membership type created successfully')
    closeDialog()
  },
  onError: (error: any) => {
    const message = error?.response?.data?.message || 'Failed to create membership type'
    toast.error(message)
    if (error?.response?.data?.errors) {
      errors.value = error.response.data.errors
    }
  },
})

// Update mutation
const updateMutation = useMutation({
  mutationFn: async ({ id, data }: { id: number; data: MembershipTypeForm }) => {
    const response = await axiosInstance.put(`/membership-types/${id}`, {
      name: data.name,
      duration_days: Number(data.duration_days),
      allowed_checkins_per_day: data.allowed_checkins_per_day
        ? Number(data.allowed_checkins_per_day)
        : null,
      description: data.description || null,
    })
    return response.data
  },
  onSuccess: () => {
    queryClient.invalidateQueries({ queryKey: ['membership-types'] })
    toast.success('Membership type updated successfully')
    closeDialog()
  },
  onError: (error: any) => {
    const message = error?.response?.data?.message || 'Failed to update membership type'
    toast.error(message)
    if (error?.response?.data?.errors) {
      errors.value = error.response.data.errors
    }
  },
})

// Delete mutation
const deleteMutation = useMutation({
  mutationFn: async (id: number) => {
    const response = await axiosInstance.delete(`/membership-types/${id}`)
    return response.data
  },
  onSuccess: () => {
    queryClient.invalidateQueries({ queryKey: ['membership-types'] })
    toast.success('Membership type deleted successfully')
    isDeleteDialogOpen.value = false
    deletingType.value = null
  },
  onError: (error: any) => {
    const message = error?.response?.data?.message || 'Failed to delete membership type'
    toast.error(message)
  },
})

// Computed
const isSubmitting = computed(
  () => createMutation.isPending.value || updateMutation.isPending.value,
)
const isDeleting = computed(() => deleteMutation.isPending.value)

// Methods
function openCreateDialog() {
  editingType.value = null
  form.value = {
    name: '',
    duration_days: '',
    allowed_checkins_per_day: '',
    description: '',
  }
  errors.value = {}
  isDialogOpen.value = true
}

function openEditDialog(type: MembershipType) {
  editingType.value = type
  form.value = {
    name: type.name,
    duration_days: type.duration_days,
    allowed_checkins_per_day: type.allowed_checkins_per_day || '',
    description: type.description || '',
  }
  errors.value = {}
  isDialogOpen.value = true
}

function openDeleteDialog(type: MembershipType) {
  deletingType.value = type
  isDeleteDialogOpen.value = true
}

function closeDialog() {
  isDialogOpen.value = false
  editingType.value = null
  form.value = {
    name: '',
    duration_days: '',
    allowed_checkins_per_day: '',
    description: '',
  }
  errors.value = {}
}

function handleSubmit() {
  errors.value = {}

  if (!form.value.name) {
    errors.value.name = 'Name is required'
    return
  }

  if (!form.value.duration_days || Number(form.value.duration_days) < 1) {
    errors.value.duration_days = 'Duration must be at least 1 day'
    return
  }

  if (editingType.value) {
    updateMutation.mutate({ id: editingType.value.id, data: form.value })
  } else {
    createMutation.mutate(form.value)
  }
}

function handleDelete() {
  if (deletingType.value) {
    deleteMutation.mutate(deletingType.value.id)
  }
}

function formatDuration(days: number): string {
  if (days === 1) return '1 day'
  if (days < 30) return `${days} days`
  if (days === 30) return '1 month'
  if (days < 365) {
    const months = Math.floor(days / 30)
    return `${months} month${months > 1 ? 's' : ''}`
  }
  const years = Math.floor(days / 365)
  return `${years} year${years > 1 ? 's' : ''}`
}
</script>

<template>
  <div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-3xl font-display font-bold tracking-tight">Membership Types</h1>
        <p class="text-surface-400 mt-1">
          Create and manage membership plans for your gym
        </p>
      </div>
      <Button
        @click="openCreateDialog"
        class="gap-2 shrink-0 self-start whitespace-nowrap sm:self-auto"
      >
        <Plus class="w-4 h-4" />
        Add Membership Type
      </Button>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <Card v-for="i in 3" :key="i" class="animate-pulse">
        <CardHeader>
          <div class="h-6 bg-surface-800 rounded w-3/4"></div>
          <div class="h-4 bg-surface-800 rounded w-1/2 mt-2"></div>
        </CardHeader>
        <CardContent>
          <div class="space-y-3">
            <div class="h-4 bg-surface-800 rounded"></div>
            <div class="h-4 bg-surface-800 rounded w-5/6"></div>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Empty State -->
    <Card
      v-else-if="!membershipTypes || membershipTypes.length === 0"
      class="border-dashed border-2"
    >
      <CardContent class="flex flex-col items-center justify-center py-16">
        <div
          class="w-16 h-16 rounded-full   flex items-center justify-center mb-4"
        >
          <Calendar class="w-8 h-8 text-surface-400" />
        </div>
        <h3 class="text-xl font-semibold mb-2">No membership types yet</h3>
        <p class="text-surface-400 text-center mb-6 max-w-md">
          Create your first membership type to start managing member subscriptions
        </p>
        <Button @click="openCreateDialog" class="gap-2">
          <Plus class="w-4 h-4" />
          Create Membership Type
        </Button>
      </CardContent>
    </Card>

    <!-- Membership Types Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <Card
        v-for="type in membershipTypes"
        :key="type.id"
        class="hover:border-brand-500/50 transition-colors"
      >
        <CardHeader>
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <CardTitle class="text-xl">{{ type.name }}</CardTitle>
              <CardDescription class="mt-1">
                {{ formatDuration(type.duration_days) }}
              </CardDescription>
            </div>
            <div class="flex gap-1">
              <Button
                variant="ghost"
                size="icon"
                class="h-8 w-8"
                @click="openEditDialog(type)"
              >
                <Pencil class="w-4 h-4" />
              </Button>
              <Button
                variant="ghost"
                size="icon"
                class="h-8 w-8 text-danger-500 hover:text-danger-400"
                @click="openDeleteDialog(type)"
              >
                <Trash2 class="w-4 h-4" />
              </Button>
            </div>
          </div>
        </CardHeader>
        <CardContent class="space-y-3">
          <div class="flex items-center gap-2 text-sm text-surface-300">
            <Users class="w-4 h-4" />
            <span>{{ type.members_count }} member{{ type.members_count !== 1 ? 's' : '' }}</span>
          </div>
          <div
            v-if="type.allowed_checkins_per_day"
            class="flex items-center gap-2 text-sm text-surface-300"
          >
            <CheckCircle2 class="w-4 h-4" />
            <span>{{ type.allowed_checkins_per_day }} check-in{{ type.allowed_checkins_per_day !== 1 ? 's' : '' }} per day</span>
          </div>
          <div v-else class="flex items-center gap-2 text-sm text-surface-300">
            <CheckCircle2 class="w-4 h-4" />
            <span>Unlimited check-ins</span>
          </div>
          <p v-if="type.description" class="text-sm text-surface-400 line-clamp-2">
            {{ type.description }}
          </p>
        </CardContent>
      </Card>
    </div>

    <!-- Create/Edit Dialog -->
    <Dialog :open="isDialogOpen" @update:open="(val) => (val ? null : closeDialog())">
      <DialogContent class="sm:max-w-[500px]">
        <DialogHeader>
          <DialogTitle>
            {{ editingType ? 'Edit Membership Type' : 'Create Membership Type' }}
          </DialogTitle>
          <DialogDescription>
            {{
              editingType
                ? 'Update the membership type details'
                : 'Add a new membership type for your gym'
            }}
          </DialogDescription>
        </DialogHeader>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <!-- Name -->
          <div class="space-y-2">
            <Label for="name">Name *</Label>
            <Input
              id="name"
              v-model="form.name"
              placeholder="e.g., Monthly, Annual, Day Pass"
              :class="errors.name ? 'border-danger-500' : ''"
            />
            <p v-if="errors.name" class="text-sm text-danger-500">{{ errors.name }}</p>
          </div>

          <!-- Duration -->
          <div class="space-y-2">
            <Label for="duration">Duration (days) *</Label>
            <Input
              id="duration"
              v-model="form.duration_days"
              type="number"
              min="1"
              placeholder="e.g., 30 for monthly"
              :class="errors.duration_days ? 'border-danger-500' : ''"
            />
            <p v-if="errors.duration_days" class="text-sm text-danger-500">
              {{ errors.duration_days }}
            </p>
          </div>

          <!-- Check-ins per day -->
          <div class="space-y-2">
            <Label for="checkins">Check-ins per day (optional)</Label>
            <Input
              id="checkins"
              v-model="form.allowed_checkins_per_day"
              type="number"
              min="1"
              placeholder="Leave empty for unlimited"
              :class="errors.allowed_checkins_per_day ? 'border-danger-500' : ''"
            />
            <p v-if="errors.allowed_checkins_per_day" class="text-sm text-danger-500">
              {{ errors.allowed_checkins_per_day }}
            </p>
          </div>

          <!-- Description -->
          <div class="space-y-2">
            <Label for="description">Description (optional)</Label>
            <Textarea
              id="description"
              v-model="form.description"
              placeholder="Add any additional details about this membership type"
              rows="3"
              :class="errors.description ? 'border-danger-500' : ''"
            />
            <p v-if="errors.description" class="text-sm text-danger-500">
              {{ errors.description }}
            </p>
          </div>

          <DialogFooter>
            <Button type="button" variant="outline" @click="closeDialog" :disabled="isSubmitting">
              Cancel
            </Button>
            <Button type="submit" :disabled="isSubmitting">
              {{ isSubmitting ? 'Saving...' : editingType ? 'Update' : 'Create' }}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>

    <!-- Delete Confirmation Dialog -->
    <Dialog :open="isDeleteDialogOpen" @update:open="(val) => (isDeleteDialogOpen = val)">
      <DialogContent class="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Delete Membership Type</DialogTitle>
          <DialogDescription>
            Are you sure you want to delete "{{ deletingType?.name }}"? This action cannot be
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
          <Button
            type="button"
            variant="destructive"
            @click="handleDelete"
            :disabled="isDeleting"
          >
            {{ isDeleting ? 'Deleting...' : 'Delete' }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
