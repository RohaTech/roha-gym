import { ref, computed } from 'vue'
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query'
import axiosInstance from '@/api/axiosInstance'
import type { GymSummary, PaginatedGyms, GymFormPayload } from '@/types/admin'

export function useAdminGyms() {
    const queryClient = useQueryClient()

    const search = ref('')
    const statusFilter = ref<string>('all')
    const page = ref(1)
    const perPage = 15

    const queryKey = computed(() => ['admin-gyms', search.value, statusFilter.value, page.value])

    const { data, isLoading, isError } = useQuery<PaginatedGyms>({
        queryKey,
        queryFn: async () => {
            const params = new URLSearchParams()
            params.set('page', String(page.value))
            params.set('per_page', String(perPage))
            if (search.value) params.set('search', search.value)
            if (statusFilter.value !== 'all') {
                const statusMap: Record<string, string> = { active: '1', suspended: '0', pending: '2' }
                params.set('status', statusMap[statusFilter.value] ?? '0')
            }
            const { data } = await axiosInstance.get<PaginatedGyms>(`/admin/gyms?${params}`)
            return data
        },
        staleTime: 2 * 60 * 1000,
    })

    const invalidate = () => queryClient.invalidateQueries({ queryKey: ['admin-gyms'] })

    const deleteMutation = useMutation({
        mutationFn: (id: number) => axiosInstance.delete(`/admin/gyms/${id}`),
        onSuccess: invalidate,
    })

    const statusMutation = useMutation({
        mutationFn: ({ id, status }: { id: number; status: number }) =>
            axiosInstance.patch(`/admin/gyms/${id}/status`, { status }),
        onSuccess: invalidate,
    })

    return {
        data,
        isLoading,
        isError,
        search,
        statusFilter,
        page,
        deleteGym: deleteMutation.mutateAsync,
        isDeleting: computed(() => deleteMutation.isPending.value),
        updateStatus: statusMutation.mutateAsync,
        isUpdatingStatus: computed(() => statusMutation.isPending.value),
    }
}

export function useAdminGymForm() {
    const queryClient = useQueryClient()
    const invalidate = () => queryClient.invalidateQueries({ queryKey: ['admin-gyms'] })

    const createMutation = useMutation({
        mutationFn: (payload: GymFormPayload) => {
            const form = new FormData()
            form.append('name', payload.name)
            form.append('phone', payload.phone)
            form.append('address', payload.address)
            if (payload.password) form.append('password', payload.password)
            if (payload.logo) form.append('logo', payload.logo)
            return axiosInstance.post('/admin/gyms', form)
        },
        onSuccess: invalidate,
    })

    const updateMutation = useMutation({
        mutationFn: ({ id, payload }: { id: number; payload: GymFormPayload }) => {
            const form = new FormData()
            form.append('_method', 'PUT')
            form.append('name', payload.name)
            form.append('phone', payload.phone)
            form.append('address', payload.address)
            if (payload.password) form.append('password', payload.password)
            if (payload.logo) form.append('logo', payload.logo)
            return axiosInstance.post(`/admin/gyms/${id}`, form)
        },
        onSuccess: invalidate,
    })

    function fetchGym(id: number): Promise<GymSummary> {
        return axiosInstance.get(`/admin/gyms/${id}`).then(r => r.data.gym as GymSummary)
    }

    return {
        createGym: createMutation.mutateAsync,
        updateGym: updateMutation.mutateAsync,
        isSaving: computed(() => createMutation.isPending.value || updateMutation.isPending.value),
        fetchGym,
    }
}
