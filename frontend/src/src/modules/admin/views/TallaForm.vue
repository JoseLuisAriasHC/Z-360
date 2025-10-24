<script setup lang="ts">
    import { ref, onMounted, computed } from 'vue';
    import { useRouter } from 'vue-router';
    import { useToast } from 'primevue/usetoast';
    import { type Talla, TallaService } from '@admin/services/TallaService';
    import FormField from '@admin/components/FormField.vue';
    import { getParamId } from '@/utils/utils';

    // --- PROPS Y HOOKS ---
    const router = useRouter();
    const toast = useToast();

    const tallaId = getParamId();
    const isEditMode = computed(() => tallaId.value !== null);

    interface TallaFormState extends Omit<Talla, 'id' | 'numero'> {
        numero: string;
    }
    const tallaState = ref<TallaFormState & { id?: number }>({
        id: tallaId.value || undefined,
        numero: '0.0',
    });

    // Referencias para manejar errores de validación del backend (422)
    const numeroError = ref('');
    const loading = ref(false);

    const clearErrores = (field: keyof TallaFormState) => {
        if (field === 'numero') numeroError.value = '';
    };

    const loadData = async (id: number) => {
        loading.value = true;
        try {
            const data = await TallaService.getTalla(id);
            tallaState.value.id = data.id;
            tallaState.value.numero = data.numero.toString();
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar la talla.', life: 3000 });
            router.push({ name: 'admin-not-found' });
        } finally {
            loading.value = false;
        }
    };

    /** Prepara y envía los datos al servicio API */
    const handleSubmit = async () => {
        loading.value = true;
        numeroError.value = '';

        const formData = new FormData();
        formData.append('numero', tallaState.value.numero.toString());

        try {
            const data = await TallaService.saveTalla(formData, tallaState.value.id);
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 3000 });
            router.push({ name: 'admin-tallas' });
        } catch (error: any) {
            const responseData = error.response?.data;

            if (error.response?.status === 422 && responseData?.errors) {
                numeroError.value = responseData.errors.numero ? responseData.errors.numero[0] : '';
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

    const goBack = () => {
        router.push({ name: 'admin-tallas' });
    };

    onMounted(() => {
        if (isEditMode.value && tallaId.value) {
            loadData(tallaId.value);
        }
    });
</script>

<template>
    <div class="flex flex-col md:flex-row">
        <div class="w-full">
            <form @submit.prevent="handleSubmit" class="card flex flex-col gap-4">
                <div class="font-semibold text-xl flex items-center justify-between">
                    <h5 class="text-xl font-bold">
                        {{ isEditMode ? 'Editar Talla' : 'Crear Nueva Talla' }}
                    </h5>
                    <Button icon="pi pi-arrow-left" label="Volver" severity="secondary" @click="goBack" :fluid="false" />
                </div>
                <div class="flex flex-col gap-2">
                    <FormField id="numero" label="Numero" :error="numeroError">
                        <InputText
                            id="numero"
                            type="number"
                            v-model="tallaState.numero"
                            :invalid="numeroError != ''"
                            class="w-full"
                            @input="clearErrores('numero')" />
                    </FormField>
                </div>
                <div class="col-span-12">
                    <div class="flex justify-end mt-4">
                        <Button
                            type="submit"
                            :label="isEditMode ? 'Guardar Cambios' : 'Crear Talla'"
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
