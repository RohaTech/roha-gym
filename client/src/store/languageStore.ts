import { useQuery } from '@tanstack/vue-query';
import Cookies from 'js-cookie';
import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import axiosInstance from '@/api/axiosInstance';
import { toast } from 'vue-sonner';
import { CONSTANTS_STALE_TIME } from '@/config/appConfig';

export interface LanguageOption {
  code: string;
  name: string;
  label: string;
}

interface LanguageResponse {
  available_languages: LanguageOption[];
  translations: Record<string, string>;
  current_language: string;
}

const LANGUAGE_QUERY_KEYS = {
  all: ['languages'] as const,
  front: (lang?: string) => [...LANGUAGE_QUERY_KEYS.all, 'front', lang || 'default'] as const,
};

export const useLanguageStore = defineStore('lang', () => {
  const isInitialized = ref(true);
  const currentLanguage = ref<string>('en');

  // Fetch front languages using TanStack Query
  const fetchFrontLanguages = useQuery({
    queryKey: computed(() => LANGUAGE_QUERY_KEYS.front(currentLanguage.value)),
    queryFn: async () => {
      try {
        const response = await axiosInstance.get<LanguageResponse>('front-language', {
          headers: {
            'Accept-Language': currentLanguage.value,
          },
        });
        const data = response.data;

        // Set current language based on cookie or response
        const cookieLang = Cookies.get('lang');
        if (cookieLang && data.available_languages?.some((l) => l.code === cookieLang)) {
          currentLanguage.value = cookieLang;
        } else if (
          data.current_language &&
          data.available_languages?.some((l) => l.code === data.current_language)
        ) {
          currentLanguage.value = data.current_language;
        } else if (data.available_languages && data.available_languages.length > 0) {
          currentLanguage.value = data.available_languages[0]?.code || 'en';
        }

        return data;
      } catch (error: any) {
        const errorMessage = error?.response?.data?.message || 'Failed to fetch languages';
        toast.error(errorMessage);
        throw error;
      }
    },
    staleTime: CONSTANTS_STALE_TIME, // 1 hour
    retry: 2,
  });

  // Computed values from query
  const availableLanguages = computed(
    () => fetchFrontLanguages.data.value?.available_languages || []
  );

  const translations = computed(() => fetchFrontLanguages.data.value?.translations || {});

  const isLoading = computed(() => fetchFrontLanguages.isLoading.value);

  const getCurrentLang = computed(() => {
    return (
      availableLanguages.value.find((l) => l.code === currentLanguage.value) ||
      availableLanguages.value[0] || { code: '', name: '', label: '' }
    );
  });

  // Set language function
  async function setLanguage(newLang: string) {
    if (availableLanguages.value.some((l) => l.code === newLang)) {
      currentLanguage.value = newLang;
      Cookies.set('lang', newLang, { expires: 7 });
      // Refetch to get new translations
      await fetchFrontLanguages.refetch();
    }
  }

  function setIsInitialized(value: boolean) {
    isInitialized.value = value;
  }

  return {
    // Query
    fetchFrontLanguages,

    // State
    availableLanguages,
    currentLanguage,
    getCurrentLang,
    translations,
    isLoading,
    isInitialized,

    // Methods
    setLanguage,
    setIsInitialized,
  };
});
