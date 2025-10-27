import apiClient from '@/services/api';
import type { AxiosResponse } from 'axios';
import type { Color } from './ColorService';
import type { Talla } from './TallaService';

export interface Size {
    id: number;
    product_variant_id: number;
    talla_id: number;
    stock: number;
    sku: number;
    talla: Talla;
}

export interface Image {
    id: number;
    product_variant_id: number;
    path: string;
}

export interface VarianteProductoVM {
    id: number;
    imagen_principal: string;
    color: Color;
    precio: number;
    stock: number;
    descuento: number;
    descuento_activo: boolean;
}

export interface VarianteProducto {
    id: number;
    product_id: number;
    color_id: number;
    precio: number;
    precio_sin_iva: number;
    iva: number;
    imagen_principal: string | null;
    descuento: number;
    descuento_desde: Date;
    descuento_hasta: Date;
}

export interface VarianteImage {
    id: number;
    product_variant_id: number;
    path: string;
}

export interface VarianteTalla {
    id: number;
    product_variant_id: number;
    talla_id: number;
    talla: Talla;
    stock: number;
    sku: string;
}

interface SingleResponseWrapper {
    success: boolean;
    data: VarianteProducto;
}

interface AllResponseImageWrapper {
    success: boolean;
    data: VarianteImage[];
}

interface AllResponseTallaWrapper {
    success: boolean;
    data: VarianteTalla[];
}

interface SaveResponsTallaeWrapper {
    success: boolean;
    message: string;
    data: VarianteTalla;
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
    async getVariantes(idProducto: number): Promise<VarianteProductoVM[]> {
        const response: AxiosResponse<AllResponseWrapper> = await apiClient.get(`admin/products/${idProducto}/variants`);
        return response.data.data;
    },

    async getVariante(idVariante: number): Promise<VarianteProducto> {
        const response: AxiosResponse<SingleResponseWrapper> = await apiClient.get(`admin/product-variants/${idVariante}`);
        return response.data.data;
    },

    async generarVaraintes(formData: FormData): Promise<SaveResponseWrapper> {
        let response: AxiosResponse<SaveResponseWrapper>;
        response = await apiClient.post('admin/product-variants/generate', formData);

        return response.data;
    },

    async saveVariante(formData: FormData, id?: number): Promise<SaveResponseWrapper> {
        let response: AxiosResponse<SaveResponseWrapper>;

        if (id) {
            if (!formData.has('_method')) {
                formData.append('_method', 'PUT');
            }
            response = await apiClient.post(`admin/product-variants/${id}`, formData);
        } else {
            response = await apiClient.post('admin/product-variants', formData);
        }

        return response.data;
    },

    async deleteVariante(idVariante: number): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.delete(`admin/product-variants/${idVariante}`);
        return response.data;
    },

    async deleteMultipleVariantes(ids: number[]): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.post('admin/product-variants/delete-multiple', { ids });
        return response.data;
    },

    async getImagesByIdVarainte(idVariante: number): Promise<VarianteImage[]> {
        const response: AxiosResponse<AllResponseImageWrapper> = await apiClient.get(`admin/product-variants/${idVariante}/images`);
        return response.data.data;
    },

    /**
     * Sube nuevas imágenes para una variante (usado en el FileUpload customizado).
     * El backend recibirá el FormData y devolverá las nuevas imágenes.
     * @param idVariante ID de la variante.
     * @param formData Contiene los archivos a subir.
     */
    async uploadVariantImages(idVariante: number, formData: FormData): Promise<AllResponseImageWrapper> {
        const response: AxiosResponse<AllResponseImageWrapper> = await apiClient.post(`admin/product-variants/${idVariante}/images`, formData);
        return response.data;
    },

    /**
     * Elimina una imagen existente de una variante.
     * @param idImage ID de la imagen a eliminar (registro en la tabla variant_images).
     */
    async deleteVariantImage(idImage: number): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.delete(`admin/variant-images/${idImage}`);
        return response.data;
    },

    async getVariantesTallas(idVariante: number): Promise<VarianteTalla[]> {
        const response: AxiosResponse<AllResponseTallaWrapper> = await apiClient.get(`admin/variant-sizes/${idVariante}/sizes`);
        return response.data.data;
    },

    async updateVarianteTalla(formData: FormData, idVarianteTalla: number): Promise<SaveResponsTallaeWrapper> {
        formData.append('_method', 'PUT');
        const response: AxiosResponse<SaveResponsTallaeWrapper> = await apiClient.post(`admin/variant-sizes/${idVarianteTalla}`, formData);

        return response.data;
    },

    async createVarianteTalla(formData: FormData, idVariante: number): Promise<SaveResponsTallaeWrapper> {
        let response: AxiosResponse<SaveResponsTallaeWrapper>;
        response = await apiClient.post(`admin/variant-sizes/${idVariante}/create`, formData);

        return response.data;
    },

    async deleteVariantTalla(idVarianteTalla: number): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.delete(`admin/variant-sizes/${idVarianteTalla}`);
        return response.data;
    },
};
