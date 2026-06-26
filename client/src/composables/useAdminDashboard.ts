import { useQuery } from '@tanstack/vue-query'
import axiosInstance from '@/api/axiosInstance'
import type { AdminDashboardData } from '@/types/admin'

export function useAdminDashboard() {
    const query = useQuery<AdminDashboardData>({
        queryKey: ['admin-dashboard'],
        queryFn: async () => {
            const { data } = await axiosInstance.get<AdminDashboardData>('/admin/dashboard')
            return data
        },
        staleTime: 5 * 60 * 1000,
    })

    return {
        data: query.data,
        isLoading: query.isLoading,
        isError: query.isError,
        refetch: query.refetch,
    }
}
