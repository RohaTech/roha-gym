import type { ComputedRef } from 'vue'
import { computed } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import axiosInstance from '@/api/axiosInstance'
import { useAuthStore } from '@/store/authStore'

interface MemberCardData {
  member: {
    name: string
    photo_url: string | null
    code: string
    slug: string
    phone: string | null
    membership_type: string
    start_date: string
    expiry_date: string
  }
  gym: {
    name: string
    logo_url: string | null
  }
}

export function useMemberCard(memberId: ComputedRef<number>) {
  const authStore = useAuthStore()
  const gymId = computed(() => authStore.user?.id as number)

  return useQuery<MemberCardData>({
    queryKey: ['member-card', gymId, memberId],
    queryFn: async () => {
      const { data } = await axiosInstance.get<MemberCardData>(
        `/gyms/${gymId.value}/members/${memberId.value}/card-data`,
      )
      return data
    },
    enabled: computed(() => !!gymId.value && !!memberId.value),
  })
}
