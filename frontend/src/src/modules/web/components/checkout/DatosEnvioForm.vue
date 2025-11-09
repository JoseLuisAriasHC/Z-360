<script setup lang="ts">
    import { ref } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import FormField from '../FormField.vue';
    // import { GuestOrderService } from '@/services/GuestOrderService'
    import { useRouter } from 'vue-router';
    import ButtonDark from '../ButtonDark.vue';

    interface Props {
        onNextStep: () => void;
    }

    const router = useRouter();
    const toast = useToast();

    const props = defineProps<Props>();
    const emit = defineEmits<{
        (e: 'token', token: string): void;
    }>();

    interface GuestOrderForm {
        envio_nombre: string;
        envio_email: string;
        envio_telefono: string;
        envio_direccion_calle: string;
        envio_direccion_numero_calle: string;
        envio_direccion_piso_info: string;
        envio_direccion_ciudad: string;
        envio_direccion_cp: string;
        usar_misma_direccion_facturacion: boolean;
        facturacion_nombre: string;
        facturacion_email: string;
        facturacion_telefono: string;
        facturacion_direccion_calle: string;
        facturacion_direccion_numero_calle: string;
        facturacion_direccion_piso_info: string;
        facturacion_direccion_ciudad: string;
        facturacion_direccion_cp: string;
        cupon_codigo: string;
    }

    const formState = ref<GuestOrderForm>({
        envio_nombre: '',
        envio_email: '',
        envio_telefono: '',
        envio_direccion_calle: '',
        envio_direccion_numero_calle: '',
        envio_direccion_piso_info: '',
        envio_direccion_ciudad: '',
        envio_direccion_cp: '',
        usar_misma_direccion_facturacion: true,
        facturacion_nombre: '',
        facturacion_email: '',
        facturacion_telefono: '',
        facturacion_direccion_calle: '',
        facturacion_direccion_numero_calle: '',
        facturacion_direccion_piso_info: '',
        facturacion_direccion_ciudad: '',
        facturacion_direccion_cp: '',
        cupon_codigo: '',
    });

    const errors = ref<Record<keyof GuestOrderForm, string>>({
        envio_nombre: '',
        envio_email: '',
        envio_telefono: '',
        envio_direccion_calle: '',
        envio_direccion_numero_calle: '',
        envio_direccion_piso_info: '',
        envio_direccion_ciudad: '',
        envio_direccion_cp: '',
        usar_misma_direccion_facturacion: '',
        facturacion_nombre: '',
        facturacion_email: '',
        facturacion_telefono: '',
        facturacion_direccion_calle: '',
        facturacion_direccion_numero_calle: '',
        facturacion_direccion_piso_info: '',
        facturacion_direccion_ciudad: '',
        facturacion_direccion_cp: '',
        cupon_codigo: '',
    });

    const loading = ref(false);

    const clearError = (field: keyof GuestOrderForm) => {
        errors.value[field] = '';
    };

    const handleSubmit = async () => {
        loading.value = true;
        Object.keys(errors.value).forEach((key) => (errors.value[key as keyof GuestOrderForm] = ''));

        try {
            const payload = { ...formState.value, items: [] };
            const response = await GuestOrderService.createGuestOrder(payload);

            emit('token', response.token);
            props.onNextStep();
        } catch (error: any) {
            const responseData = error.response?.data;
            if (error.response?.status === 422 && responseData?.errors) {
                Object.keys(responseData.errors).forEach((key: string) => {
                    if (errors.value[key as keyof GuestOrderForm] !== undefined) {
                        errors.value[key as keyof GuestOrderForm] = responseData.errors[key][0];
                    }
                });
                toast.add({
                    severity: 'error',
                    summary: 'Errores de validación',
                    detail: 'Por favor, corrige los campos marcados.',
                    life: 5000,
                });
            } else {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: responseData?.message || 'No se pudo crear el pedido.',
                    life: 3000,
                });
            }
        } finally {
            loading.value = false;
        }
    };
</script>

