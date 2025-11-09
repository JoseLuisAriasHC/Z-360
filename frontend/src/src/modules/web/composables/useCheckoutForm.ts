// composables/useCheckoutForm.ts
import { ref, reactive, computed } from 'vue';
import type { AxiosError } from 'axios';
import { useCheckoutStore } from '../stores/checkout';
import { useCestaStore } from '../stores/cesta';
import { useCuponStore } from '../stores/cupon';
import { GuestOrderService, type GuestOrderItem, type GuestOrderPayload } from '../services/GuestOrderService';

export interface FormErrors {
    [key: string]: string;
}

export function useCheckoutForm() {
    const checkoutStore = useCheckoutStore();
    const cestaStore = useCestaStore();
    const cuponStore = useCuponStore();

    const loading = ref(false);
    const errors = reactive<FormErrors>({});

    // Computed para verificar si el formulario es válido
    const isFormValid = computed(() => {
        const { envio, facturacion } = checkoutStore;

        // Validar datos de envío
        const envioValid = !!(
            envio.envio_nombre &&
            envio.envio_email &&
            envio.envio_telefono &&
            envio.envio_direccion_calle &&
            envio.envio_direccion_numero_calle &&
            envio.envio_direccion_ciudad &&
            envio.envio_direccion_cp
        );

        // Si usa misma dirección, solo validar envío
        if (envio.usar_misma_direccion_facturacion) {
            return envioValid;
        }

        // Validar datos de facturación
        const facturacionValid = !!(
            facturacion.facturacion_nombre &&
            facturacion.facturacion_email &&
            facturacion.facturacion_telefono &&
            facturacion.facturacion_direccion_calle &&
            facturacion.facturacion_direccion_numero_calle &&
            facturacion.facturacion_direccion_ciudad &&
            facturacion.facturacion_direccion_cp
        );

        return envioValid && facturacionValid;
    });

    const clearError = (field: string) => {
        delete errors[field];
    };

    const clearAllErrors = () => {
        Object.keys(errors).forEach((key) => delete errors[key]);
    };

    const handleValidationErrors = (errorResponse: any) => {
        if (errorResponse?.errors) {
            Object.entries(errorResponse.errors).forEach(([key, messages]) => {
                if (Array.isArray(messages) && messages.length > 0) {
                    errors[key] = messages[0];
                }
            });
        }
    };

    const prepareOrderPayload = (): GuestOrderPayload => {
        const { envio, facturacion } = checkoutStore;

        // Mapear los items de la cesta
        const items: GuestOrderItem[] = cestaStore.items.map((item) => ({
            variant_size_id: item.id,
            cantidad: item.cantidad,
        }));

        // Construir payload principal
        const payload: GuestOrderPayload = {
            items,
            envio_nombre: envio.envio_nombre,
            envio_email: envio.envio_email,
            envio_telefono: envio.envio_telefono,
            envio_direccion_calle: envio.envio_direccion_calle,
            envio_direccion_numero_calle: envio.envio_direccion_numero_calle,
            envio_direccion_piso_info: envio.envio_direccion_piso_info,
            envio_direccion_ciudad: envio.envio_direccion_ciudad,
            envio_direccion_cp: envio.envio_direccion_cp,
            usar_misma_direccion_facturacion: envio.usar_misma_direccion_facturacion,
        };

        // Agregar datos de facturación si no usa la misma dirección
        if (!envio.usar_misma_direccion_facturacion) {
            payload.facturacion_nombre = facturacion.facturacion_nombre;
            payload.facturacion_email = facturacion.facturacion_email;
            payload.facturacion_telefono = facturacion.facturacion_telefono;
            payload.facturacion_direccion_calle = facturacion.facturacion_direccion_calle;
            payload.facturacion_direccion_numero_calle = facturacion.facturacion_direccion_numero_calle;
            payload.facturacion_direccion_piso_info = facturacion.facturacion_direccion_piso_info;
            payload.facturacion_direccion_ciudad = facturacion.facturacion_direccion_ciudad;
            payload.facturacion_direccion_cp = facturacion.facturacion_direccion_cp;
        }

        // Agregar cupón si existe
        if (cuponStore.cupon?.codigo) {
            payload.cupon_codigo = cuponStore.cupon.codigo;
        }

        return payload;
    };

    const submitOrder = async () => {
        loading.value = true;
        clearAllErrors();

        try {
            const payload = prepareOrderPayload();
            const response = await GuestOrderService.createGuestOrder(payload);

            return {
                success: true,
                data: response,
            };
        } catch (error) {
            const axiosError = error as AxiosError<any>;

            if (axiosError.response?.status === 422) {
                handleValidationErrors(axiosError.response.data);
                return {
                    success: false,
                    error: 'Errores de validación. Por favor, corrige los campos marcados.',
                };
            }

            return {
                success: false,
                error: axiosError.response?.data?.message || 'Error al crear el pedido',
            };
        } finally {
            loading.value = false;
        }
    };

    return {
        loading,
        errors,
        isFormValid,
        clearError,
        clearAllErrors,
        submitOrder,
    };
}
