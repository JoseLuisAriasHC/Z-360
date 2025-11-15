import apiClient from '@/services/api';
import type { AxiosResponse } from 'axios';

export interface WebSetting {
    clave: string;
    valor: string;
    nombre: string;
    descripcion: string;
}

interface SingleResponseWrapper {
    success: boolean;
    data: WebSetting;
}

interface AllResponseWrapper {
    success: boolean;
    data: WebSetting[];
}

interface SaveResponseWrapper {
    success: boolean;
    message: string;
    data: WebSetting;
}

export const WebSettingService = {
    async getSettings(): Promise<WebSetting[]> {
        const response: AxiosResponse<AllResponseWrapper> = await apiClient.get('admin/web-settings');
        return response.data.data;
    },

    async getEmpresaSettings(): Promise<WebSetting[]> {
        const response: AxiosResponse<AllResponseWrapper> = await apiClient.get('web-settings/empresa');
        return response.data.data;
    },

    async getEnvioSettings(): Promise<WebSetting[]> {
        const response: AxiosResponse<AllResponseWrapper> = await apiClient.get('web-settings/envio');
        return response.data.data;
    },

    async getEcommerceSettings(): Promise<WebSetting[]> {
        const response: AxiosResponse<AllResponseWrapper> = await apiClient.get('admin/web-settings/ecommerce');
        return response.data.data;
    },

    /**
     * Función para guardar (Crear/Editar) usando FormData.
     *
     * @param formData Contiene los datos de la WebSetting
     * @param id ID de la WebSetting para edición (opcional).
     * @returns La WebSetting creada/actualizada.
     */
    async saveEmpresaSetting(formData: FormData): Promise<SaveResponseWrapper> {
        let response: AxiosResponse<SaveResponseWrapper>;
        if (!formData.has('_method')) {
            formData.append('_method', 'PATCH');
        }

        response = await apiClient.post(`admin/web-settings/save-empresa`, formData);
        return response.data;
    },

    async saveEnvioSetting(formData: FormData): Promise<SaveResponseWrapper> {
        let response: AxiosResponse<SaveResponseWrapper>;
        if (!formData.has('_method')) {
            formData.append('_method', 'PATCH');
        }

        response = await apiClient.post(`admin/web-settings/save-envio`, formData);
        return response.data;
    },

    async saveEcommerceSetting(formData: FormData): Promise<SaveResponseWrapper> {
        let response: AxiosResponse<SaveResponseWrapper>;
        if (!formData.has('_method')) {
            formData.append('_method', 'PATCH');
        }

        response = await apiClient.post(`admin/web-settings/save-ecommerce`, formData);
        return response.data;
    },
};
