<script setup lang="ts">
import { reactive, computed, watch } from 'vue'
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import {
  Select, SelectContent, SelectItem, SelectTrigger, SelectValue,
} from '@/components/ui/select'
import {
  Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle,
} from '@/components/ui/dialog'
import { useSubscriptionDetail } from '@/composables/useAdminSubscriptions'
import type { RecordPaymentPayload } from '@/types/admin'

const props = defineProps<{
  open: boolean
  gymId: number | null
  gymName?: string
}>()

const emit = defineEmits<{
  'update:open': [value: boolean]
  saved: []
}>()

const { recordPayment, isRecording } = useSubscriptionDetail()

function todayStr(): string {
  return new Date().toISOString().slice(0, 10)
}

const form = reactive<{
  plan_type: string
  months: number
  amount: number | undefined
  currency: string
  paid_at: string
  note: string
}>({
  plan_type: 'monthly',
  months: 1,
  amount: undefined,
  currency: 'ETB',
  paid_at: todayStr(),
  note: '',
})

const errors = reactive<Record<string, string>>({})

// Reset the form each time the dialog opens.
watch(() => props.open, (isOpen) => {
  if (isOpen) {
    form.plan_type = 'monthly'
    form.months = 1
    form.amount = undefined
    form.currency = 'ETB'
    form.paid_at = todayStr()
    form.note = ''
    Object.keys(errors).forEach(k => delete errors[k])
  }
})

const isMonthly = computed(() => form.plan_type === 'monthly')

async function handleSave() {
  Object.keys(errors).forEach(k => delete errors[k])

  if (form.amount === undefined || form.amount < 0) {
    errors.amount = 'Enter a valid amount'
  }
  if (isMonthly.value && (!form.months || form.months < 1)) {
    errors.months = 'At least 1 month'
  }
  if (!form.paid_at) {
    errors.paid_at = 'Payment date is required'
  }
  if (Object.keys(errors).length || props.gymId === null) return

  const payload: RecordPaymentPayload = {
    plan_type: form.plan_type as 'lifetime' | 'monthly',
    months: isMonthly.value ? form.months : null,
    amount: form.amount as number,
    currency: form.currency || 'ETB',
    paid_at: form.paid_at,
    note: form.note || null,
  }

  try {
    await recordPayment({ gymId: props.gymId, payload })
    toast.success('Payment recorded')
    emit('saved')
    emit('update:open', false)
  } catch (e: any) {
    const serverErrors = e?.response?.data?.errors
    if (serverErrors) {
      Object.entries(serverErrors).forEach(([field, msgs]) => {
        errors[field] = (msgs as string[])[0] ?? ''
      })
    } else {
      toast.error(e?.response?.data?.message ?? 'Failed to record payment')
    }
  }
}
</script>

<template>
  <Dialog :open="open" @update:open="(v) => emit('update:open', v)">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>{{ $lang.adminSubsDialogTitle }}</DialogTitle>
        <DialogDescription>
          {{ gymName ? gymName + ' — ' : '' }}{{ $lang.adminSubsDialogDesc }}
        </DialogDescription>
      </DialogHeader>

      <div class="space-y-4">
        <!-- Plan type -->
        <div class="space-y-2">
          <Label>{{ $lang.adminSubsFieldPlan }}</Label>
          <Select v-model="form.plan_type">
            <SelectTrigger class="w-full">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="monthly">{{ $lang.adminSubsPlanMonthly }}</SelectItem>
              <SelectItem value="lifetime">{{ $lang.adminSubsPlanLifetime }}</SelectItem>
            </SelectContent>
          </Select>
        </div>

        <!-- Months (monthly only) -->
        <div v-if="isMonthly" class="space-y-2">
          <Label for="months">{{ $lang.adminSubsFieldMonths }} *</Label>
          <Input id="months" v-model.number="form.months" type="number" min="1" max="120" />
          <small v-if="errors.months" class="text-danger-500">{{ errors.months }}</small>
        </div>

        <!-- Amount + currency -->
        <div class="grid grid-cols-3 gap-3">
          <div class="col-span-2 space-y-2">
            <Label for="amount">{{ $lang.adminSubsFieldAmount }} *</Label>
            <Input id="amount" v-model.number="form.amount" type="number" min="0" step="0.01" placeholder="0.00" />
            <small v-if="errors.amount" class="text-danger-500">{{ errors.amount }}</small>
          </div>
          <div class="space-y-2">
            <Label for="currency">{{ $lang.adminSubsFieldCurrency }}</Label>
            <Input id="currency" v-model="form.currency" />
          </div>
        </div>

        <!-- Paid at -->
        <div class="space-y-2">
          <Label for="paid_at">{{ $lang.adminSubsFieldPaidAt }} *</Label>
          <Input id="paid_at" v-model="form.paid_at" type="date" />
          <small v-if="errors.paid_at" class="text-danger-500">{{ errors.paid_at }}</small>
        </div>

        <!-- Note -->
        <div class="space-y-2">
          <Label for="note">{{ $lang.adminSubsFieldNote }}</Label>
          <Textarea id="note" v-model="form.note" :placeholder="$lang.adminSubsFieldNotePlaceholder" rows="2" />
        </div>
      </div>

      <DialogFooter>
        <Button variant="outline" :disabled="isRecording" @click="emit('update:open', false)">
          {{ $lang.adminSubsCancel }}
        </Button>
        <Button :disabled="isRecording" @click="handleSave">
          {{ isRecording ? $lang.adminSubsSaving : $lang.adminSubsSave }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