<template>
    <form @submit.prevent="handleSubmit" class="flex flex-col gap-4">
        <h3 class="text-xl font-semibold">Datos de Envío</h3>
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-12">
                <FormField id="envio_email" label="Email" :error="errors.envio_email">
                    <InputText v-model="formState.envio_email" @input="clearError('envio_email')" class="w-full" :invalid="!!errors.envio_email" />
                </FormField>
            </div>

            <div class="col-span-12 md:col-span-6">
                <FormField id="envio_nombre" label="Nombre completo" :error="errors.envio_nombre">
                    <InputText v-model="formState.envio_nombre" @input="clearError('envio_nombre')" class="w-full" :invalid="!!errors.envio_nombre" />
                </FormField>
            </div>

            <div class="col-span-12 md:col-span-6">
                <FormField id="envio_telefono" label="Teléfono" :error="errors.envio_telefono">
                    <InputText
                        v-model="formState.envio_telefono"
                        @input="clearError('envio_telefono')"
                        class="w-full"
                        :invalid="!!errors.envio_telefono" />
                </FormField>
            </div>

            <div class="col-span-12 md:col-span-9">
                <FormField id="envio_direccion_calle" label="Calle" :error="errors.envio_direccion_calle">
                    <InputText
                        v-model="formState.envio_direccion_calle"
                        @input="clearError('envio_direccion_calle')"
                        class="w-full"
                        :invalid="!!errors.envio_direccion_calle" />
                </FormField>
            </div>

            <div class="col-span-12 md:col-span-3">
                <FormField id="envio_direccion_numero_calle" label="Número" :error="errors.envio_direccion_numero_calle">
                    <InputText
                        v-model="formState.envio_direccion_numero_calle"
                        @input="clearError('envio_direccion_numero_calle')"
                        class="w-full"
                        :invalid="!!errors.envio_direccion_numero_calle" />
                </FormField>
            </div>

            <div class="col-span-12 md:col-span-12">
                <FormField id="envio_direccion_piso_info" label="Piso / info extra" :error="errors.envio_direccion_piso_info">
                    <InputText v-model="formState.envio_direccion_piso_info" @input="clearError('envio_direccion_piso_info')" class="w-full" />
                </FormField>
            </div>

            <div class="col-span-12 md:col-span-4">
                <FormField id="envio_direccion_cp" label="Código postal" :error="errors.envio_direccion_cp">
                    <InputText
                        v-model="formState.envio_direccion_cp"
                        @input="clearError('envio_direccion_cp')"
                        class="w-full"
                        :invalid="!!errors.envio_direccion_cp" />
                </FormField>
            </div>

            <div class="col-span-12 md:col-span-4">
                <FormField id="envio_direccion_ciudad" label="Ciudad" :error="errors.envio_direccion_ciudad">
                    <InputText
                        v-model="formState.envio_direccion_ciudad"
                        @input="clearError('envio_direccion_ciudad')"
                        class="w-full"
                        :invalid="!!errors.envio_direccion_ciudad" />
                </FormField>
            </div>

            <div class="col-span-12 md:col-span-4">
                <FormField id="envio_pais" label="España">
                    <InputText class="w-full" :disabled="true" />
                </FormField>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <Checkbox inputId="usar_misma_direccion_facturacion" v-model="formState.usar_misma_direccion_facturacion" binary />
            <label for="usar_misma_direccion_facturacion">Usar la misma dirección para facturación</label>
        </div>

        <template v-if="!formState.usar_misma_direccion_facturacion">
            <h3 class="text-xl font-semibold mt-4">Datos de Facturación</h3>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 md:col-span-12">
                    <FormField id="envio_email" label="Email" :error="errors.envio_email">
                        <InputText
                            v-model="formState.envio_email"
                            @input="clearError('envio_email')"
                            class="w-full"
                            :invalid="!!errors.envio_email" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-6">
                    <FormField id="facturacion_nombre" label="Nombre completo" :error="errors.facturacion_nombre">
                        <InputText
                            v-model="formState.facturacion_nombre"
                            @input="clearError('facturacion_nombre')"
                            class="w-full"
                            :invalid="!!errors.facturacion_nombre" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-6">
                    <FormField id="facturacion_telefono" label="Teléfono" :error="errors.facturacion_telefono">
                        <InputText
                            v-model="formState.facturacion_telefono"
                            @input="clearError('facturacion_telefono')"
                            class="w-full"
                            :invalid="!!errors.facturacion_telefono" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-9">
                    <FormField id="facturacion_direccion_calle" label="Calle" :error="errors.facturacion_direccion_calle">
                        <InputText
                            v-model="formState.facturacion_direccion_calle"
                            @input="clearError('facturacion_direccion_calle')"
                            class="w-full"
                            :invalid="!!errors.facturacion_direccion_calle" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-3">
                    <FormField id="facturacion_direccion_numero_calle" label="Número" :error="errors.facturacion_direccion_numero_calle">
                        <InputText
                            v-model="formState.facturacion_direccion_numero_calle"
                            @input="clearError('facturacion_direccion_numero_calle')"
                            class="w-full"
                            :invalid="!!errors.facturacion_direccion_numero_calle" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-12">
                    <FormField id="facturacion_direccion_piso_info" label="Piso / info extra" :error="errors.facturacion_direccion_piso_info">
                        <InputText
                            v-model="formState.facturacion_direccion_piso_info"
                            @input="clearError('facturacion_direccion_piso_info')"
                            class="w-full" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-4">
                    <FormField id="facturacion_direccion_cp" label="Código postal" :error="errors.facturacion_direccion_cp">
                        <InputText
                            v-model="formState.facturacion_direccion_cp"
                            @input="clearError('facturacion_direccion_cp')"
                            class="w-full"
                            :invalid="!!errors.facturacion_direccion_cp" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-4">
                    <FormField id="facturacion_direccion_ciudad" label="Ciudad" :error="errors.facturacion_direccion_ciudad">
                        <InputText
                            v-model="formState.facturacion_direccion_ciudad"
                            @input="clearError('facturacion_direccion_ciudad')"
                            class="w-full"
                            :invalid="!!errors.facturacion_direccion_ciudad" />
                    </FormField>
                </div>

                <div class="col-span-12 md:col-span-4">
                    <FormField id="facturacion_pais" label="España">
                        <InputText class="w-full" :disabled="true" />
                    </FormField>
                </div>
            </div>
        </template>

        <div class="flex justify-end mt-6">
            <ButtonDark :loading="loading" variant="primary" size="lg" border-radius="full">Guardar y continuar</ButtonDark>
        </div>
    </form>
</template>

<style scoped>
    input.p-inputtext {
        padding-top: 1.2rem;
        padding-bottom: 1.2rem;
    }
</style>
