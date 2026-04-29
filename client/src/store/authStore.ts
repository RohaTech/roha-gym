import { defineStore } from 'pinia';
import { ref } from 'vue';
import Cookies from 'js-cookie';
import axiosInstance from '@/api/axiosInstance';

export interface User {
    id: number;
    name: string;
    email: string;
}

export const useAuthStore = defineStore(
    'auth',
    () => {
        const user = ref<User | null>(null);
        const token = ref<string | null>(Cookies.get('access_token') ?? null);
        const loadingUser = ref(false);
        const isInitialized = ref(false);

        const login = async (email: string, password: string) => {
            const response = await axiosInstance.post('/auth/login', { email, password });
            const { token: newToken, user: newUser } = response.data;
            token.value = newToken;
            user.value = newUser;
            Cookies.set('access_token', newToken);
            isInitialized.value = true;
        };

        const fetchUser = async () => {
            if (!token.value) return;
            loadingUser.value = true;
            try {
                const response = await axiosInstance.get('/auth/me');
                user.value = response.data;
                isInitialized.value = true;
            } catch {
                logout();
            } finally {
                loadingUser.value = false;
            }
        };

        const logout = async () => {
            try {
                if (token.value) await axiosInstance.post('/auth/logout');
            } catch { /* ignore */ } finally {
                token.value = null;
                user.value = null;
                isInitialized.value = false;
                Cookies.remove('access_token');
                window.location.href = '/login';
            }
        };

        const initialize = async () => {
            if (token.value && !isInitialized.value) {
                await fetchUser();
            }
        };

        if (token.value && !isInitialized.value) {
            initialize();
        }

        return { user, token, loadingUser, isInitialized, login, fetchUser, logout, initialize };
    },
    {
        persist: { key: 'auth', storage: localStorage },
    }
);
