<script setup lang="ts">
import { ref, computed } from 'vue'
import { Download } from 'lucide-vue-next'
import { useMemberCard } from '@/composables/useMemberCard'
import { useCardDownload } from '@/composables/useCardDownload'
import MembershipCard from '@/components/membership/MembershipCard.vue'
import { Button } from '@/components/ui/button'
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

const cardRef = ref<HTMLElement | null>(null)
const { download, isDownloading } = useCardDownload(
  cardRef,
  computed(() => data.value?.member.name ?? ''),
)
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
          <!-- Outer div reserves layout space for the scaled card -->
          <div class="relative" style="width: calc(54mm * 1.6); height: calc(85.6mm * 1.6)">
            <!-- Scale wrapper — visual only, does NOT affect cardRef capture -->
            <div class="absolute top-0 left-0 origin-top-left" style="transform: scale(1.6)">
              <div ref="cardRef">
                <MembershipCard
                  :member-name="data.member.name"
                  :member-photo="data.member.photo_url ?? ''"
                  :member-code="data.member.code"
                  :member-slug="data.member.slug"
                  :membership-type="data.member.membership_type"
                  :start-date="data.member.start_date"
                  :expiry-date="data.member.expiry_date"
                  :gym-logo="data.gym.logo_url ?? ''"
                  :gym-name="data.gym.name"
                />
              </div>
            </div>
          </div>

          <Button class="gap-2 w-full" @click="download" :disabled="isDownloading">
            <Download class="w-4 h-4" />
            {{ isDownloading ? 'Downloading...' : 'Download Card' }}
          </Button>
        </template>
      </div>
    </DialogContent>
  </Dialog>
</template>
