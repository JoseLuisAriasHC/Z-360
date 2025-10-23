<script setup lang="ts">
    import { ref, onMounted, computed } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { useToast } from 'primevue/usetoast';
    import { type Etiqueta, EtiquetaService } from '@admin/services/EtiquetaService';
    import FormField from '@admin/components/FormField.vue';

    // --- PROPS Y HOOKS ---
    const route = useRoute();
    const router = useRouter();
    const toast = useToast();

    const etiquetaId = computed<number | null>(() => {
        const idParam = route.params.id;
        return Array.isArray(idParam) ? null : idParam ? parseInt(idParam) : null;
    });

    const isEditMode = computed(() => etiquetaId.value !== null);

    interface EtiquetaFormState extends Omit<Etiqueta, 'id'> {}
    const etiquetaState = ref<EtiquetaFormState & { id?: number }>({
        id: etiquetaId.value || undefined,
        nombre: ''
    });

    // Referencias para manejar errores de validación del backend (422)
    const nombreError = ref('');
    const loading = ref(false);

    const clearErrores = (field: keyof EtiquetaFormState) => {
        if (field === 'nombre') nombreError.value = '';
    };

    const loadData = async (id: number) => {
        loading.value = true;
        try {
            const data = await EtiquetaService.getEtiqueta(id);
            etiquetaState.value.id = data.id;
            etiquetaState.value.nombre = data.nombre;
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar la etiqueta.', life: 3000 });
            router.push({ name: 'admin-etiquetas' });
        } finally {
            loading.value = false;
        }
    };

    /** Prepara y envía los datos al servicio API */
    const handleSubmit = async () => {
        loading.value = true;
        nombreError.value = '';

        const formData = new FormData();
        formData.append('nombre', etiquetaState.value.nombre);

        try {
            const data = await EtiquetaService.saveEtiqueta(formData, etiquetaState.value.id);
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 3000 });
            router.push({ name: 'admin-etiquetas' });
        } catch (error: any) {
            const responseData = error.response?.data;

            if (error.response?.status === 422 && responseData?.errors) {
                nombreError.value = responseData.errors.nombre ? responseData.errors.nombre[0] : '';
                toast.add({
                    severity: 'error',
                    summary: 'Error de Validación',
                    detail: 'Por favor, corrige los errores en el formulario.',
                    life: 5000,
                });
            } else {
                const detail = responseData?.message || 'Error desconocido al guardar la Etiqueta.';
                toast.add({ severity: 'error', summary: 'Error al guardar', detail, life: 3000 });
            }
        } finally {
            loading.value = false;
        }
    };

    const goBack = () => {
        router.push({ name: 'admin-etiquetas' });
    };

    onMounted(() => {
        if (isEditMode.value && etiquetaId.value) {
            loadData(etiquetaId.value);
        }
    });
</script>

<template>
    <div class="flex flex-col md:flex-row">
        <div class="w-full">
            <form @submit.prevent="handleSubmit" class="card flex flex-col gap-4">
                <div class="font-semibold text-xl flex items-center justify-between">
                    <h5 class="text-xl font-bold">
                        {{ isEditMode ? 'Editar Etiqueta' : 'Crear Nueva Etiqueta' }}
                    </h5>
                    <Button icon="pi pi-arrow-left" label="Volver" severity="secondary" @click="goBack" :fluid="false" />
                </div>
                <div class="flex flex-col gap-2">
                    <FormField id="nombre" label="Nombre" :error="nombreError">
                        <InputText
                            id="nombre"
                            type="text"
                            v-model="etiquetaState.nombre"
                            :invalid="nombreError != ''"
                            class="w-full"
                            @input="clearErrores('nombre')" />
                    </FormField>
                </div>
                <div class="col-span-12">
                    <div class="flex justify-end mt-4">
                        <Button
                            type="submit"
                            :label="isEditMode ? 'Guardar Cambios' : 'Crear Etiqueta'"
                            :loading="loading"
                            severity="primary"
                            icon="pi pi-check"
                            :disabled="loading" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
