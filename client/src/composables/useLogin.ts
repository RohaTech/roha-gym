import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useMutation } from '@tanstack/vue-query'
import { toast } from 'vue-sonner'
import { useAuthStore } from '@/store/authStore'
import { useLanguageStore } from '@/store/languageStore'
import { loginSchema, type LoginFields, type LoginFormData } from '@/schemas/loginSchema'
import type { AuthError } from '@/types/authTypes'
import { normalizeErrors, validateFormWithZod, type NormalizedErrors } from '@/utils/errorFormatter'

export function useLogin() {
  const router = useRouter()
  const authStore = useAuthStore()
  const languageStore = useLanguageStore()

  function redirectToDashboard() {
    if (authStore.userRole === 'admin') {
      return router.replace({ name: 'admin-dashboard' })
    }

    return router.replace({ name: 'user-dashboard' })
  }

  const form = ref<LoginFormData>({
    phone: '',
    password: '',
  })

  const errors = ref<NormalizedErrors<LoginFields>>({})
  const showPassword = ref(false)

  function clearError(field: LoginFields) {
    if (errors.value?.[field]) {
      delete errors.value[field]
    }
  }

  const loginMutation = useMutation({
    mutationFn: async () => authStore.login(form.value.phone, form.value.password),
    onSuccess: async () => {
      await languageStore.fetchFrontLanguages.refetch()
      await redirectToDashboard()
    },
    onError: (err: any) => {
      const data = err?.response?.data as AuthError | undefined
      if (data?.errors) {
        errors.value = normalizeErrors<LoginFields>(data.errors)
        return
      }

      const message = data?.message || 'An unexpected error occurred. Please try again.'
      toast.error(message)
    },
  })

  const isSubmitting = computed(() => loginMutation.isPending.value)

  async function handleSubmit() {
    errors.value = {}
    const validationErrors = validateFormWithZod<LoginFields>(loginSchema, form.value)
    if (validationErrors) {
      errors.value = validationErrors
      return
    }

    try {
      await loginMutation.mutateAsync()
    } catch {
      // Errors are handled in the mutation's onError handler.
    }
  }

  function togglePassword() {
    showPassword.value = !showPassword.value
  }

  return {
    form,
    errors,
    isSubmitting,
    showPassword,
    handleSubmit,
    clearError,
    togglePassword,
  }
}
