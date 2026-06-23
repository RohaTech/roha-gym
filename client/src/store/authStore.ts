import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import Cookies from 'js-cookie'
import axiosInstance from '@/api/axiosInstance'
import type { UserRole } from '@/types/roleTypes'

export interface User {
  id: number
  name: string
  email: string | null
  phone?: string
  address?: string
  logo_path?: string | null
  role?: UserRole
  status?: number
}

export const useAuthStore = defineStore(
  'auth',
  () => {
    const user = ref<User | null>(null)
    const token = ref<string | null>(Cookies.get('access_token') ?? null)
    const loadingUser = ref(false)
    const isInitialized = ref(false)
    const userRole = ref<UserRole>('guest')

    const isAuthenticated = computed(() => !!token.value)

    function resolveRole(nextUser: User | null | undefined): UserRole {
      if (!nextUser) {
        return 'guest'
      }

      // Use the role from the backend if available
      if (nextUser.role === 'admin' || nextUser.role === 'user') {
        return nextUser.role
      }

      // Default to 'user' if no role is specified
      return 'user'
    }

    const login = async (phone: string, password: string) => {
      const response = await axiosInstance.post('/auth/login', { phone, password })
      const { token: newToken, user: newUser } = response.data
      token.value = newToken
      user.value = newUser
      userRole.value = resolveRole(newUser)
      Cookies.set('access_token', newToken)
      isInitialized.value = true
    }

    const fetchUser = async () => {
      if (!token.value) return
      loadingUser.value = true
      try {
        const response = await axiosInstance.get('/auth/me')
        user.value = response.data
        userRole.value = resolveRole(response.data)
        isInitialized.value = true
      } catch {
        logout()
      } finally {
        loadingUser.value = false
      }
    }

    const logout = async () => {
      try {
        if (token.value) await axiosInstance.post('/auth/logout')
      } catch {
        /* ignore */
      } finally {
        token.value = null
        user.value = null
        userRole.value = 'guest'
        isInitialized.value = false
        Cookies.remove('access_token')
        window.location.href = '/login'
      }
    }

    const initialize = async () => {
      if (token.value && !isInitialized.value) {
        await fetchUser()
      }
    }

    if (token.value && !isInitialized.value) {
      initialize()
    }

    return {
      user,
      token,
      userRole,
      isAuthenticated,
      loadingUser,
      isInitialized,
      login,
      fetchUser,
      logout,
      initialize,
    }
  },
  {
    persist: { key: 'auth', storage: localStorage },
  },
)
