import type { AlturaSuela, Cierre, Genero, Tipo } from '@/constants/productos';
import apiClient from '@/services/api';
import type { AxiosResponse } from 'axios';

export interface ProductoVM {
    id: number;
    nombre: string;
    marca: Marca;
    tipo: string;
    genero: string;
    cierre: string;
    stock: number;
}

export interface Producto {
    id: number;
    nombre: string;
    marca_id: number;
    tipo: Tipo;
    descripcion: string;
    cierre: Cierre;
    altura_suela: AlturaSuela;
    plantilla: string;
    genero: Genero;
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
    data: ProductoVM[];
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
    async getProductos(): Promise<ProductoVM[]> {
        const response: AxiosResponse<AllResponseWrapper> = await apiClient.get('admin/products');
        return response.data.data;
    },

    /**
     * Obtiene una Talla específica.
     */
    async getProducto(id: number): Promise<Producto> {
        const response: AxiosResponse<SingleResponseWrapper> = await apiClient.get(`admin/products/${id}`);
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

    /**
     * Función para guardar (Crear/Editar) usando FormData.
     *
     * @param formData Contiene los datos de la Talla
     * @param id ID de la Talla para edición (opcional).
     * @returns La Talla creada/actualizada.
     */
    async saveProducto(formData: FormData, id?: number): Promise<SaveResponseWrapper> {
        let response: AxiosResponse<SaveResponseWrapper>;

        if (id) {
            if (!formData.has('_method')) {
                formData.append('_method', 'PUT');
            }

            response = await apiClient.post(`admin/products/${id}`, formData);
        } else {
            response = await apiClient.post('admin/products', formData);
        }

        return response.data;
    },
};
