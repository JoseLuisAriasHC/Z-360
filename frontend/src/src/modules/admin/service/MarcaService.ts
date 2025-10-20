import apiClient from '@/services/api';
import type { AxiosResponse } from 'axios';

export interface Marca {
    id: number;
    nombre: string;
    logo: string;
    talla_offset: number;
    created_at?: string;
    updated_at?: string;
}

interface SingleResponseWrapper {
    success: boolean;
    data: Marca;
}

interface AllResponseWrapper {
    success: boolean;
    data: Marca[];
}

interface ResponseDelete {
    success: boolean;
    message: string;
}

export const MarcaService = {
    /**
     * Hace la llamada GET /api/admin/marcas
     * Extrae el array de marcas de response.data.data.data.
     */
    async getMarcas(): Promise<Marca[]> {
        const response: AxiosResponse<AllResponseWrapper> = await apiClient.get('admin/marcas');
        return response.data.data;
    },

    /**
     * Hace la llamada DELETE /api/admin/marcas/{id}
     */
    async deleteMarca(id: number): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.delete(`admin/marcas/${id}`);
        return response.data;
    },

    /**
     * Realiza una petición DELETE por cada ID.
     */
    async deleteMultipleMarcas(ids: number[]): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.post('admin/marcas/delete-multiple', { ids });
        return response.data;
    },

    /**
     * Función para guardar (Crear/Editar).
     * Retorna la marca creada/actualizada desde response.data.data.
     */
    async saveMarca(marca: Omit<Marca, 'id'> & { id?: number }): Promise<Marca> {
        if (marca.id) {
            const response: AxiosResponse<SingleResponseWrapper> = await apiClient.put(`admin/marcas/${marca.id}`, marca);
            return response.data.data;
        } else {
            const response: AxiosResponse<SingleResponseWrapper> = await apiClient.post('admin/marcas', marca);
            return response.data.data;
        }
    },

    /**
     * Obtiene una marca específica.
     * Retorna la marca desde response.data.data.
     */
    async getMarca(id: number): Promise<Marca> {
        const response: AxiosResponse<SingleResponseWrapper> = await apiClient.get(`admin/marcas/${id}`);
        return response.data.data;
    },
};
