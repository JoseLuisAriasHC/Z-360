<script setup lang="ts">
    import { ref, onMounted } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import AppButton from '@/components/AppButton.vue';
    import type { Order } from '../services/GuestOrderService';

    const route = useRoute();
    const router = useRouter();

    const loading = ref(true);
    const order = ref<Order | null>(null);
    const error = ref<string | null>(null);

    onMounted(async () => {
        const orderId = route.query.order as string;

        if (!orderId) {
            error.value = 'No se encontró el pedido';
            loading.value = false;
            return;
        }

        try {
            // Aquí podrías obtener los detalles del pedido si lo necesitas
            // order.value = await GuestOrderService.getOrderById(Number(orderId));
            loading.value = false;
        } catch (e) {
            error.value = 'Error al cargar los detalles del pedido';
            loading.value = false;
        }
    });

    const handleGoHome = () => {
        router.push({ name: 'home' });
    };

    const handleViewOrders = () => {
        router.push({ name: 'my-orders' });
    };
</script>

<template>
    <div class="success-page">
        <!-- Loading -->
        <div v-if="loading" class="flex justify-center items-center min-h-screen">
            <ProgressSpinner />
        </div>

        <!-- Error -->
        <div v-else-if="error" class="flex justify-center items-center min-h-screen p-4">
            <Message severity="error" :closable="false">
                {{ error }}
            </Message>
        </div>

        <!-- Success content -->
        <div v-else class="success-content">
            <div class="success-card">
                <!-- Icono de éxito -->
                <div class="success-icon">
                    <i class="pi pi-check-circle"></i>
                </div>

                <!-- Mensaje principal -->
                <h1 class="success-title">¡Pago completado con éxito!</h1>
                <p class="success-subtitle">Tu pedido ha sido procesado correctamente</p>

                <!-- Información del pedido -->
                <div class="order-info">
                    <div class="info-item">
                        <i class="pi pi-envelope"></i>
                        <div>
                            <p class="info-label">Confirmación enviada</p>
                            <p class="info-value">Revisa tu correo electrónico</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="pi pi-box"></i>
                        <div>
                            <p class="info-label">Preparando envío</p>
                            <p class="info-value">Te notificaremos cuando se envíe</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="pi pi-clock"></i>
                        <div>
                            <p class="info-label">Tiempo estimado</p>
                            <p class="info-value">3-5 días laborables</p>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="success-actions">
                    <AppButton variant="primary" size="lg" icon="pi pi-home" @click="handleGoHome">Volver al inicio</AppButton>

                    <AppButton variant="outline" size="lg" icon="pi pi-list" @click="handleViewOrders">Ver mis pedidos</AppButton>
                </div>

                <!-- Nota adicional -->
                <div class="success-note">
                    <p class="text-sm text-gray-600 text-center">
                        ¿Tienes alguna pregunta? Contacta con nuestro
                        <a href="/contacto" class="text-blue-600 hover:underline">servicio de atención al cliente</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .success-page {
        @apply min-h-screen bg-gray-50 py-12 px-4;
    }

    .success-content {
        @apply max-w-2xl mx-auto;
    }

    .success-card {
        @apply bg-white rounded-2xl shadow-lg p-8 md:p-12;
    }

    .success-icon {
        @apply text-center mb-6;
    }

    .success-icon i {
        @apply text-8xl text-green-500;
    }

    .success-title {
        @apply text-3xl md:text-4xl font-bold text-center mb-4 font-oswald;
    }

    .success-subtitle {
        @apply text-lg text-gray-600 text-center mb-8;
    }

    .order-info {
        @apply bg-gray-50 rounded-lg p-6 mb-8 space-y-4;
    }

    .info-item {
        @apply flex items-start gap-4;
    }

    .info-item i {
        @apply text-2xl text-blue-600 mt-1;
    }

    .info-label {
        @apply font-semibold text-gray-900 mb-1;
    }

    .info-value {
        @apply text-sm text-gray-600;
    }

    .success-actions {
        @apply flex flex-col md:flex-row gap-4 justify-center mb-6;
    }

    .success-note {
        @apply pt-6 border-t border-gray-200;
    }
</style>
