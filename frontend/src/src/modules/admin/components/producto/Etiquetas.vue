<script setup lang="ts">
    import { ref, onMounted, computed } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import { type Etiqueta, EtiquetaService } from '@admin/services/EtiquetaService';
    import FormField from '@admin/components/FormField.vue';

    const toast = useToast();

    const props = defineProps<{
        productoId: number | null;
    }>();

    const loading = ref(false);
    const etiquetasError = ref('');

    const etiquetasDisponibles = ref<Etiqueta[]>([]);
    const etiquetasSeleccionadas = ref<Etiqueta[]>([]);
    const selectedEtiquetaIds = computed<number[]>(() => {
        if (!etiquetasSeleccionadas.value) {
            return [];
        }
        return etiquetasSeleccionadas.value.map((etiqueta) => etiqueta.id);
    });

    const loadEtiquetasData = async () => {
        loading.value = true;

        try {
            const data = await EtiquetaService.getEtiquetas();
            etiquetasDisponibles.value = data;
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al cargar las etiquetas.', life: 3000 });
        } finally {
            loading.value = false;
        }
    };

    const loadData = async (id: number) => {
        loading.value = true;

        try {
            const data = await EtiquetaService.getEtiquetasByIdProduct(id);
            etiquetasSeleccionadas.value = data;
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar las etiquetas del producto', life: 3000 });
        } finally {
            loading.value = false;
        }
    };

    const handleSubmit = async () => {
        debugger;
        loading.value = true;
        const idsToSend = selectedEtiquetaIds.value;
        
        const payload = {
            etiquetas: selectedEtiquetaIds.value,
        };

        try {
            if (props.productoId) {
                const data = await EtiquetaService.saveEtiquetasDelProducto(payload, props.productoId);
                toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 3000 });
            }
        } catch (error: any) {
            const responseData = error.response?.data;

            if (error.response?.status === 422 && responseData?.errors) {
                etiquetasError.value = responseData.errors.etiquetas ? responseData.errors.etiquetas[0] : '';
                toast.add({
                    severity: 'error',
                    summary: 'Error de Validación',
                    detail: 'Por favor, corrige los errores en el formulario.',
                    life: 5000,
                });
            } else {
                const detail = responseData?.message || 'Error desconocido al guardar las etiquetas del producto.';
                toast.add({ severity: 'error', summary: 'Error al guardar', detail, life: 3000 });
            }
        } finally {
            loading.value = false;
        }
    };

    onMounted(() => {
        loadEtiquetasData();
        if (props.productoId) {
            loadData(props.productoId);
        }
    });
</script>

<template>
    <div class="card">
        <div class="font-semibold text-xl mb-4">Etiquetas del producto</div>
        <form @submit.prevent="handleSubmit" class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <FormField id="etiquetas" label="" :error="etiquetasError">
                    <MultiSelect
                        id="etiquetas"
                        v-model="etiquetasSeleccionadas"
                        :options="etiquetasDisponibles"
                        filter
                        showClear
                        optionLabel="nombre"
                        placeholder="Selecciona Etiquetas"
                        class="w-full"
                        @input="etiquetasError = ''">
                        <template #value="slotProps">
                            <div class="" v-for="option of slotProps.value" :key="option">
                                <Tag :value="option.nombre" severity="primary" class="m-2" />
                            </div>
                            <template v-if="!slotProps.value || slotProps.value.length === 0">
                                <div class="p-1">Selecciona Etiquetas</div>
                            </template>
                        </template>
                    </MultiSelect>
                </FormField>
            </div>
            <div>
                <div class="flex justify-end">
                    <Button type="submit" label="Guardar Cambios" :loading="loading" severity="primary" icon="pi pi-check" />
                </div>
            </div>
        </form>
    </div>
</template>
