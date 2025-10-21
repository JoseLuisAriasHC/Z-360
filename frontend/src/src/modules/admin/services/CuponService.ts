import apiClient from '@/services/api';
import type { AxiosResponse } from 'axios';

export interface Cupon {
    id: number;
    codigo: string;
    descuento: number;
    tipo: 'porcentaje' | 'fijo';
    fecha_expiracion: Date;
    uso_maximo: number;
}

interface SingleResponseWrapper {
    success: boolean;
    data: Cupon;
}

interface AllResponseWrapper {
    success: boolean;
    data: Cupon[];
}

interface ResponseDelete {
    success: boolean;
    message: string;
}

interface SaveResponseWrapper {
    success: boolean;
    message: string;
    data: Cupon;
}

export const CuponService = {
    /**
     * Hace la llamada GET /api/admin/cupones
     */
    async getCupones(): Promise<Cupon[]> {
        const response: AxiosResponse<AllResponseWrapper> = await apiClient.get('admin/cupones');
        return response.data.data;
    },

    /**
     * Hace la llamada DELETE /api/admin/cupones/{id}
     */
    async deleteCupon(id: number): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.delete(`admin/cupones/${id}`);
        return response.data;
    },

    /**
     * Realiza una petición DELETE por cada ID.
     */
    async deleteMultipleCupones(ids: number[]): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.post('admin/cupones/delete-multiple', { ids });
        return response.data;
    },

    /**
     * Función para guardar (Crear/Editar) usando FormData.
     *
     * @param formData Contiene los datos de la Cupon
     * @param id ID de la Cupon para edición (opcional).
     * @returns La Cupon creada/actualizada.
     */
    async saveCupon(formData: FormData, id?: number): Promise<SaveResponseWrapper> {
        let response: AxiosResponse<SaveResponseWrapper>;

        if (id) {
            if (!formData.has('_method')) {
                formData.append('_method', 'PUT');
            }

            response = await apiClient.post(`admin/cupones/${id}`, formData);
        } else {
            response = await apiClient.post('admin/cupones', formData);
        }

        return response.data;
    },

    /**
     * Obtiene una Cupon específica.
     */
    async getCupon(id: number): Promise<Cupon> {
        const response: AxiosResponse<SingleResponseWrapper> = await apiClient.get(`admin/cupones/${id}`);
        return response.data.data;
    },  
};
