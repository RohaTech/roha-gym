import { ref } from 'vue'
import { z } from 'zod'
import { useCheckIn } from './useCheckIn'

const codeSchema = z.string().length(5).regex(/^\d+$/)

export function useManualCheckIn() {
  const { mutate } = useCheckIn()
  const code = ref('')
  const validationError = ref<string | null>(null)

  const submit = () => {
    const result = codeSchema.safeParse(code.value)
    if (!result.success) {
      validationError.value = 'Code must be exactly 5 digits'
      return
    }

    validationError.value = null
    mutate(code.value, 'manual')
  }

  return {
    code,
    submit,
    validationError,
  }
}
