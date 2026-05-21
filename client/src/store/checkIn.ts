import { defineStore } from 'pinia'
import type { CheckInResult } from '@/types/checkIn'

export const useCheckInStore = defineStore('checkIn', {
  state: () => ({
    status: 'idle' as 'idle' | 'pending' | 'success' | 'failed',
    lastResult: null as CheckInResult | null,
    failureReason: null as 'not_found' | 'expired' | 'limit_reached' | null,
  }),
  actions: {
    setResult(result: CheckInResult) {
      this.lastResult = result
      if (result.success) {
        this.status = 'success'
        this.failureReason = null
      } else {
        this.status = 'failed'
        this.failureReason = result.reason
      }
    },
    reset() {
      this.status = 'idle'
      this.lastResult = null
      this.failureReason = null
    },
  },
})
