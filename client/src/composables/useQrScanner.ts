import { ref, computed } from 'vue'
import { useCheckIn } from './useCheckIn'
import { useCheckInStore } from '@/store/checkIn'

export function useQrScanner() {
  const { mutate } = useCheckIn()
  const checkInStore = useCheckInStore()
  const isOnCooldown = ref(false)
  const cameraError = ref<string | null>(null)

  const isPaused = computed(() => {
    return isOnCooldown.value ||
      checkInStore.status === 'pending' ||
      checkInStore.status === 'success' ||
      checkInStore.status === 'failed'
  })

  const onDecode = (rawValue: string) => {
    if (isPaused.value) return
    
    isOnCooldown.value = true
    mutate(rawValue, 'qr')
    
    setTimeout(() => {
      isOnCooldown.value = false
    }, 2000)
  }

  const onError = (error: any) => {
    cameraError.value = error?.message || String(error)
  }

  return {
    onDecode,
    onError,
    isOnCooldown,
    cameraError,
    isPaused,
  }
}
