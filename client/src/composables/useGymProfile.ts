import { computed, onUnmounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query'
import { toast } from 'vue-sonner'
import axiosInstance from '@/api/axiosInstance'
import { useAuthStore } from '@/store/authStore'
import { gymProfileSchema } from '@/schemas/gymProfileSchema'
import type { GymProfile, GymProfileFields, GymProfileFormData } from '@/types/gymProfileTypes'
import { normalizeErrors, validateFormWithZod, type NormalizedErrors } from '@/utils/errorFormatter'
import { storageUrl } from '@/constants'

export function useGymProfile() {
  const authStore = useAuthStore()
  const queryClient = useQueryClient()
  const route = useRouter()

  const form = ref<GymProfileFormData>({
    name: '',
    phone: '',
    address: '',
  })

  const errors = ref<NormalizedErrors<GymProfileFields>>({})
  const logoFile = ref<File | null>(null)
  const logoPreview = ref<string>('')
  const logoInput = ref<HTMLInputElement | null>(null)

  function clearError(field: GymProfileFields) {
    if (errors.value?.[field]) {
      delete errors.value[field]
    }
  }

  const {
    data: profile,
    isLoading,
    isError,
  } = useQuery({
    queryKey: ['gym-profile'],
    queryFn: async () => {
      try {
        const response = await axiosInstance.get<GymProfile>('/gym/profile')
        return response.data
      } catch (error: any) {
        toast.error(error?.response?.data?.message || 'Failed to load profile')
        throw error
      }
    },
  })

  watch(
    profile,
    (data) => {
      if (!data) return
      form.value.name = data.name ?? ''
      form.value.phone = data.phone ?? ''
      form.value.address = data.address ?? ''
    },
    { immediate: true },
  )

  const updateMutation = useMutation({
    mutationFn: async (formData: FormData) => {
      const response = await axiosInstance.post('/gym/profile', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      return response.data
    },
    onSuccess: async (data) => {
      queryClient.invalidateQueries({ queryKey: ['gym-profile'] })
      await authStore.fetchUser()
      toast.success('Profile updated successfully')
    },
    onError: (err: any) => {
      const data = err?.response?.data
      if (data?.errors) {
        errors.value = normalizeErrors<GymProfileFields>(data.errors)
        return
      }
      toast.error(data?.message || 'An unexpected error occurred.')
    },
  })

  const isSubmitting = computed(() => updateMutation.isPending.value)

  async function handleSubmit() {
    errors.value = {}
    const validationErrors = validateFormWithZod<GymProfileFields>(gymProfileSchema, form.value)
    if (validationErrors) {
      errors.value = validationErrors
      return
    }

    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('phone', form.value.phone)
    formData.append('address', form.value.address)
    if (logoFile.value) formData.append('logo', logoFile.value)

    try {
      await updateMutation.mutateAsync(formData)
    } catch {
      // handled in onError
    }
  }

  function handleLogoChange(event: Event) {
    const target = event.target as HTMLInputElement
    const file = target.files?.[0]
    if (!file) return clearLogo()

    const validTypes = ['image/jpeg', 'image/png', 'image/webp']
    if (!validTypes.includes(file.type)) {
      toast.error('Only JPEG, PNG, and WebP images are allowed')
      return clearLogo()
    }
    if (file.size > 2 * 1024 * 1024) {
      toast.error('File size must not exceed 2MB')
      return clearLogo()
    }

    logoFile.value = file
    logoPreview.value = URL.createObjectURL(file)
  }

  function clearLogo() {
    if (logoPreview.value) URL.revokeObjectURL(logoPreview.value)
    logoFile.value = null
    logoPreview.value = ''
    if (logoInput.value) logoInput.value.value = ''
  }

  function triggerLogoUpload() {
    logoInput.value?.click()
  }

  function handleCancel() {
    errors.value = {}
    clearLogo()
    if (profile.value) {
      form.value.name = profile.value.name ?? ''
      form.value.phone = profile.value.phone ?? ''
      form.value.address = profile.value.address ?? ''
    }
    route.push({ name: 'user-dashboard' })
  }

  const existingLogoUrl = computed(() =>
    profile.value?.logo_path ? storageUrl(profile.value.logo_path) : null,
  )

  onUnmounted(() => {
    if (logoPreview.value) URL.revokeObjectURL(logoPreview.value)
  })

  return {
    form,
    errors,
    profile,
    isLoading,
    isError,
    isSubmitting,
    logoFile,
    logoPreview,
    logoInput,
    existingLogoUrl,
    clearError,
    handleSubmit,
    handleCancel,
    handleLogoChange,
    clearLogo,
    triggerLogoUpload,
  }
}
