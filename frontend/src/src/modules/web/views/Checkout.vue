<script setup lang="ts">
    import DatosEnvioForm from '../components/checkout/DatosEnvioForm.vue';
    import PaymentForm from '../components/checkout/PaymentForm.vue';
    import OrderSummary from '../components/checkout/OrderSummary.vue';
    import { useCestaStore } from '../stores/cesta';
    import router from '@/router';
    import { onBeforeRouteUpdate } from 'vue-router';
    import { ref } from 'vue';

    const cestaStore = useCestaStore();

    // Estado del pago
    const paymentData = ref<{
        token: string;
        clientSecret: string;
        paymentIntentId: string;
        orderId?: number;
    } | null>(null);

    const currentStep = ref('1');

    const handleOrderCreated = (data: { token: string; clientSecret: string; paymentIntentId: string; orderId: number }) => {
        paymentData.value = {
            ...data,
        };
    };

    const activateStep = (step: string) => {
        currentStep.value = step;
    };

    onBeforeRouteUpdate(() => {
        if (cestaStore.items.length === 0) {
            router.push({ name: 'carrito' });
        }
    });

    if (cestaStore.items.length === 0) {
        router.push({ name: 'carrito' });
    }
</script>

<template>
    <div class="checkout-container">
        <Stepper v-model:value="currentStep" linear class="w-full">
            <StepList>
                <Step value="1">
                    <span class="step-title">Datos de envío</span>
                </Step>
                <Step value="2">
                    <span class="step-title">Pago</span>
                </Step>
                <Step value="3">
                    <span class="step-title">Confirmación</span>
                </Step>
            </StepList>

            <StepPanels>
                <!-- Step 1: Datos de Envío -->
                <StepPanel value="1">
                    <div class="checkout-content">
                        <div class="checkout-form">
                            <DatosEnvioForm :on-next-step="() => activateStep('2')" @order-created="handleOrderCreated" />
                        </div>
                        <div class="checkout-summary">
                            <OrderSummary />
                        </div>
                    </div>
                </StepPanel>

                <!-- Step 2: Pago -->
                <StepPanel value="2">
                    <div class="checkout-content">
                        <div class="checkout-form">
                            <PaymentForm
                                v-if="paymentData"
                                :client-secret="paymentData.clientSecret"
                                :payment-intent-id="paymentData.paymentIntentId"
                                :order-id="paymentData.orderId || 0"
                                :token="paymentData.token"
                                :on-prev-step="() => activateStep('1')" />
                            <div v-else class="p-8 text-center">
                                <Message severity="warn" :closable="false">No hay información de pago disponible</Message>
                            </div>
                        </div>
                        <div class="checkout-summary">
                            <OrderSummary />
                        </div>
                    </div>
                </StepPanel>

                <!-- Step 3: Confirmación (mostrado después de pago exitoso) -->
                <StepPanel value="3">
                    <div class="success-message">
                        <div class="text-center py-12">
                            <i class="pi pi-check-circle text-6xl text-green-500 mb-4"></i>
                            <h2 class="text-3xl font-bold mb-2">¡Pago completado!</h2>
                            <p class="text-gray-600">Tu pedido ha sido procesado exitosamente</p>
                        </div>
                    </div>
                </StepPanel>
            </StepPanels>
        </Stepper>
    </div>
</template>

<style scoped>
    .checkout-container {
        @apply w-full px-4 py-8;
    }

    .checkout-content {
        @apply grid grid-cols-12 gap-8 mx-auto xl:pt-8 xl:w-4/5;
    }

    .checkout-form {
        @apply col-span-12 xl:col-span-8;
        min-height: 60vh;
    }

    .checkout-summary {
        @apply col-span-12 xl:col-span-4;
    }

    .step-title {
        @apply font-rubik font-semibold;
    }

    /* Estilos personalizados para el Stepper */
    :deep(.p-stepper-header) {
        @apply font-rubik;
    }

    :deep(.p-stepper-title) {
        @apply font-semibold;
    }

    /* Ajustes responsive */
    @media (max-width: 1279px) {
        .checkout-summary {
            @apply order-first mb-6;
        }
    }

    input.p-inputtext {
        padding-top: 1.2rem;
        padding-bottom: 1.2rem;
    }
</style>
