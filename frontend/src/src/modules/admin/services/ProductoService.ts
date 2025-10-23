import apiClient from '@/services/api';
import type { AxiosResponse } from 'axios';

export interface Producto {
    id: number;
    nombre: string;
    marca: Marca;
    tipo: string;
    genero: string;
    cierre: string;
    stock: number;
}

interface Marca {
    nombre: string;
    logo: string;
}

interface SingleResponseWrapper {
    success: boolean;
    data: Producto;
}

interface AllResponseWrapper {
    success: boolean;
    data: Producto[];
}

interface ResponseDelete {
    success: boolean;
    message: string;
}

interface SaveResponseWrapper {
    success: boolean;
    message: string;
    data: Producto;
}

export const ProductoService = {
    /**
     * Hace la llamada GET /api/admin/products
     */
    async getProductos(): Promise<Producto[]> {
        const response: AxiosResponse<AllResponseWrapper> = await apiClient.get('admin/products');
        return response.data.data;
    },

    /**
     * Hace la llamada DELETE /api/admin/etiquetas/{id}
     */
    async deleteProducto(id: number): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.delete(`admin/products/${id}`);
        return response.data;
    },

    /**
     * Realiza una petición DELETE por cada ID.
     */
    async deleteMultipleProductos(ids: number[]): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.post('admin/products/delete-multiple', { ids });
        return response.data;
    },
};
