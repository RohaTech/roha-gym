import { useMutation } from '@tanstack/vue-query'
import axiosInstance from '@/api/axiosInstance'
import { useCheckInStore } from '@/store/checkIn'
import { useAuthStore } from '@/store/authStore'
import type { CheckInMethod, CheckInResult } from '@/types/checkIn'

export function useCheckIn() {
  const checkInStore = useCheckInStore()
  const authStore = useAuthStore()

  const mutation = useMutation({
    mutationFn: async ({ identifier, method }: { identifier: string; method: CheckInMethod }) => {
      const gymId = authStore.user?.id
      const response = await axiosInstance.post(`/gyms/${gymId}/check-in`, { identifier, method })
      return response.data as CheckInResult
    },
    onSuccess: (data) => {
      checkInStore.setResult(data)
    },
    onError: () => {
      checkInStore.setResult({ success: false, reason: 'not_found' })
    },
  })

  return {
    mutate: (identifier: string, method: CheckInMethod) => mutation.mutate({ identifier, method }),
    isPending: mutation.isPending,
  }
}
