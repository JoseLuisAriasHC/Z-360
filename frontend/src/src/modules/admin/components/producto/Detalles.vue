<script setup lang="ts">
    import { ref, onMounted } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import { type ProductoDetalle, ProductoDetalleService } from '@admin/services/ProductoDetalleService';
    import FormField from '@admin/components/FormField.vue';

    // --- PROPS Y HOOKS ---
    const toast = useToast();

    const props = defineProps<{
        productoId: number | null;
    }>();

    interface DetalleFormState extends Omit<ProductoDetalle, 'id'> {}
    const detalleState = ref<DetalleFormState & { id?: number }>({
        product_id: 0,
        parte_superior: '',
        parte_inferior: '',
        suela: '',
        cuidados: '',
    });

    // Referencias para manejar errores de validación del backend (422)
    const loading = ref(false);
    const parteSuperiorError = ref('');
    const parteInferiorError = ref('');
    const suelaError = ref('');
    const cuidadosError = ref('');

    const clearErrores = (field: keyof DetalleFormState) => {
        if (field === 'parte_superior') parteSuperiorError.value = '';
        if (field === 'parte_inferior') parteInferiorError.value = '';
        if (field === 'suela') suelaError.value = '';
        if (field === 'cuidados') cuidadosError.value = '';
    };

    const loadData = async (productoId: number) => {
        loading.value = true;
        try {
            const data = await ProductoDetalleService.getDetalles(productoId);
            detalleState.value = data;
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar los detalles del producto.', life: 3000 });
        } finally {
            loading.value = false;
        }
    };

    function getFomrData(): FormData {
        const formData = new FormData();
        formData.append('parte_superior', detalleState.value.parte_superior);
        formData.append('parte_inferior', detalleState.value.parte_inferior);
        formData.append('suela', detalleState.value.suela);
        formData.append('cuidados', detalleState.value.cuidados);

        return formData;
    }

    function saveErrors(responseData: any) {
        parteSuperiorError.value = responseData.errors.parte_superior ? responseData.errors.parte_superior[0] : '';
        parteInferiorError.value = responseData.errors.parte_inferior ? responseData.errors.parte_inferior[0] : '';
        suelaError.value = responseData.errors.suela ? responseData.errors.suela[0] : '';
        cuidadosError.value = responseData.errors.cuidados ? responseData.errors.cuidados[0] : '';
    }

    /** Prepara y envía los datos al servicio API */
    const handleSubmit = async () => {
        loading.value = true;
        const formData = getFomrData();

        try {
            const data = await ProductoDetalleService.saveTalla(formData, detalleState.value.id);
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 3000 });
        } catch (error: any) {
            const responseData = error.response?.data;

            if (error.response?.status === 422 && responseData?.errors) {
                saveErrors(responseData);
                toast.add({
                    severity: 'error',
                    summary: 'Error de Validación',
                    detail: 'Por favor, corrige los errores en el formulario.',
                    life: 5000,
                });
            } else {
                const detail = responseData?.message || 'Error desconocido al guardar la talla.';
                toast.add({ severity: 'error', summary: 'Error al guardar', detail, life: 3000 });
            }
        } finally {
            loading.value = false;
        }
    };

    onMounted(() => {
        if (props.productoId) {
            loadData(props.productoId);
        }
    });
</script>

<template>
    <TabPanel value="detalles">
        <form @submit.prevent="handleSubmit" class="card grid grid-cols-12 gap-8">
            <div class="col-span-12 xl:col-span-4">
                <FormField id="cuidaddos" label="Cuidados recomendados" :error="cuidadosError" v-model="detalleState.cuidados">
                    <Textarea
                        id="cuidaddos"
                        class="w-full"
                        size="large"
                        rows="8"
                        v-model="detalleState.cuidados"
                        :invalid="cuidadosError != ''"
                        @input="clearErrores('cuidados')" />
                </FormField>
            </div>
            <div class="col-span-12 xl:col-span-8">
                <FormField
                    id="nombre"
                    label="Parte Superior del calzado"
                    :error="parteSuperiorError"
                    v-model="detalleState.parte_superior">
                    <InputText
                        id="nombre"
                        type="text"
                        v-model="detalleState.parte_superior"
                        :invalid="parteSuperiorError != ''"
                        class="w-full"
                        @input="clearErrores('parte_superior')" />
                </FormField>

                <FormField
                    id="parte_inferior"
                    label="Parte Inferior del calzado"
                    :error="parteInferiorError"
                    v-model="detalleState.parte_inferior">
                    <InputText
                        id="parte_inferior"
                        type="text"
                        v-model="detalleState.parte_inferior"
                        :invalid="parteInferiorError != ''"
                        class="w-full"
                        @input="clearErrores('parte_inferior')" />
                </FormField>

                <FormField id="suela" label="Suela del calzado" :error="suelaError" v-model="detalleState.suela">
                    <InputText
                        id="suela"
                        type="text"
                        v-model="detalleState.suela"
                        :invalid="suelaError != ''"
                        class="w-full"
                        @input="clearErrores('suela')" />
                </FormField>
            </div>

            <div class="col-span-12 xl:col-span-12">
                <div class="flex justify-end mt-4">
                    <Button type="submit" label="Guardar cambios" :loading="loading" severity="primary" icon="pi pi-check" :disabled="loading" />
                </div>
            </div>
        </form>
    </TabPanel>
</template>
