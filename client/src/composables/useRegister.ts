import { computed, ref, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useMutation } from '@tanstack/vue-query'
import { toast } from 'vue-sonner'
import { useAuthStore } from '@/store/authStore'
import { useLanguageStore } from '@/store/languageStore'
import { registerSchema, type RegisterFormData } from '@/schemas/registerSchema'
import type { AuthError } from '@/types/authTypes'
import axiosInstance from '@/api/axiosInstance'
import { normalizeErrors, validateFormWithZod, type NormalizedErrors } from '@/utils/errorFormatter'

export function useRegister() {
  const router = useRouter()
  const authStore = useAuthStore()
  const languageStore = useLanguageStore()

  const form = ref<RegisterFormData>({
    name: '',
    phone: '',
    address: '',
    password: '',
    password_confirmation: '',
  })

  type RegisterFields = keyof RegisterFormData | 'logo'

  const errors = ref<NormalizedErrors<RegisterFields>>({})
  const showPassword = ref(false)
  const showConfirmPassword = ref(false)
  const logoFile = ref<File | null>(null)
  const logoPreview = ref<string>('')
  const logoFileName = ref<string>('')

  function clearError(field: RegisterFields) {
    if (errors.value?.[field]) {
      delete errors.value[field]
    }
  }

  function handleLogoChange(event: Event) {
    clearError('logo')
    const target = event.target as HTMLInputElement
    const file = target.files?.[0]

    if (!file) {
      clearLogo()
      return
    }

    // Validate file type
    const validTypes = ['image/jpeg', 'image/png', 'image/webp']
    if (!validTypes.includes(file.type)) {
      errors.value.logo = 'Only JPEG, PNG, and WebP images are allowed'
      clearLogo()
      return
    }

    // Validate file size (2MB = 2 * 1024 * 1024 bytes)
    if (file.size > 2 * 1024 * 1024) {
      errors.value.logo = 'File size must not exceed 2MB'
      clearLogo()
      return
    }

    logoFile.value = file
    logoFileName.value = file.name
    logoPreview.value = URL.createObjectURL(file)
    clearError('logo')
  }

  function clearLogo() {
    if (logoPreview.value) {
      URL.revokeObjectURL(logoPreview.value)
    }
    logoFile.value = null
    logoPreview.value = ''
    logoFileName.value = ''
  }

  const registerMutation = useMutation({
    mutationFn: async (formData: FormData) => {
      const response = await axiosInstance.post('/auth/register', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })
      return response.data
    },
    onSuccess: async (data: { token: string; user: unknown }) => {
      const { token, user } = data
      authStore.token = token
      authStore.user = user as typeof authStore.user
      
      // Initialize the auth store to set userRole and isInitialized
      await authStore.initialize()
      
      const Cookies = (await import('js-cookie')).default
      Cookies.set('access_token', token)

      await languageStore.fetchFrontLanguages.refetch()
      
      // Redirect based on user role
      if (authStore.userRole === 'admin') {
        router.push({ name: 'admin-dashboard' })
      } else {
        router.push({ name: 'user-dashboard' })
      }
    },
    onError: (err: any) => {
      const data = err?.response?.data as AuthError | undefined
      if (data?.errors) {
        errors.value = normalizeErrors<RegisterFields>(data.errors)
        return
      }

      const message = data?.message || 'An unexpected error occurred. Please try again.'
      toast.error(message)
    },
  })

  const isSubmitting = computed(() => registerMutation.isPending.value)

  async function handleSubmit() {
    errors.value = {}
    const validationErrors = validateFormWithZod<RegisterFields>(registerSchema, form.value)
    if (validationErrors) {
      errors.value = validationErrors
      return
    }

    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('phone', form.value.phone)
    formData.append('address', form.value.address)
    formData.append('password', form.value.password)
    formData.append('password_confirmation', form.value.password_confirmation)

    if (logoFile.value) {
      formData.append('logo', logoFile.value)
    }

    try {
      await registerMutation.mutateAsync(formData)
    } catch {
      // Errors are handled in the mutation's onError handler.
    }
  }

  function togglePassword() {
    showPassword.value = !showPassword.value
  }

  function toggleConfirmPassword() {
    showConfirmPassword.value = !showConfirmPassword.value
  }

  // Cleanup preview URL on unmount
  onUnmounted(() => {
    if (logoPreview.value) {
      URL.revokeObjectURL(logoPreview.value)
    }
  })

  return {
    form,
    errors,
    isSubmitting,
    showPassword,
    showConfirmPassword,
    logoFile,
    logoPreview,
    logoFileName,
    handleSubmit,
    handleLogoChange,
    clearError,
    togglePassword,
    toggleConfirmPassword,
  }
}
