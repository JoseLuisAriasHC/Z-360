<script setup lang="ts">
    import { ref, onMounted, computed, inject, type Ref } from 'vue';
    import { useRouter } from 'vue-router';
    import { useToast } from 'primevue/usetoast';
    import { type TiposUso, TiposUsoService } from '@admin/services/TiposUsoService';
    import FormField from '@admin/components/FormField.vue';

    const router = useRouter();
    const toast = useToast();

    const props = defineProps<{
        productoId: number | null;
    }>();

    const productoValido = inject<Ref<boolean>>('productoValido', ref(true));
    const loading = ref(false);
    const tipoUsoError = ref('');

    const tiposUsoDisponibles = ref<TiposUso[]>([]);
    const tiposUsoSeleccionadas = ref<TiposUso[]>([]);
    const TiposUsoIdsSelecionados = computed<number[]>(() => {
        if (!tiposUsoSeleccionadas.value) {
            return [];
        }
        return tiposUsoSeleccionadas.value.map((uso) => uso.id);
    });

    const loadTiposUsoData = async () => {
        loading.value = true;

        try {
            const data = await TiposUsoService.getTiposUsos();
            tiposUsoDisponibles.value = data;
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al cargar los tipos de uso.', life: 3000 });
            router.push({ name: 'admin-not-found' });
        } finally {
            loading.value = false;
        }
    };

    const loadData = async (id: number) => {
        loading.value = true;

        try {
            const data = await TiposUsoService.getTipoUsoByIdProducto(id);
            tiposUsoSeleccionadas.value = data;
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar las etiquetas del producto', life: 3000 });
            router.push({ name: 'admin-not-found' });
        } finally {
            loading.value = false;
        }
    };

    const handleSubmit = async () => {
        loading.value = true;

        const payload = {
            usages: TiposUsoIdsSelecionados.value,
        };

        try {
            if (props.productoId) {
                const data = await TiposUsoService.saveEtiquetasDelProducto(payload, props.productoId);
                toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 3000 });
            }
        } catch (error: any) {
            const responseData = error.response?.data;

            if (error.response?.status === 422 && responseData?.errors) {
                tipoUsoError.value = responseData.errors.etiquetas ? responseData.errors.etiquetas[0] : '';
                toast.add({
                    severity: 'error',
                    summary: 'Error de Validación',
                    detail: 'Por favor, corrige los errores en el formulario.',
                    life: 5000,
                });
            } else {
                const detail = responseData?.message || 'Error desconocido al guardar los tipos de uso del producto.';
                toast.add({ severity: 'error', summary: 'Error al guardar', detail, life: 3000 });
            }
        } finally {
            loading.value = false;
        }
    };

    onMounted(() => {
        loadTiposUsoData();
        if (props.productoId && productoValido) {
            loadData(props.productoId);
        }
    });
</script>

<template>
    <div class="p-8 border border-surface-200 dark:border-surface-700 p-8 rounded-lg drop-shadow-md">
        <div class="font-semibold text-xl mb-4">Tipos de uso del producto</div>
        <form @submit.prevent="handleSubmit" class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <FormField id="etiquetas" label="" :error="tipoUsoError">
                    <MultiSelect
                        id="etiquetas"
                        v-model="tiposUsoSeleccionadas"
                        :options="tiposUsoDisponibles"
                        filter
                        showClear
                        optionLabel="nombre"
                        placeholder="Selecciona el tipo de uso"
                        class="w-full"
                        @input="tipoUsoError = ''">
                        <template #value="slotProps">
                            <div class="" v-for="option of slotProps.value" :key="option">
                                <Tag :value="option.nombre" severity="primary" class="m-2" />
                            </div>
                            <template v-if="!slotProps.value || slotProps.value.length === 0">
                                <div class="p-1">Selecciona el tipo de uso</div>
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
