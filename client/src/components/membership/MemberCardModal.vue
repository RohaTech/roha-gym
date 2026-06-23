<script setup lang="ts">
import { computed } from 'vue'
import { useMemberCard } from '@/composables/useMemberCard'
import MembershipCard from '@/components/membership/MembershipCard.vue'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'

const props = defineProps<{
  open: boolean
  memberId: number | null
}>()

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void
}>()

const memberId = computed(() => props.memberId ?? 0)
const { data, isLoading, isError } = useMemberCard(memberId)
</script>

<template>
  <Dialog :open="open" @update:open="(val) => emit('update:open', val)">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>Membership Card</DialogTitle>
      </DialogHeader>

      <div class="flex flex-col items-center gap-6 py-2">
        <div v-if="isLoading" class="text-muted-foreground text-sm">Loading card...</div>
        <div v-else-if="isError" class="text-danger-500 text-sm">
          Failed to load membership card.
        </div>

        <template v-else-if="data">
          <div class="relative" style="width: calc(54mm * 1.6); height: calc(85.6mm * 1.6)">
            <div class="absolute top-0 left-0 origin-top-left" style="transform: scale(1.6)">
              <MembershipCard
                :member-name="data.member.name"
                :member-photo="data.member.photo_url ?? ''"
                :member-code="data.member.code"
                :member-slug="data.member.slug"
                :member-phone="data.member.phone ?? ''"
                :membership-type="data.member.membership_type"
                :start-date="data.member.start_date"
                :expiry-date="data.member.expiry_date"
                :gym-logo="data.gym.logo_url ?? ''"
                :gym-name="data.gym.name"
              />
            </div>
          </div>
        </template>
      </div>
    </DialogContent>
  </Dialog>
</template>
