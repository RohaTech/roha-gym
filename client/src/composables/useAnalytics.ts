import { computed, ref } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import axiosInstance from '@/api/axiosInstance'
import { useAuthStore } from '@/store/authStore'
import type { AnalyticsData, RangeOption } from '@/types/analytics'

export function useAnalytics() {
    const authStore = useAuthStore()
    const gymId = computed(() => authStore.user?.id as number)
    const range = ref<RangeOption>('30d')

    const query = useQuery<AnalyticsData>({
        queryKey: ['analytics', gymId, range],
        queryFn: async () => {
            const { data } = await axiosInstance.get<AnalyticsData>(
                `/gyms/${gymId.value}/analytics?range=${range.value}`,
            )
            return data
        },
        staleTime: 5 * 60 * 1000,
        enabled: computed(() => !!gymId.value),
    })

    return {
        data: query.data,
        isLoading: query.isLoading,
        isError: query.isError,
        range,
    }
}
