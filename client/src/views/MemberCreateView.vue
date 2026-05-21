<script setup lang="ts">
import { ref, computed, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query'
import { ArrowLeft, Upload, X } from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import axiosInstance from '@/api/axiosInstance'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
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

interface MemberForm {
  full_name: string
  phone: string
  membership_type_id: string
  start_date: string
  gender: string
  notes: string
}

const router = useRouter()
const queryClient = useQueryClient()

// State
const form = ref<MemberForm>({
  full_name: '',
  phone: '',
  membership_type_id: '',
  start_date: new Date().toISOString().split('T')[0]!,
  gender: '',
  notes: '',
})

const photoFile = ref<File | null>(null)
const photoPreview = ref<string>('')
const photoInput = ref<HTMLInputElement | null>(null)
const errors = ref<Record<string, string>>({})

// Fetch membership types
const { data: membershipTypes, isLoading: loadingTypes } = useQuery({
  queryKey: ['membership-types'],
  queryFn: async () => {
    const response = await axiosInstance.get<MembershipType[]>('/membership-types')
    return response.data
  },
})

// Create member mutation
const createMutation = useMutation({
  mutationFn: async (formData: FormData) => {
    const response = await axiosInstance.post('/members', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    return response.data
  },
  onSuccess: () => {
    queryClient.invalidateQueries({ queryKey: ['members'] })
    toast.success('Member added successfully')
    router.push({ name: 'members-list' })
  },
  onError: (error: any) => {
    const message = error?.response?.data?.message || 'Failed to add member'
    toast.error(message)
    if (error?.response?.data?.errors) {
      errors.value = error.response.data.errors
    }
  },
})

const isSubmitting = computed(() => createMutation.isPending.value)

// Methods
function handlePhotoChange(event: Event) {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]

  if (!file) {
    clearPhoto()
    return
  }

  // Validate file type
  const validTypes = ['image/jpeg', 'image/png', 'image/webp']
  if (!validTypes.includes(file.type)) {
    errors.value.photo = 'Only JPEG, PNG, and WebP images are allowed'
    clearPhoto()
    return
  }

  // Validate file size (2MB)
  if (file.size > 2 * 1024 * 1024) {
    errors.value.photo = 'File size must not exceed 2MB'
    clearPhoto()
    return
  }

  photoFile.value = file
  photoPreview.value = URL.createObjectURL(file)
  delete errors.value.photo
}

function clearPhoto() {
  if (photoPreview.value) {
    URL.revokeObjectURL(photoPreview.value)
  }
  photoFile.value = null
  photoPreview.value = ''
  if (photoInput.value) {
    photoInput.value.value = ''
  }
}

function handleSubmit() {
  errors.value = {}

  // Validation
  if (!form.value.full_name) {
    errors.value.full_name = 'Full name is required'
    return
  }

  if (!form.value.phone) {
    errors.value.phone = 'Phone number is required'
    return
  }

  if (!form.value.membership_type_id) {
    errors.value.membership_type_id = 'Membership type is required'
    return
  }

  if (!form.value.start_date) {
    errors.value.start_date = 'Start date is required'
    return
  }

  const formData = new FormData()
  formData.append('full_name', form.value.full_name)
  formData.append('phone', form.value.phone)
  formData.append('membership_type_id', form.value.membership_type_id)
  formData.append('start_date', form.value.start_date)

  if (form.value.gender) {
    formData.append('gender', form.value.gender)
  }

  if (form.value.notes) {
    formData.append('notes', form.value.notes)
  }

  if (photoFile.value) {
    formData.append('photo', photoFile.value)
  }

  createMutation.mutate(formData)
}

function goBack() {
  router.push({ name: 'members-list' })
}

function triggerPhotoUpload() {
  photoInput.value?.click()
}

// Cleanup
onUnmounted(() => {
  if (photoPreview.value) {
    URL.revokeObjectURL(photoPreview.value)
  }
})
</script>

<template>
  <div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <Button variant="ghost" size="icon" @click="goBack">
        <ArrowLeft class="w-5 h-5" />
      </Button>
      <div>
        <h1 class="text-3xl font-display font-bold tracking-tight">Add New Member</h1>
        <p class="text-surface-400 mt-1">Register a new member to your gym</p>
      </div>
    </div>

    <!-- Form -->
    <form @submit.prevent="handleSubmit">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>Member Information</CardTitle>
              <CardDescription>Enter the member's personal details</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
              <!-- Full Name -->
              <div class="space-y-2">
                <Label for="full_name">Full Name *</Label>
                <Input
                  id="full_name"
                  v-model="form.full_name"
                  placeholder="John Doe"
                  :class="errors.full_name ? 'border-danger-500' : ''"
                />
                <p v-if="errors.full_name" class="text-sm text-danger-500">
                  {{ errors.full_name }}
                </p>
              </div>

              <!-- Phone -->
              <div class="space-y-2">
                <Label for="phone">Phone Number *</Label>
                <Input
                  id="phone"
                  v-model="form.phone"
                  type="tel"
                  placeholder="+1234567890"
                  :class="errors.phone ? 'border-danger-500' : ''"
                />
                <p v-if="errors.phone" class="text-sm text-danger-500">{{ errors.phone }}</p>
              </div>

              <!-- Gender -->
              <div class="space-y-2">
                <Label for="gender">Gender (optional)</Label>
                <Select v-model="form.gender">
                  <SelectTrigger>
                    <SelectValue placeholder="Select gender" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="male">Male</SelectItem>
                    <SelectItem value="female">Female</SelectItem>
                    <SelectItem value="other">Other</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Membership Details</CardTitle>
              <CardDescription>Select membership type and start date</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
              <!-- Membership Type -->
              <div class="space-y-2">
                <Label for="membership_type">Membership Type *</Label>
                <Select v-model="form.membership_type_id" :disabled="loadingTypes">
                  <SelectTrigger :class="errors.membership_type_id ? 'border-danger-500' : ''">
                    <SelectValue
                      :placeholder="loadingTypes ? 'Loading...' : 'Select membership type'"
                    />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem
                      v-for="type in membershipTypes"
                      :key="type.id"
                      :value="type.id.toString()"
                    >
                      {{ type.name }} ({{ type.duration_days }} days)
                    </SelectItem>
                  </SelectContent>
                </Select>
                <p v-if="errors.membership_type_id" class="text-sm text-danger-500">
                  {{ errors.membership_type_id }}
                </p>
              </div>

              <!-- Start Date -->
              <div class="space-y-2">
                <Label for="start_date">Start Date *</Label>
                <Input
                  id="start_date"
                  v-model="form.start_date"
                  type="date"
                  :class="errors.start_date ? 'border-danger-500' : ''"
                />
                <p v-if="errors.start_date" class="text-sm text-danger-500">
                  {{ errors.start_date }}
                </p>
              </div>

              <!-- Notes -->
              <div class="space-y-2">
                <Label for="notes">Notes (optional)</Label>
                <Textarea
                  id="notes"
                  v-model="form.notes"
                  placeholder="Any additional information about the member"
                  rows="3"
                />
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Photo Upload -->
        <div class="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>Member Photo</CardTitle>
              <CardDescription>Upload a photo (optional)</CardDescription>
            </CardHeader>
            <CardContent>
              <div class="space-y-4">
                <!-- Preview -->
                <div
                  v-if="photoPreview"
                  class="relative w-full aspect-square rounded-lg overflow-hidden bg-surface-800"
                >
                  <img :src="photoPreview" alt="Preview" class="w-full h-full object-cover" />
                  <button
                    type="button"
                    @click="clearPhoto"
                    class="absolute top-2 right-2 p-1 rounded-full bg-surface-900/80 hover:bg-surface-900 transition-colors"
                  >
                    <X class="w-4 h-4" />
                  </button>
                </div>

                <!-- Upload Button -->
                <div
                  v-else
                  class="w-full aspect-square rounded-lg border-2 border-dashed border-surface-700 flex flex-col items-center justify-center gap-2 hover:border-brand-500 transition-colors cursor-pointer"
                  @click="triggerPhotoUpload"
                >
                  <Upload class="w-8 h-8 text-surface-400" />
                  <p class="text-sm text-surface-400">Click to upload</p>
                  <p class="text-xs text-surface-500">JPEG, PNG, WebP (max 2MB)</p>
                </div>

                <input
                  ref="photoInput"
                  type="file"
                  accept="image/jpeg,image/png,image/webp"
                  class="hidden"
                  @change="handlePhotoChange"
                />

                <p v-if="errors.photo" class="text-sm text-danger-500">{{ errors.photo }}</p>
              </div>
            </CardContent>
          </Card>

          <!-- Actions -->
          <div class="flex flex-col gap-3">
            <Button type="submit" :disabled="isSubmitting" class="w-full">
              {{ isSubmitting ? 'Adding Member...' : 'Add Member' }}
            </Button>
            <Button
              type="button"
              variant="outline"
              @click="goBack"
              :disabled="isSubmitting"
              class="w-full"
            >
              Cancel
            </Button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>
