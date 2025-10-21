import apiClient from '@/services/api';
import type { AxiosResponse } from 'axios';

export interface Talla {
    id: number;
    numero: number;
}

interface SingleResponseWrapper {
    success: boolean;
    data: Talla;
}

interface AllResponseWrapper {
    success: boolean;
    data: Talla[];
}

interface ResponseDelete {
    success: boolean;
    message: string;
}

interface SaveResponseWrapper {
    success: boolean;
    message: string;
    data: Talla;
}

export const TallaService = {
    /**
     * Hace la llamada GET /api/admin/tallas
     */
    async getTallas(): Promise<Talla[]> {
        const response: AxiosResponse<AllResponseWrapper> = await apiClient.get('admin/tallas');
        return response.data.data;
    },

    /**
     * Hace la llamada DELETE /api/admin/tallas/{id}
     */
    async deleteTalla(id: number): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.delete(`admin/tallas/${id}`);
        return response.data;
    },

    /**
     * Realiza una petición DELETE por cada ID.
     */
    async deleteMultipleTallas(ids: number[]): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.post('admin/tallas/delete-multiple', { ids });
        return response.data;
    },

    /**
     * Función para guardar (Crear/Editar) usando FormData.
     *
     * @param formData Contiene los datos de la Talla
     * @param id ID de la Talla para edición (opcional).
     * @returns La Talla creada/actualizada.
     */
    async saveTalla(formData: FormData, id?: number): Promise<SaveResponseWrapper> {
        let response: AxiosResponse<SaveResponseWrapper>;

        if (id) {
            if (!formData.has('_method')) {
                formData.append('_method', 'PUT');
            }
            
            response = await apiClient.post(`admin/tallas/${id}`, formData);
        } else {
            response = await apiClient.post('admin/tallas', formData);
        }

        return response.data;
    },

    /**
     * Obtiene una Talla específica.
     */
    async getTalla(id: number): Promise<Talla> {
        const response: AxiosResponse<SingleResponseWrapper> = await apiClient.get(`admin/tallas/${id}`);
        return response.data.data;
    },
};