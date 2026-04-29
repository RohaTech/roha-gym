import Cookies from 'js-cookie';
import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import axiosInstance from '@/api/axiosInstance';
export interface LanguageOption {
    code: string;
    name: string;
    label: string;
}

export const useLanguageStore = defineStore('lang', () => {
    const isLoading = ref<boolean>(false);
    const availableLanguages = ref<LanguageOption[]>([]);
    const translations = ref<Record<string, any>>({});
    const isInitialized = ref<boolean>(true);
    const reFetching = ref<boolean>(false);
    const currentLanguage = ref<string>('en');

    async function fetchFrontLanguages(isNeedLoading = true) {
        if (isNeedLoading) {
            isLoading.value = true;
        }
        try {
            reFetching.value = true;
            const response = await axiosInstance.get('front-language');
            const data = response.data;
            availableLanguages.value = data.available_languages || [];
            translations.value = data.translations || {};

            const cookieLang = Cookies.get('lang');
            if (cookieLang && availableLanguages.value.some((l) => l.code === cookieLang)) {
                currentLanguage.value = cookieLang;
            } else if (
                data.current_language &&
                availableLanguages.value.some((l) => l.code === data.current_language)
            ) {
                currentLanguage.value = data.current_language;
            } else if (availableLanguages.value.length > 0) {
                currentLanguage.value = availableLanguages.value[0]?.code ?? 'en';
            }
        } catch (error) {
            console.log(error);
        } finally {
            isLoading.value = false;
            reFetching.value = false;
        }
    }

    async function setLanguage(newLang: string) {
        if (availableLanguages.value.some((l) => l.code === newLang)) {
            currentLanguage.value = newLang;
            Cookies.set('lang', newLang, { expires: 7 });
            await fetchFrontLanguages(false);
        }
    }

    function setIsInitialized(value: boolean) {
        isInitialized.value = value;
    }

    const getCurrentLang = computed(() => {
        return (
            availableLanguages.value.find((l) => l.code === currentLanguage.value) ||
            availableLanguages.value[0] ||
            ({ code: '', name: '', label: '' } as LanguageOption)
        );
    });

    return {
        availableLanguages,
        currentLanguage,
        getCurrentLang,
        translations,
        isLoading,
        reFetching,

        setLanguage,
        fetchFrontLanguages,
        setIsInitialized
    };
});
