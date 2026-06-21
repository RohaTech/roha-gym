<script setup lang="ts">
import { ref, computed, watch, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query'
import { ArrowLeft, Upload, X, CalendarIcon } from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import axiosInstance from '@/api/axiosInstance'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Calendar } from '@/components/ui/calendar'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { parseDate, DateFormatter, getLocalTimeZone } from '@internationalized/date'
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

interface MemberDetail {
  id: number
  full_name: string
  phone: string
  membership_type_id: number
  start_date: string
  gender: string | null
  notes: string | null
  photo_path: string | null
  status: string
}

const router = useRouter()
const route = useRoute()
const queryClient = useQueryClient()

const memberId = computed(() => Number(route.params.memberId))

const df = new DateFormatter('en-US', { dateStyle: 'long' })

const form = ref({
  full_name: '',
  phone: '',
  membership_type_id: '',
  start_date: '',
  gender: '',
  notes: '',
  status: '',
})

const photoFile = ref<File | null>(null)
const photoPreview = ref<string>('')
const existingPhotoPath = ref<string | null>(null)
const photoInput = ref<HTMLInputElement | null>(null)
const errors = ref<Record<string, string>>({})

const startDateValue = computed({
  get: () => form.value.start_date ? parseDate(form.value.start_date) : undefined,
  set: (val) => {
    form.value.start_date = val ? val.toString() : ''
  },
})

// Fetch member data
const { data: memberData, isLoading: loadingMember, isError: memberError } = useQuery({
  queryKey: ['member', memberId],
  queryFn: async () => {
    const response = await axiosInstance.get<MemberDetail>(`/members/${memberId.value}`)
    return response.data
  },
  enabled: computed(() => !!memberId.value),
})

watch(memberData, (data) => {
  if (!data) return
  form.value.full_name = data.full_name
  form.value.phone = data.phone
  form.value.membership_type_id = data.membership_type_id.toString()
  form.value.start_date = data.start_date
  form.value.gender = data.gender ?? ''
  form.value.notes = data.notes ?? ''
  form.value.status = data.status
  existingPhotoPath.value = data.photo_path
}, { immediate: true })

// Fetch membership types
const { data: membershipTypes, isLoading: loadingTypes } = useQuery({
  queryKey: ['membership-types'],
  queryFn: async () => {
    const response = await axiosInstance.get<MembershipType[]>('/membership-types')
    return response.data
  },
})

