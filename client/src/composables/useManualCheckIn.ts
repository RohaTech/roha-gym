import { ref } from 'vue'
import { z } from 'zod'
import { useCheckIn } from './useCheckIn'

const codeSchema = z.string().length(5)

export function useManualCheckIn() {
  const { mutate } = useCheckIn()
  const code = ref('')
  const validationError = ref<string | null>(null)

  const clearError = () => {
    validationError.value = null
  }

  const submit = () => {
    const trimmed = code.value.trim().toUpperCase()
    const result = codeSchema.safeParse(trimmed)
    if (!result.success) {
      validationError.value = 'Code must be exactly 5 characters'
      return
    }

    validationError.value = null
    mutate(trimmed, 'manual')
  }

  return {
    code,
    submit,
    validationError,
    clearError,
  }
}
