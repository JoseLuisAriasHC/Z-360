<script setup lang="ts">
    import { computed } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import { useCheckoutStore } from '../../stores/checkout';
    import { useCheckoutForm } from '../../composables/useCheckoutForm';
    import FormField from '../FormField.vue';
    import ButtonDark from '../ButtonDark.vue';

    interface Props {
        onNextStep: () => void;
    }

    const props = defineProps<Props>();
    const emit = defineEmits<{
        (e: 'order-created', data: { token: string; clientSecret: string; paymentIntentId: string, orderId: number }): void;
    }>();

    const toast = useToast();
    const checkoutStore = useCheckoutStore();
    const { loading, errors, isFormValid, clearError, submitOrder } = useCheckoutForm();

    // Referencias reactivas a los datos del store
    const envio = computed(() => checkoutStore.envio);
    const facturacion = computed(() => checkoutStore.facturacion);

    const handleSubmit = async () => {
        const result = await submitOrder();

        if (result.success && result.data) {
            toast.add({
                severity: 'success',
                summary: 'Pedido creado',
                detail: 'Procediendo al pago...',
                life: 3000,
            });

            // Emitir datos necesarios para el siguiente paso
            emit('order-created', {
                token: result.data.token,
                clientSecret: result.data.payment.clientSecret,
                paymentIntentId: result.data.payment.paymentIntentId,
                orderId: result.data.data.id,
            });

            props.onNextStep();
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: result.error || 'No se pudo crear el pedido',
                life: 5000,
            });
        }
    };
</script>

