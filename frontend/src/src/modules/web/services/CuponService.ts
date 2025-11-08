import apiClient from '@/services/api';
import type { AxiosResponse } from 'axios';

export interface Cupon {
    id: number;
    codigo: string;
    descuento: number;
    tipo: 'porcentaje' | 'fijo';
}

interface SingleResponseWrapper {
    success: boolean;
    message: string;
    data: Cupon;
}

export const CuponService = {
    async getCuponByCodigo(codigo: string): Promise<SingleResponseWrapper> {
        const response: AxiosResponse<SingleResponseWrapper> = await apiClient.get(`/cupones/${codigo}`);
        return response.data;
    },
};
