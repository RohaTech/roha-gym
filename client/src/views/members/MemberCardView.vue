<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useMemberCard } from '@/composables/useMemberCard'
import { useCardDownload } from '@/composables/useCardDownload'
import MembershipCard from '@/components/membership/MembershipCard.vue'
import { Button } from '@/components/ui/button'

const route = useRoute()
const memberId = computed(() => Number(route.params.memberId))

const { data, isLoading, isError } = useMemberCard(memberId)

const cardRef = ref<HTMLElement | null>(null)
const { download, isDownloading } = useCardDownload(
    cardRef,
    computed(() => data.value?.member.name ?? '')
)
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center flex-col gap-6 p-8">
        <div v-if="isLoading" class="text-gray-500 text-sm">Loading card...</div>

        <div v-else-if="isError" class="text-red-500 text-sm">Failed to load membership card.</div>

        <template v-else-if="data">
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
                />
            </div>

            <Button @click="download" :disabled="isDownloading">
                {{ isDownloading ? 'Downloading...' : 'Download Card' }}
            </Button>
        </template>
    </div>
</template>
