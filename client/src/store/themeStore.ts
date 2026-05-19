import { defineStore } from 'pinia'
import { ref, watch } from 'vue'

export type Theme = 'light' | 'dark'

export const useThemeStore = defineStore(
  'theme',
  () => {
    const theme = ref<Theme>('dark')

    // Initialize theme from localStorage or system preference
    function initializeTheme() {
      const savedTheme = localStorage.getItem('theme') as Theme | null
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches

      if (savedTheme) {
        theme.value = savedTheme
      } else if (prefersDark) {
        theme.value = 'dark'
      } else {
        theme.value = 'light'
      }

      applyTheme(theme.value)
    }

    // Apply theme to document
    function applyTheme(newTheme: Theme) {
      if (newTheme === 'dark') {
        document.documentElement.classList.add('dark')
      } else {
        document.documentElement.classList.remove('dark')
      }
    }

    // Toggle between light and dark
    function toggleTheme() {
      theme.value = theme.value === 'dark' ? 'light' : 'dark'
    }

    // Set specific theme
    function setTheme(newTheme: Theme) {
      theme.value = newTheme
    }

    // Watch for theme changes and apply them
    watch(theme, (newTheme) => {
      applyTheme(newTheme)
      localStorage.setItem('theme', newTheme)
    })

    // Initialize on store creation
    initializeTheme()

    return {
      theme,
      toggleTheme,
      setTheme,
      initializeTheme,
    }
  },
)