// Update mutation
const updateMutation = useMutation({
  mutationFn: async (formData: FormData) => {
    const response = await axiosInstance.put(`/members/${memberId.value}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data
  },
  onSuccess: () => {
    queryClient.invalidateQueries({ queryKey: ['members'] })
    queryClient.invalidateQueries({ queryKey: ['member', memberId.value] })
    toast.success('Member updated successfully')
    router.push({ name: 'members-list' })
  },
  onError: (error: any) => {
    const message = error?.response?.data?.message || 'Failed to update member'
    toast.error(message)
    if (error?.response?.data?.errors) {
      errors.value = error.response.data.errors
    }
  },
})

const isSubmitting = computed(() => updateMutation.isPending.value)

function handlePhotoChange(event: Event) {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]

  if (!file) {
    clearNewPhoto()
    return
  }

  const validTypes = ['image/jpeg', 'image/png', 'image/webp']
  if (!validTypes.includes(file.type)) {
    errors.value.photo = 'Only JPEG, PNG, and WebP images are allowed'
    clearNewPhoto()
    return
  }

  if (file.size > 2 * 1024 * 1024) {
    errors.value.photo = 'File size must not exceed 2MB'
    clearNewPhoto()
    return
  }

  photoFile.value = file
  photoPreview.value = URL.createObjectURL(file)
  delete errors.value.photo
}

function clearNewPhoto() {
  if (photoPreview.value) URL.revokeObjectURL(photoPreview.value)
  photoFile.value = null
  photoPreview.value = ''
  if (photoInput.value) photoInput.value.value = ''
}

function triggerPhotoUpload() {
  photoInput.value?.click()
}

function handleSubmit() {
  errors.value = {}

  if (!form.value.full_name) { errors.value.full_name = 'Full name is required'; return }
  if (!form.value.phone) { errors.value.phone = 'Phone number is required'; return }
  if (!form.value.membership_type_id) { errors.value.membership_type_id = 'Membership type is required'; return }
  if (!form.value.start_date) { errors.value.start_date = 'Start date is required'; return }

  const formData = new FormData()
  formData.append('full_name', form.value.full_name)
  formData.append('phone', form.value.phone)
  formData.append('membership_type_id', form.value.membership_type_id)
  formData.append('start_date', form.value.start_date)
  if (form.value.gender) formData.append('gender', form.value.gender)
  if (form.value.notes) formData.append('notes', form.value.notes)
  if (form.value.status) formData.append('status', form.value.status)
  if (photoFile.value) formData.append('photo', photoFile.value)

  updateMutation.mutate(formData)
}

onUnmounted(() => {
  if (photoPreview.value) URL.revokeObjectURL(photoPreview.value)
})
</script>

<template>
  <div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <Button variant="ghost" size="icon" @click="router.back()">
        <ArrowLeft class="w-5 h-5" />
      </Button>
      <div>
        <h1 class="text-3xl font-display font-bold tracking-tight">Edit Member</h1>
        <p class="text-surface-400 mt-1">Update member information</p>
      </div>
    </div>

    <div v-if="loadingMember" class="text-surface-400 text-sm">Loading member data...</div>
    <div v-else-if="memberError" class="text-danger-500 text-sm">Failed to load member.</div>

    <form v-else-if="memberData" @submit.prevent="handleSubmit">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>Member Information</CardTitle>
              <CardDescription>Update the member's personal details</CardDescription>
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
                <p v-if="errors.full_name" class="text-sm text-danger-500">{{ errors.full_name }}</p>
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
                <Label>Gender (optional)</Label>
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
              <CardDescription>Update membership type, dates, and status</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
              <!-- Membership Type -->
              <div class="space-y-2">
                <Label>Membership Type *</Label>
                <Select v-model="form.membership_type_id" :disabled="loadingTypes">
                  <SelectTrigger :class="errors.membership_type_id ? 'border-danger-500' : ''">
                    <SelectValue :placeholder="loadingTypes ? 'Loading...' : 'Select membership type'" />
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
              <div class="space-y-2 flex flex-col">
                <Label>Start Date *</Label>
                <Popover>
                  <PopoverTrigger as-child>
                    <Button
                      variant="outline"
                      :class="[
                        'w-full justify-start text-left font-normal',
                        !form.start_date && 'text-surface-400',
                        errors.start_date ? 'border-danger-500' : '',
                      ]"
                    >
                      <CalendarIcon class="mr-2 h-4 w-4" />
                      {{ startDateValue ? df.format(startDateValue.toDate(getLocalTimeZone())) : 'Pick a date' }}
                    </Button>
                  </PopoverTrigger>
                  <PopoverContent class="w-auto p-0">
                    <Calendar v-model="startDateValue" initial-focus layout="month-and-year" />
                  </PopoverContent>
                </Popover>
                <p v-if="errors.start_date" class="text-sm text-danger-500">{{ errors.start_date }}</p>
              </div>

              <!-- Status -->
              <div class="space-y-2">
                <Label>Status</Label>
                <Select v-model="form.status">
                  <SelectTrigger>
                    <SelectValue placeholder="Select status" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="active">Active</SelectItem>
                    <SelectItem value="expired">Expired</SelectItem>
                    <SelectItem value="suspended">Suspended</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <!-- Notes -->
              <div class="space-y-2">
                <Label>Notes (optional)</Label>
                <Textarea
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
              <CardDescription>Upload a new photo to replace the existing one</CardDescription>
            </CardHeader>
            <CardContent>
              <div class="space-y-4">
                <!-- New photo preview -->
                <div
                  v-if="photoPreview"
                  class="relative w-full aspect-square rounded-lg overflow-hidden bg-surface-800"
                >
                  <img :src="photoPreview" alt="Preview" class="w-full h-full object-cover" />
                  <button
                    type="button"
                    @click="clearNewPhoto"
                    class="absolute top-2 right-2 p-1 rounded-full bg-surface-900/80 hover:bg-surface-900 transition-colors"
                  >
                    <X class="w-4 h-4" />
                  </button>
                </div>

                <!-- Existing photo -->
                <div
                  v-else-if="existingPhotoPath"
                  class="relative w-full aspect-square rounded-lg overflow-hidden bg-surface-800 cursor-pointer"
                  @click="triggerPhotoUpload"
                >
                  <img
                    :src="`/storage/${existingPhotoPath}`"
                    alt="Current photo"
                    class="w-full h-full object-cover"
                  />
                  <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                    <Upload class="w-8 h-8 text-white" />
                  </div>
                </div>

                <!-- No photo upload area -->
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
              {{ isSubmitting ? 'Saving...' : 'Save Changes' }}
            </Button>
            <Button
              type="button"
              variant="outline"
              @click="router.back()"
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
