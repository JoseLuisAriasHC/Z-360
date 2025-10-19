import apiClient from '@/services/api';
import { ROLES, type UserRole } from '@/constants/roles';


export interface LoginCredentials {
    email: string;
    password: string;
}

export interface UserData {
    id: number;
    nombre: string;
    apellido: string;
    email: string;
    rol: UserRole; 
}

export interface LoginResponse {
    success: boolean;
    message: string;
    data: UserData;
    token: string;
}

export interface ApiError {
    error?: string;
    message?: string;
    errors?: Record<string, string[]>;
}

export class RoleAccessError extends Error {
    constructor(message: string) {
        super(message);
        this.name = 'RoleAccessError';
    }
}

class AuthService {
    
    async login(credentials: LoginCredentials): Promise<LoginResponse> {
        const response = await apiClient.post<LoginResponse>('/login', credentials);
        
        if (response.data.success && response.data.token) {
            const userData = response.data.data;
            
            if (userData.rol !== ROLES.ADMIN) {
                throw new RoleAccessError('Acceso denegado. Solo los administradores pueden iniciar sesión en este panel.');
            }

            localStorage.setItem('auth_token', response.data.token);
            localStorage.setItem('user_data', JSON.stringify(userData));
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
    
    getUser(): UserData | null { 
        const userData = localStorage.getItem('user_data');
        return userData ? JSON.parse(userData) : null;
    }

    isAdmin(): boolean {
        const user = this.getUser();
        return user?.rol === ROLES.ADMIN;
    }
}

export default new AuthService();