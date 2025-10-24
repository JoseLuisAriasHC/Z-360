import apiClient from '@/services/api';
import type { AxiosResponse } from 'axios';

export interface Etiqueta {
    id: number;
    nombre: string;
}

interface SingleResponseWrapper {
    success: boolean;
    data: Etiqueta;
}

interface AllResponseWrapper {
    success: boolean;
    data: Etiqueta[];
}

interface ResponseDelete {
    success: boolean;
    message: string;
}

interface SaveResponseWrapper {
    success: boolean;
    message: string;
    data: Etiqueta;
}

interface SaveAllResponseWrapper {
    success: boolean;
    message: string;
    data: Etiqueta[];
}

type EtiquetasPayload = {
    etiquetas: number[];
};

export const EtiquetaService = {
    /**
     * Hace la llamada GET /api/admin/etiquetas
     */
    async getEtiquetas(): Promise<Etiqueta[]> {
        const response: AxiosResponse<AllResponseWrapper> = await apiClient.get('admin/etiquetas');
        return response.data.data;
    },

    /**
     * Obtiene una Etiqueta específica.
     */
    async getEtiqueta(id: number): Promise<Etiqueta> {
        const response: AxiosResponse<SingleResponseWrapper> = await apiClient.get(`admin/etiquetas/${id}`);
        return response.data.data;
    },

    /**
     * Hace la llamada DELETE /api/admin/etiquetas/{id}
     */
    async deleteEtiqueta(id: number): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.delete(`admin/etiquetas/${id}`);
        return response.data;
    },

    /**
     * Realiza una petición DELETE por cada ID.
     */
    async deleteMultipleEtiquetas(ids: number[]): Promise<ResponseDelete> {
        const response: AxiosResponse<ResponseDelete> = await apiClient.post('admin/etiquetas/delete-multiple', { ids });
        return response.data;
    },

    /**
     * Función para guardar (Crear/Editar) usando FormData.
     *
     * @param formData Contiene los datos de la Etiqueta
     * @param id ID de la Etiqueta para edición (opcional).
     * @returns La Etiqueta creada/actualizada.
     */
    async saveEtiqueta(formData: FormData, id?: number): Promise<SaveResponseWrapper> {
        let response: AxiosResponse<SaveResponseWrapper>;

        if (id) {
            if (!formData.has('_method')) {
                formData.append('_method', 'PUT');
            }

            response = await apiClient.post(`admin/etiquetas/${id}`, formData);
        } else {
            response = await apiClient.post('admin/etiquetas', formData);
        }

        return response.data;
    },

    /**
     * Obtiene las etiquetas específicas en base la producto
     * products/{product}/etiquetas
     */
    async getEtiquetasByIdProduct(idProducto: number): Promise<Etiqueta[]> {
        const response: AxiosResponse<AllResponseWrapper> = await apiClient.get(`admin/products/${idProducto}/etiquetas`);
        return response.data.data;
    },

    /**
     * @param idProducto idProducto del producto
     * @returns Las etiquetas del producto
     */
    async saveEtiquetasDelProducto(payload: EtiquetasPayload, idProducto: number): Promise<SaveAllResponseWrapper> {
        let response: AxiosResponse<SaveAllResponseWrapper>;
        response = await apiClient.post(`admin/products/${idProducto}/etiquetas`, payload);
        return response.data;
    },
};
