import apiClient from '@/services/api';
import type { AxiosResponse } from 'axios';
import type { Color } from './ColorService';

export interface VarianteProductoVM {
    id: number;
    imagen_principal: string;
    color: Color;
    precio: number;
    stock: number;
    descuento: number;
    descuento_activo: boolean;
}

interface SingleResponseWrapper {
    success: boolean;
    data: VarianteProductoVM;
}

interface AllResponseWrapper {
    success: boolean;
    data: VarianteProductoVM[];
}

interface ResponseDelete {
    success: boolean;
    message: string;
}

interface SaveResponseWrapper {
    success: boolean;
    message: string;
    data: VarianteProductoVM;
}

export const VarianteProductoService = {
    /**
     * Hace la llamada GET /api/admin/tallas
     */
    async getVariantes(idProducto: number): Promise<VarianteProductoVM[]> {
        const response: AxiosResponse<AllResponseWrapper> = await apiClient.get(`admin/products/${idProducto}/variants`);
        return response.data.data;
    },

    async generarVaraintes(formData: FormData): Promise<SaveResponseWrapper> {
        let response: AxiosResponse<SaveResponseWrapper>;
        response = await apiClient.post('admin/product-variants/generate', formData);

        return response.data;
    },

    /**
     * Hace la llamada DELETE /api/admin/tallas/{id}
     */
    async deleteVariante(idVariante: number): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.delete(`admin/product-variants/${idVariante}`);
        return response.data;
    },

    /**
     * Realiza una petición DELETE por cada ID.
     */
    async deleteMultipleVariantes(ids: number[]): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.post('admin/product-variants/delete-multiple', { ids });
        return response.data;
    },
};
