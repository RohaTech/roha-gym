<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useMemberCard } from '@/composables/useMemberCard'
import MembershipCard from '@/components/membership/MembershipCard.vue'
import { ArrowLeft } from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()

const memberId = computed(() => Number(route.params.memberId))

const { data, isLoading, isError } = useMemberCard(memberId)
</script>

<template>
  <div class="min-h-screen bg-surface-50 dark:bg-surface-950 flex flex-col items-center gap-6 p-8">
    <!-- Back button -->
    <div class="w-full" style="max-width: calc(54mm * 1.6)">
      <button
        class="flex items-center gap-1 text-sm text-surface-500 hover:text-surface-800 dark:hover:text-surface-200 transition-colors"
        @click="router.back()"
      >
        <ArrowLeft class="w-4 h-4" />
        Back
      </button>
    </div>

    <div v-if="isLoading" class="text-surface-500 text-sm">Loading card...</div>

    <div v-else-if="isError" class="text-danger-500 text-sm">Failed to load membership card.</div>

    <template v-else-if="data">
      <!-- Card preview (scaled for display) -->
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
            :gym-logo="data.gym.logo_url ?? ''"
            :gym-name="data.gym.name"
          />
        </div>
      </div>
    </template>
  </div>
</template>
