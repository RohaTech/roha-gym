import { computed } from 'vue'
import type { ComputedRef, Ref } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import axiosInstance from '@/api/axiosInstance'
import { useAuthStore } from '@/store/authStore'
import type { MemberCardData } from '@/types/member'

export function useMemberCard(memberId: Ref<number> | ComputedRef<number>) {
    const authStore = useAuthStore()
    const gymId = computed(() => authStore.user?.id)

    const { data, isLoading, isError } = useQuery({
        queryKey: ['member-card', gymId, memberId],
        queryFn: async () => {
            const response = await axiosInstance.get<MemberCardData>(
                `/gyms/${gymId.value}/members/${memberId.value}/card-data`
            )
            return response.data
        },
        enabled: computed(() => !!memberId.value && !!gymId.value),
    })

    return { data, isLoading, isError }
}
