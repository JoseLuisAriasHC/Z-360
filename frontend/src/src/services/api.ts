import axios, { type AxiosInstance, type InternalAxiosRequestConfig } from 'axios';
import router from '@/router';

const apiClient: AxiosInstance = axios.create({
    baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
    headers: {
        'Accept': 'application/json',
    },
    withCredentials: false,
});

// Interceptor para agregar el token a cada petición
apiClient.interceptors.request.use(
    (config: InternalAxiosRequestConfig) => {
        const token = localStorage.getItem('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }

        // El interceptor elimina la cabecera Content-Type para que el navegador la genere automáticamente, incluyendo el boundary necesario para los archivos.
        if (config.data instanceof FormData) {
            delete config.headers['Content-Type'];
        } else {
            config.headers['Content-Type'] = 'application/json';
        }

        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Interceptor para manejar respuestas y errores
apiClient.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        if (error.response?.status === 401) {
            // Token inválido o expirado
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_data');
            router.push('/admin/login');
        }
        return Promise.reject(error);
    }
);

export default apiClient;