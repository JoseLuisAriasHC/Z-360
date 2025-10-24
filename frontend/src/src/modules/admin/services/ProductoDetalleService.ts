import apiClient from '@/services/api';
import type { AxiosResponse } from 'axios';

export interface ProductoDetalle {
    product_id: number;
    parte_superior: string;
    parte_inferior: string;
    suela: string;
    cuidados: string;
}

interface SingleResponseWrapper {
    success: boolean;
    data: ProductoDetalle;
}

interface SaveResponseWrapper {
    success: boolean;
    message: string;
    data: ProductoDetalle;
}

export const ProductoDetalleService = {
    /**
     * Hace la llamada GET api/admin/products/{product}/details
     * 
     */
    async getDetalles(idProdcuto: number): Promise<ProductoDetalle> {
        const response: AxiosResponse<SingleResponseWrapper> = await apiClient.get(`admin/products/${idProdcuto}/details`);
        return response.data.data;
    },

    /**
     * Hace la llamada GET api/admin/products/{product}/details
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

};