import { ref } from 'vue'
import { useCheckIn } from './useCheckIn'

export function useQrScanner() {
  const { mutate } = useCheckIn()
  const isOnCooldown = ref(false)
  const cameraError = ref<string | null>(null)

  const onDecode = (rawValue: string) => {
    if (isOnCooldown.value) return
    
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
  }
}
