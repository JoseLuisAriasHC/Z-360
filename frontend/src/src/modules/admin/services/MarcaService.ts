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

interface SaveResponseWrapper {
    success: boolean;
    message: string;
    data: Marca;
}

export const MarcaService = {
    /**
     * Hace la llamada GET /api/admin/marcas
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
     * Función para guardar (Crear/Editar) usando FormData.
     * CRÍTICO: No establecer manualmente Content-Type, dejar que el navegador lo haga.
     *
     * @param formData Contiene los datos de la marca, incluyendo el archivo 'logo'.
     * @param id ID de la marca para edición (opcional).
     * @returns La marca creada/actualizada.
     */
    async saveMarca(formData: FormData, id?: number): Promise<SaveResponseWrapper> {
        let response: AxiosResponse<SaveResponseWrapper>;

        if (id) {
            if (!formData.has('_method')) {
                formData.append('_method', 'PUT');
            }
            
            response = await apiClient.post(`admin/marcas/${id}`, formData);
        } else {
            response = await apiClient.post('admin/marcas', formData);
        }

        return response.data;
    },

    /**
     * Obtiene una marca específica.
     */
    async getMarca(id: number): Promise<Marca> {
        const response: AxiosResponse<SingleResponseWrapper> = await apiClient.get(`admin/marcas/${id}`);
        return response.data.data;
    },
};