<template>
    <form @submit.prevent="handleSubmit" class="flex flex-col gap-6">
        <!-- Datos de Envío -->
        <section>
            <h3 class="text-2xl font-semibold mb-8 font-rubik">Datos de Envío</h3>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <FormField id="envio_email" label="Email" :error="errors.envio_email">
                        <InputText
                            v-model="envio.envio_email"
                            @input="clearError('envio_email')"
                            type="email"
                            class="w-full"
                            :invalid="!!errors.envio_email" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-6">
                    <FormField id="envio_nombre" label="Nombre completo" :error="errors.envio_nombre">
                        <InputText v-model="envio.envio_nombre" @input="clearError('envio_nombre')" class="w-full" :invalid="!!errors.envio_nombre" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-6">
                    <FormField id="envio_telefono" label="Teléfono" :error="errors.envio_telefono">
                        <InputText
                            v-model="envio.envio_telefono"
                            @input="clearError('envio_telefono')"
                            class="w-full"
                            :invalid="!!errors.envio_telefono" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-9">
                    <FormField id="envio_direccion_calle" label="Calle" :error="errors.envio_direccion_calle">
                        <InputText
                            v-model="envio.envio_direccion_calle"
                            @input="clearError('envio_direccion_calle')"
                            class="w-full"
                            :invalid="!!errors.envio_direccion_calle" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-3">
                    <FormField id="envio_direccion_numero_calle" label="Número" :error="errors.envio_direccion_numero_calle">
                        <InputText
                            v-model="envio.envio_direccion_numero_calle"
                            @input="clearError('envio_direccion_numero_calle')"
                            class="w-full"
                            :invalid="!!errors.envio_direccion_numero_calle" />
                    </FormField>
                </div>

                <div class="col-span-12">
                    <FormField id="envio_direccion_piso_info" label="Piso / Info adicional (opcional)" :error="errors.envio_direccion_piso_info">
                        <InputText v-model="envio.envio_direccion_piso_info" @input="clearError('envio_direccion_piso_info')" class="w-full" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-4">
                    <FormField id="envio_direccion_cp" label="Código postal" :error="errors.envio_direccion_cp">
                        <InputText
                            v-model="envio.envio_direccion_cp"
                            @input="clearError('envio_direccion_cp')"
                            class="w-full"
                            :invalid="!!errors.envio_direccion_cp" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-4">
                    <FormField id="envio_direccion_ciudad" label="Ciudad" :error="errors.envio_direccion_ciudad">
                        <InputText
                            v-model="envio.envio_direccion_ciudad"
                            @input="clearError('envio_direccion_ciudad')"
                            class="w-full"
                            :invalid="!!errors.envio_direccion_ciudad" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-4">
                    <FormField id="envio_pais" label="España">
                        <InputText class="w-full" disabled />
                    </FormField>
                </div>
            </div>
        </section>

        <!-- Checkbox misma dirección -->
        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
            <Checkbox inputId="usar_misma_direccion_facturacion" v-model="envio.usar_misma_direccion_facturacion" binary />
            <label for="usar_misma_direccion_facturacion" class="cursor-pointer font-medium">Usar la misma dirección para facturación</label>
        </div>

        <!-- Datos de Facturación (condicional) -->
        <section v-if="!envio.usar_misma_direccion_facturacion">
            <h3 class="text-2xl font-semibold mb-4 font-oswald">Datos de Facturación</h3>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <FormField id="facturacion_email" label="Email" :error="errors.facturacion_email">
                        <InputText
                            v-model="facturacion.facturacion_email"
                            @input="clearError('facturacion_email')"
                            type="email"
                            class="w-full"
                            :invalid="!!errors.facturacion_email" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-6">
                    <FormField id="facturacion_nombre" label="Nombre completo" :error="errors.facturacion_nombre">
                        <InputText
                            v-model="facturacion.facturacion_nombre"
                            @input="clearError('facturacion_nombre')"
                            class="w-full"
                            :invalid="!!errors.facturacion_nombre" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-6">
                    <FormField id="facturacion_telefono" label="Teléfono" :error="errors.facturacion_telefono">
                        <InputText
                            v-model="facturacion.facturacion_telefono"
                            @input="clearError('facturacion_telefono')"
                            class="w-full"
                            :invalid="!!errors.facturacion_telefono" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-9">
                    <FormField id="facturacion_direccion_calle" label="Calle" :error="errors.facturacion_direccion_calle">
                        <InputText
                            v-model="facturacion.facturacion_direccion_calle"
                            @input="clearError('facturacion_direccion_calle')"
                            class="w-full"
                            :invalid="!!errors.facturacion_direccion_calle" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-3">
                    <FormField id="facturacion_direccion_numero_calle" label="Número" :error="errors.facturacion_direccion_numero_calle">
                        <InputText
                            v-model="facturacion.facturacion_direccion_numero_calle"
                            @input="clearError('facturacion_direccion_numero_calle')"
                            class="w-full"
                            :invalid="!!errors.facturacion_direccion_numero_calle" />
                    </FormField>
                </div>

                <div class="col-span-12">
                    <FormField id="facturacion_direccion_piso_info" label="Piso / Info adicional (opcional)">
                        <InputText v-model="facturacion.facturacion_direccion_piso_info" class="w-full" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-4">
                    <FormField id="facturacion_direccion_cp" label="Código postal" :error="errors.facturacion_direccion_cp">
                        <InputText
                            v-model="facturacion.facturacion_direccion_cp"
                            @input="clearError('facturacion_direccion_cp')"
                            class="w-full"
                            :invalid="!!errors.facturacion_direccion_cp" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-4">
                    <FormField id="facturacion_direccion_ciudad" label="Ciudad" :error="errors.facturacion_direccion_ciudad">
                        <InputText
                            v-model="facturacion.facturacion_direccion_ciudad"
                            @input="clearError('facturacion_direccion_ciudad')"
                            class="w-full"
                            :invalid="!!errors.facturacion_direccion_ciudad" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-4">
                    <FormField id="facturacion_pais" label="España">
                        <InputText class="w-full" disabled />
                    </FormField>
                </div>
            </div>
        </section>

        <!-- Botón de envío -->
        <div class="flex justify-end mt-6">
            <ButtonDark
                type="submit"
                variant="primary"
                size="xl"
                :loading="loading"
                :disabled="!isFormValid"
                border-radius="full"
                icon="pi pi-arrow-right"
                icon-position="right">
                {{ loading ? 'Procesando...' : 'Continuar al pago' }}
            </ButtonDark>
        </div>
    </form>
</template>

<style scoped>
    input.p-inputtext {
        padding-top: 1.2rem;
        padding-bottom: 1.2rem;
    }
</style>
