import apiClient from '@/services/api';
import type { AxiosResponse } from 'axios';

export interface TiposUso {
    id: number;
    nombre: string;
}

interface SingleResponseWrapper {
    success: boolean;
    data: TiposUso;
}

interface AllResponseWrapper {
    success: boolean;
    data: TiposUso[];
}

interface ResponseDelete {
    success: boolean;
    message: string;
}

interface SaveResponseWrapper {
    success: boolean;
    message: string;
    data: TiposUso;
}

export const TiposUsoService = {
    /**
     * Hace la llamada GET /api/admin/usages
     */
    async getTiposUsos(): Promise<TiposUso[]> {
        const response: AxiosResponse<AllResponseWrapper> = await apiClient.get('admin/usages');
        return response.data.data;
    },

    /**
     * Hace la llamada DELETE /api/admin/usages/{id}
     */
    async deleteTipoUso(id: number): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.delete(`admin/usages/${id}`);
        return response.data;
    },

    /**
     * Realiza una petición DELETE por cada ID.
     */
    async deleteMultipleTiposUsos(ids: number[]): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.post('admin/usages/delete-multiple', { ids });
        return response.data;
    },

    /**
     * Función para guardar (Crear/Editar) usando FormData.
     *
     * @param formData Contiene los datos de la Etiqueta
     * @param id ID de la Etiqueta para edición (opcional).
     * @returns La Etiqueta creada/actualizada.
     */
    async saveTipoUso(formData: FormData, id?: number): Promise<SaveResponseWrapper> {
        let response: AxiosResponse<SaveResponseWrapper>;

        if (id) {
            if (!formData.has('_method')) {
                formData.append('_method', 'PUT');
            }
            
            response = await apiClient.post(`admin/usages/${id}`, formData);
        } else {
            response = await apiClient.post('admin/usages', formData);
        }

        return response.data;
    },

    /**
     * Obtiene una Etiqueta específica.
     */
    async getTipoUso(id: number): Promise<TiposUso> {
        const response: AxiosResponse<SingleResponseWrapper> = await apiClient.get(`admin/usages/${id}`);
        return response.data.data;
    },
};