import { computed } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import axiosInstance from '@/api/axiosInstance'
import { useAuthStore } from '@/store/authStore'
import type { DashboardData } from '@/types/analytics'

export function useDashboard() {
    const authStore = useAuthStore()
    const gymId = computed(() => authStore.user?.id as number)

    const query = useQuery<DashboardData>({
        queryKey: ['dashboard', gymId],
        queryFn: async () => {
            const { data } = await axiosInstance.get<DashboardData>(
                `/gyms/${gymId.value}/dashboard`,
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
    }
}
