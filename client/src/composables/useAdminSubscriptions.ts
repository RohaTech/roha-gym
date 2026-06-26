import { ref, computed } from 'vue'
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query'
import axiosInstance from '@/api/axiosInstance'
import type {
    PaginatedSubscriptions,
    SubscriptionDetail,
    RecordPaymentPayload,
} from '@/types/admin'

export function useAdminSubscriptions() {
    const queryClient = useQueryClient()

    const search = ref('')
    const statusFilter = ref<string>('all')
    const page = ref(1)
    const perPage = 15

    const queryKey = computed(() => ['admin-subscriptions', search.value, statusFilter.value, page.value])

    const { data, isLoading, isError } = useQuery<PaginatedSubscriptions>({
        queryKey,
        queryFn: async () => {
            const params = new URLSearchParams()
            params.set('page', String(page.value))
            params.set('per_page', String(perPage))
            if (search.value) params.set('search', search.value)
            if (statusFilter.value !== 'all') params.set('status', statusFilter.value)
            const { data } = await axiosInstance.get<PaginatedSubscriptions>(`/admin/subscriptions?${params}`)
            return data
        },
        staleTime: 2 * 60 * 1000,
    })

    return {
        data,
        isLoading,
        isError,
        search,
        statusFilter,
        page,
        ...useSubscriptionMutations(queryClient),
    }
}

/**
 * Fetch a single gym's subscription detail (summary + payment history).
 * Used by the gym edit page panel.
 */
export function useSubscriptionDetail() {
    const queryClient = useQueryClient()

    function fetchDetail(gymId: number): Promise<SubscriptionDetail> {
        return axiosInstance.get(`/admin/subscriptions/${gymId}`).then(r => r.data as SubscriptionDetail)
    }

    return {
        fetchDetail,
        ...useSubscriptionMutations(queryClient),
    }
}

/**
 * Shared record / delete payment mutations. Invalidates both the subscription
 * list and the gym list, since recording a payment can reactivate a gym.
 */
function useSubscriptionMutations(queryClient = useQueryClient()) {
    const invalidate = () => {
        queryClient.invalidateQueries({ queryKey: ['admin-subscriptions'] })
        queryClient.invalidateQueries({ queryKey: ['admin-gyms'] })
    }

    const recordMutation = useMutation({
        mutationFn: ({ gymId, payload }: { gymId: number; payload: RecordPaymentPayload }) =>
            axiosInstance.post(`/admin/subscriptions/${gymId}/payments`, payload),
        onSuccess: invalidate,
    })

    const deleteMutation = useMutation({
        mutationFn: (paymentId: number) => axiosInstance.delete(`/admin/subscriptions/payments/${paymentId}`),
        onSuccess: invalidate,
    })

    return {
        recordPayment: recordMutation.mutateAsync,
        isRecording: computed(() => recordMutation.isPending.value),
        deletePayment: deleteMutation.mutateAsync,
        isDeletingPayment: computed(() => deleteMutation.isPending.value),
    }
}
