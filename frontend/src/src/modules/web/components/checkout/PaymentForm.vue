<script setup lang="ts">
    import { ref, onMounted, nextTick } from 'vue';
    import { useRouter } from 'vue-router';
    import { useToast } from 'primevue/usetoast';
    import { useCestaStore } from '../../stores/cesta';
    import { useCheckoutStore } from '../../stores/checkout';
    import { useCuponStore } from '../../stores/cupon';
    import { loadStripe, type Stripe, type StripeElements } from '@stripe/stripe-js';
    import { GuestOrderService } from '../../services/GuestOrderService';
    import ButtonDark from '../ButtonDark.vue';

    interface Props {
        clientSecret: string;
        paymentIntentId: string;
        orderId: number;
        token: string;
        onPrevStep: () => void;
    }

    const props = defineProps<Props>();

    const router = useRouter();
    const toast = useToast();
    const cestaStore = useCestaStore();
    const checkoutStore = useCheckoutStore();
    const cuponStore = useCuponStore();

    const stripe = ref<Stripe | null>(null);
    const elements = ref<StripeElements | null>(null);
    const cardElement = ref<HTMLDivElement | null>(null);
    const loading = ref(false);
    const mounted = ref(false);

    // Cargar Stripe
    onMounted(async () => {
        try {
            const stripeKey = import.meta.env.VITE_STRIPE_PUBLIC_KEY;
            stripe.value = await loadStripe(stripeKey);
            elements.value = stripe.value.elements({ clientSecret: props.clientSecret });
            mounted.value = true;

            // Espera a que Vue renderice el div con el ref
            await nextTick();

            const paymentElement = elements.value.create('payment');
            if (cardElement.value) paymentElement.mount(cardElement.value);
        } catch (error) {
            console.error('Error al inicializar Stripe:', error);
        }
    });

    const handleSubmit = async () => {
        if (!stripe.value || !elements.value) {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Sistema de pago no inicializado',
                life: 3000,
            });
            return;
        }

        loading.value = true;

        try {
            // Confirmar el pago con Stripe
            const { error: submitError } = await elements.value.submit();

            if (submitError) {
                throw submitError;
            }

            const { error, paymentIntent } = await stripe.value.confirmPayment({
                elements: elements.value,
                confirmParams: {
                    return_url: `${window.location.origin}/checkout/success`,
                },
                redirect: 'if_required',
            });

            if (error) {
                throw error;
            }

            if (paymentIntent && paymentIntent.status === 'succeeded') {
                // Confirmar el pago en el backend
                const confirmResult = await GuestOrderService.confirmarPago(props.orderId, paymentIntent.id);

                if (confirmResult.success) {
                    toast.add({
                        severity: 'success',
                        summary: '¡Pago exitoso!',
                        detail: 'Tu pedido ha sido confirmado',
                        life: 5000,
                    });

                    // Limpiar datos
                    cestaStore.clearCesta();
                    checkoutStore.clearCheckout();
                    cuponStore.clearCupon();

                    // Redirigir a página de éxito
                    router.push({
                        name: 'checkout-success',
                        query: { order: props.token },
                    });
                } else {
                    throw new Error(confirmResult.message || 'Error al confirmar el pago');
                }
            }
        } catch (error: any) {
            console.error('Error al procesar el pago:', error);

            toast.add({
                severity: 'error',
                summary: 'Error en el pago',
                detail: error.message || 'No se pudo procesar el pago',
                life: 5000,
            });
        } finally {
            loading.value = false;
        }
    };
</script>

<template>
    <div class="w-full">
        <h3 class="text-2xl font-semibold mb-6 font-oswald">Método de Pago</h3>

        <!-- Loading state mientras se carga Stripe -->
        <div v-if="!mounted" class="flex items-center justify-center py-12">
            <ProgressSpinner style="width: 50px; height: 50px" />
        </div>

        <!-- Formulario de pago -->
        <div v-else>
            <!-- Elemento de Stripe -->
            <div ref="cardElement" class="stripe-element mb-6"></div>

            <!-- Información de seguridad -->
            <div class="security-info mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-start gap-3">
                    <i class="pi pi-lock text-blue-600 text-xl mt-1"></i>
                    <div>
                        <p class="font-semibold text-blue-900 mb-1">Pago seguro</p>
                        <p class="text-sm text-blue-700">Tu información de pago está protegida y encriptada</p>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex gap-4 justify-between">
                <ButtonDark variant="outline" size="lg" icon="pi pi-arrow-left" @click="onPrevStep" :disabled="loading">Volver</ButtonDark>

                <ButtonDark variant="primary" size="lg" icon="pi pi-check" :loading="loading" @click="handleSubmit">
                    {{ loading ? 'Procesando...' : 'Completar pago' }}
                </ButtonDark>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
