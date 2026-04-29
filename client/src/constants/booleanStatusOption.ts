import { useLanguageStore } from '@/store/languageStore';
import { storeToRefs } from 'pinia';
import { computed } from 'vue';

const { translations } = storeToRefs(useLanguageStore()) as any;
export const BOOLEAN_STATUS_OPTION = computed(() => [
    { id: '0', name: translations.value.false },
    { id: '1', name: translations.value.true }
]);
