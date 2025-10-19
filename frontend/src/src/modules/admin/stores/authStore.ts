import { defineStore } from 'pinia';
import { ref } from 'vue';
import authService, { RoleAccessError, type LoginCredentials } from '@/services/authService';

export const useAuthStore = defineStore('auth', () => {
    const user = ref<any>(null);
    const isAuthenticated = ref(false);
    const loading = ref(false);
    const error = ref<string | null>(null);

    // Inicializar el estado desde localStorage
    const init = () => {
        const storedUser = authService.getUser();
        if (storedUser && authService.isAuthenticated()) {
            user.value = storedUser;
            isAuthenticated.value = true;
        }
    };

    const login = async (credentials: LoginCredentials) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await authService.login(credentials);

            user.value = response.data;
            isAuthenticated.value = true;
            return response;
        } catch (err: any) {
            let errorMessage: string | null = null;

            if (err instanceof RoleAccessError) {
                errorMessage = err.message;
            }
            else if (err.response?.data?.error) {
                errorMessage = err.response.data.error;
            } else if (err.response?.data?.message) {
                errorMessage = err.response.data.message;
            } else {
                errorMessage = 'Error al iniciar sesión. Verifica tu conexión.';
            }

            error.value = errorMessage;
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const logout = async () => {
        try {
            await authService.logout();
        } finally {
            user.value = null;
            isAuthenticated.value = false;
            error.value = null;
        }
    };

    const clearError = () => {
        error.value = null;
    };

    // Inicializar al crear el store
    init();

    return {
        user,
        isAuthenticated,
        loading,
        error,
        login,
        logout,
        clearError,
    };
});
