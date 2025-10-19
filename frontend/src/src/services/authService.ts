import apiClient from './api';

export interface LoginCredentials {
    email: string;
    password: string;
}

export interface LoginResponse {
    success: boolean;
    message: string;
    data: {
        id: number;
        nombre: string;
        apellido: string;
        email: string;
        rol: boolean;
    };
    token: string;
}

export interface ApiError {
    error?: string;
    message?: string;
    errors?: Record<string, string[]>;
}

class AuthService {
    async login(credentials: LoginCredentials): Promise<LoginResponse> {
        const response = await apiClient.post<LoginResponse>('/login', credentials);
        
        if (response.data.success && response.data.token) {
            localStorage.setItem('auth_token', response.data.token);
            localStorage.setItem('user_data', JSON.stringify(response.data.data));
        }
        
        return response.data;
    }

    async logout(): Promise<void> {
        try {
            await apiClient.post('/logout');
        } catch (error) {
            console.error('Error durante el logout:', error);
        } finally {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_data');
        }
    }

    isAuthenticated(): boolean {
        return !!localStorage.getItem('auth_token');
    }

    getUser() {
        const userData = localStorage.getItem('user_data');
        return userData ? JSON.parse(userData) : null;
    }

    isAdmin(): boolean {
        const user = this.getUser();
        return user?.rol === true || user?.rol === 1;
    }
}

export default new AuthService();