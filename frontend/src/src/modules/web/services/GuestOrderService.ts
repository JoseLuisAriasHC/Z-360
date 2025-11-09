import apiClient from '@/services/api';
import type { AxiosResponse } from 'axios';

export interface GuestOrderItem {
    variant_size_id: number;
    cantidad: number;
}

export interface GuestOrderPayload {
    items: GuestOrderItem[];
    envio_nombre: string;
    envio_email: string;
    envio_telefono: string;
    envio_direccion_calle: string;
    envio_direccion_numero_calle: string;
    envio_direccion_piso_info: string;
    envio_direccion_ciudad: string;
    envio_direccion_cp: string;
    usar_misma_direccion_facturacion: boolean;
    facturacion_nombre?: string;
    facturacion_email?: string;
    facturacion_telefono?: string;
    facturacion_direccion_calle?: string;
    facturacion_direccion_numero_calle?: string;
    facturacion_direccion_piso_info?: string;
    facturacion_direccion_ciudad?: string;
    facturacion_direccion_cp?: string;
    cupon_codigo?: string;
}

export interface OrderItem {
    id: number;
    variant_size_id: number;
    cantidad: number;
    precio_unitario: number;
}

export interface Order {
    id: number;
    token: string;
    subtotal: number;
    descuento: number;
    total: number;
    estado: string;
    pago_estado: string;
    pago_id: string;
    items: OrderItem[];
}

export interface CreateOrderResponse {
    success: boolean;
    data: Order;
    token: string;
    payment: {
        clientSecret: string;
        paymentIntentId: string;
    };
}

export interface ConfirmPaymentResponse {
    success: boolean;
    message: string;
    data?: Order;
    payment_status?: string;
}

export const GuestOrderService = {
    /**
     * Crear pedido de invitado
     * POST /api/orders/guest
     */
    async createGuestOrder(payload: GuestOrderPayload): Promise<CreateOrderResponse> {
        const response: AxiosResponse<CreateOrderResponse> = await apiClient.post('orders/guest', payload);
        return response.data;
    },

    /**
     * Confirmar pago después de Stripe
     * POST /api/orders/{order}/confirmar-pago
     */
    async confirmarPago(orderId: number, paymentIntentId: string): Promise<ConfirmPaymentResponse> {
        const response: AxiosResponse<ConfirmPaymentResponse> = await apiClient.post(`orders/${orderId}/confirmar-pago`, {
            payment_intent_id: paymentIntentId,
        });
        return response.data;
    },

    /**
     * Obtener pedido por token (para invitados)
     * GET /api/orders/token/{token}
     */
    async getOrderByToken(token: string): Promise<Order> {
        const response: AxiosResponse<{ success: boolean; data: Order }> = await apiClient.get(`orders/token/${token}`);
        return response.data.data;
    },
};
