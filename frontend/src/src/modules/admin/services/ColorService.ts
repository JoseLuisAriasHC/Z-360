import apiClient from '@/services/api';
import type { AxiosResponse } from 'axios';

export interface Color {
    id: number;
    nombre: string;
    codigo_hex: string;
}

interface SingleResponseWrapper {
    success: boolean;
    data: Color;
}

interface AllResponseWrapper {
    success: boolean;
    data: Color[];
}

interface ResponseDelete {
    success: boolean;
    message: string;
}

interface SaveResponseWrapper {
    success: boolean;
    message: string;
    data: Color;
}

export const ColorService = {
    /**
     * Hace la llamada GET /api/admin/colores
     */
    async getColores(): Promise<Color[]> {
        const response: AxiosResponse<AllResponseWrapper> = await apiClient.get('admin/colores');
        return response.data.data;
    },

    /**
     * Hace la llamada DELETE /api/admin/colores/{id}
     */
    async deleteColor(id: number): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.delete(`admin/colores/${id}`);
        return response.data;
    },

    /**
     * Realiza una petición DELETE por cada ID.
     */
    async deleteMultipleColores(ids: number[]): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.post('admin/colores/delete-multiple', { ids });
        return response.data;
    },

    /**
     * Función para guardar (Crear/Editar) usando FormData.
     *
     * @param formData Contiene los datos de la Color
     * @param id ID de la Color para edición (opcional).
     * @returns La Color creada/actualizada.
     */
    async saveColor(formData: FormData, id?: number): Promise<SaveResponseWrapper> {
        let response: AxiosResponse<SaveResponseWrapper>;

        if (id) {
            if (!formData.has('_method')) {
                formData.append('_method', 'PUT');
            }
            
            response = await apiClient.post(`admin/colores/${id}`, formData);
        } else {
            response = await apiClient.post('admin/colores', formData);
        }

        return response.data;
    },

    /**
     * Obtiene una Color específica.
     */
    async getColor(id: number): Promise<Color> {
        const response: AxiosResponse<SingleResponseWrapper> = await apiClient.get(`admin/colores/${id}`);
        return response.data.data;
    },
};