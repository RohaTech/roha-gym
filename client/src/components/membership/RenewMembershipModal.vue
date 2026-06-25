<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { Calendar, Loader2, AlertCircle } from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import axiosInstance from '@/api/axiosInstance'
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Calendar as CalendarComponent } from '@/components/ui/calendar'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { parseDate, DateFormatter, getLocalTimeZone } from '@internationalized/date'

interface MembershipType {
  id: number
  name: string
  duration_days: number
  price?: number
}

interface Member {
  id: number
  full_name: string
  expiry_date: string
  membership_type_id: number
}

interface Props {
  member: Member | null
  open: boolean
}

interface Emits {
  (e: 'update:open', value: boolean): void
  (e: 'renewed'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const queryClient = useQueryClient()
const df = new DateFormatter('en-US', { dateStyle: 'long' })

const selectedMembershipTypeId = ref<string>('')
const startDateOpen = ref(false)
const selectedStartDate = ref<string>((new Date().toISOString().split('T')[0] || ''))

// Fetch membership types
const { data: membershipTypes, isLoading: loadingTypes } = useQuery({
  queryKey: ['membership-types'],
  queryFn: async () => {
    const response = await axiosInstance.get<MembershipType[]>('/membership-types')
    return response.data
  },
})

// Reset form when modal opens
watch(() => props.open, (isOpen) => {
  if (isOpen && props.member) {
    selectedMembershipTypeId.value = props.member.membership_type_id.toString()
    selectedStartDate.value = (new Date().toISOString().split('T')[0] || '')
  }
})

// Computed date value for calendar
const startDateValue = computed({
  get: () => {
    if (!selectedStartDate.value) return undefined
    const datePart = selectedStartDate.value.split('T')[0]
    return datePart ? parseDate(datePart) : undefined
  },
  set: (val) => {
    selectedStartDate.value = val ? val.toString() : (new Date().toISOString().split('T')[0] || '')
  },
})

// Calculate days remaining (can be negative if expired)
const daysRemaining = computed(() => {
  if (!props.member) return 0
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  const expiry = new Date(props.member.expiry_date)
  expiry.setHours(0, 0, 0, 0)
  const diffTime = expiry.getTime() - today.getTime()
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  return diffDays
})

// Get selected membership type details
const selectedMembershipType = computed(() => {
  if (!selectedMembershipTypeId.value || !membershipTypes.value) return null
  return membershipTypes.value.find(
    (type) => type.id.toString() === selectedMembershipTypeId.value
  )
})

// Calculate new expiry date
const calculatedExpiryDate = computed(() => {
  if (!props.member || !selectedMembershipType.value || !selectedStartDate.value) return null

  const startDate = new Date(selectedStartDate.value)
  startDate.setHours(0, 0, 0, 0)
  
  const currentExpiry = new Date(props.member.expiry_date)
  currentExpiry.setHours(0, 0, 0, 0)
  
  const today = new Date()
  today.setHours(0, 0, 0, 0)

  let newExpiryDate: Date

  // If start date is today or before current expiry (for active/recently expired members)
  if (startDate <= today) {
    // Add duration to the later of: current expiry or today
    const baseDate = currentExpiry > today ? currentExpiry : today
    newExpiryDate = new Date(baseDate)
    newExpiryDate.setDate(newExpiryDate.getDate() + selectedMembershipType.value.duration_days)
  } else {
    // Start date is in the future - calculate from that date
    newExpiryDate = new Date(startDate)
    newExpiryDate.setDate(newExpiryDate.getDate() + selectedMembershipType.value.duration_days)
  }

  return newExpiryDate.toISOString().split('T')[0]
})

// Format date for display
function formatDate(dateString: string) {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

// Renewal mutation
const renewMutation = useMutation({
  mutationFn: async () => {
    if (!props.member || !calculatedExpiryDate.value) {
      throw new Error('Invalid renewal data')
    }

    const response = await axiosInstance.put(`/members/${props.member.id}`, {
      membership_type_id: selectedMembershipTypeId.value,
      start_date: selectedStartDate.value,
      expiry_date: calculatedExpiryDate.value,
      status: 'active',
    })
    return response.data
  },
  onSuccess: () => {
    queryClient.invalidateQueries({ queryKey: ['members-expiring'] })
    queryClient.invalidateQueries({ queryKey: ['members'] })
    queryClient.invalidateQueries({ queryKey: ['member', props.member?.id] })
    toast.success('Membership renewed successfully')
    emit('renewed')
    emit('update:open', false)
  },
  onError: (error: any) => {
    const message = error?.response?.data?.message || 'Failed to renew membership'
    toast.error(message)
  },
})

function handleRenew() {
  if (!selectedMembershipTypeId.value) {
    toast.error('Please select a membership type')
    return
  }
  if (!selectedStartDate.value) {
    toast.error('Please select a start date')
    return
  }
  renewMutation.mutate()
}

function handleClose() {
  if (!renewMutation.isPending.value) {
    emit('update:open', false)
  }
}
</script>

<template>
  <Dialog :open="open" @update:open="handleClose">
    <DialogContent class="sm:max-w-[500px]">
      <DialogHeader>
        <DialogTitle>Renew Membership</DialogTitle>
        <DialogDescription>
          {{ member?.full_name }} - Renew or extend membership
        </DialogDescription>
      </DialogHeader>

      <div class="space-y-5 py-4">
        <!-- Current Status Info -->
        <div
          v-if="member"
          class="p-3 rounded-lg bg-surface-800/50 border border-surface-700"
        >
          <div class="space-y-1">
            <div class="flex justify-between text-sm">
              <span class="text-surface-400">Current Expiry:</span>
              <span class="font-medium">{{ formatDate(member.expiry_date) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-surface-400">Days Remaining:</span>
              <span
                :class="[
                  'font-semibold',
                  daysRemaining < 0
                    ? 'text-red-500'
                    : daysRemaining <= 7
                      ? 'text-yellow-500'
                      : 'text-green-500',
                ]"
              >
                {{ daysRemaining < 0 ? `Expired ${Math.abs(daysRemaining)} days ago` : `${daysRemaining} days` }}
              </span>
            </div>
          </div>
        </div>

        <!-- Membership Type Selection -->
        <div class="space-y-2">
          <Label>Membership Type *</Label>
          <Select v-model="selectedMembershipTypeId" :disabled="loadingTypes">
            <SelectTrigger>
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
                {{ type.name }} - {{ type.duration_days }} days
                <span v-if="type.price" class="text-surface-400 ml-2">
                  (${{ type.price }})
                </span>
              </SelectItem>
            </SelectContent>
          </Select>
        </div>

        <!-- Start Date Selection -->
        <div class="space-y-2">
          <Label>Renewal Start Date *</Label>
          <Popover v-model:open="startDateOpen">
            <PopoverTrigger as-child>
              <Button
                variant="outline"
                class="w-full justify-start text-left font-normal"
              >
                <Calendar class="mr-2 h-4 w-4" />
                {{
                  startDateValue
                    ? df.format(startDateValue.toDate(getLocalTimeZone()))
                    : 'Pick a date'
                }}
              </Button>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0">
              <CalendarComponent
                v-model="startDateValue"
                initial-focus
                @update:model-value="startDateOpen = false"
              />
            </PopoverContent>
          </Popover>
          <p class="text-xs text-surface-400">
            Select today to start immediately, or a future date to schedule renewal
          </p>
        </div>

        <!-- Calculation Preview -->
        <div
          v-if="calculatedExpiryDate && selectedMembershipType"
          class="p-4 rounded-lg bg-brand-500/10 border border-brand-500/20"
        >
          <div class="flex items-start gap-3">
            <AlertCircle class="w-5 h-5 text-brand-500 flex-shrink-0 mt-0.5" />
            <div class="space-y-2 flex-1">
              <p class="text-sm font-medium text-brand-500">Renewal Calculation</p>
              <div class="space-y-1 text-sm text-foreground/80">
                <div class="flex justify-between">
                  <span>Start Date:</span>
                  <span class="font-medium">{{ formatDate(selectedStartDate) }}</span>
                </div>
                <div class="flex justify-between">
                  <span>Duration:</span>
                  <span class="font-medium">
                    {{ selectedMembershipType.duration_days }} days
                  </span>
                </div>
                <div class="flex justify-between pt-2 border-t border-brand-500/20">
                  <span class="font-semibold">New Expiry:</span>
                  <span class="font-semibold text-brand-500">
                    {{ formatDate(calculatedExpiryDate) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <DialogFooter>
        <Button
          variant="outline"
          @click="handleClose"
          :disabled="renewMutation.isPending.value"
        >
          Cancel
        </Button>
        <Button @click="handleRenew" :disabled="renewMutation.isPending.value">
          <Loader2 v-if="renewMutation.isPending.value" class="w-4 h-4 mr-2 animate-spin" />
          {{ renewMutation.isPending.value ? 'Renewing...' : 'Renew Membership' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
