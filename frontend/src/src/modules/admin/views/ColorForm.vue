<script setup lang="ts">
    import { ref, onMounted, computed } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { useToast } from 'primevue/usetoast';
    import { type Color, ColorService } from '@admin/services/ColorService';
    import FormField from '@admin/components/FormField.vue';

    // --- PROPS Y HOOKS ---
    const route = useRoute();
    const router = useRouter();
    const toast = useToast();

    const colorId = computed<number | null>(() => {
        const idParam = route.params.id;
        return Array.isArray(idParam) ? null : idParam ? parseInt(idParam) : null;
    });

    const isEditMode = computed(() => colorId.value !== null);
    const documentStyle = getComputedStyle(document.documentElement);

    interface ColorFormState extends Omit<Color, 'id'> {}
    const colorState = ref<ColorFormState & { id?: number }>({
        id: colorId.value || undefined,
        nombre: '',
        codigo_hex: documentStyle.getPropertyValue('--p-primary-500'),
    });

    // Referencias para manejar errores de validación del backend (422)
    const nombreError = ref('');
    const codigo_hexError = ref('');
    const loading = ref(false);

    const clearnombreError = () => {
        if (nombreError.value) nombreError.value = '';
    };

    const clearCodigoHex = () => {
        if (codigo_hexError.value) codigo_hexError.value = '';
    };

    const loadColorData = async (id: number) => {
        loading.value = true;
        try {
            const data = await ColorService.getColor(id);
            colorState.value.id = data.id;
            colorState.value.nombre = data.nombre;
            colorState.value.codigo_hex = data.codigo_hex;
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar el color.', life: 3000 });
            router.push({ name: 'admin-colores' });
        } finally {
            loading.value = false;
        }
    };

    /** Prepara y envía los datos al servicio API */
    const handleSubmit = async () => {
        loading.value = true;
        clearnombreError();
        clearCodigoHex();

        const formData = new FormData();
        formData.append('nombre', colorState.value.nombre);
        formData.append('codigo_hex', colorState.value.codigo_hex);

        try {
            const data = await ColorService.saveColor(formData, colorState.value.id);
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 3000 });
            router.push({ name: 'admin-colores' });
        } catch (error: any) {
            const responseData = error.response?.data;

            if (error.response?.status === 422 && responseData?.errors) {
                nombreError.value = responseData.errors.nombre ? responseData.errors.nombre[0] : '';
                codigo_hexError.value = responseData.errors.codigo_hexError ? responseData.errors.codigo_hex[0] : '';
                toast.add({
                    severity: 'error',
                    summary: 'Error de Validación',
                    detail: 'Por favor, corrige los errores en el formulario.',
                    life: 5000,
                });
            } else {
                const detail = responseData?.message || 'Error desconocido al guardar el color.';
                toast.add({ severity: 'error', summary: 'Error al guardar', detail, life: 3000 });
                console.error('Error al enviar formulario:', error);
            }
        } finally {
            loading.value = false;
        }
    };

    const goBack = () => {
        router.push({ name: 'admin-colores' });
    };

    onMounted(() => {
        if (isEditMode.value && colorId.value) {
            loadColorData(colorId.value);
        }
    });
</script>

<template>
    <div class="flex flex-col md:flex-row">
        <div class="w-full">
            <form @submit.prevent="handleSubmit" class="card flex flex-col gap-4">
                <div class="font-semibold text-xl flex items-center justify-between">
                    <h5 class="text-xl font-bold">
                        {{ isEditMode ? 'Editar Color' : 'Crear Nueva Color' }}
                    </h5>
                    <Button icon="pi pi-arrow-left" label="Volver" severity="secondary" @click="goBack" :fluid="false" />
                </div>
                <div class="flex flex-col gap-2">
                    <FormField id="nombre" label="Nombre" :error="nombreError">
                        <InputText
                            id="nombre"
                            type="text"
                            v-model="colorState.nombre"
                            :class="{ 'p-invalid': nombreError }"
                            class="w-full"
                            @input="clearnombreError" />
                    </FormField>
                </div>
                <div class="flex flex-col gap-2">
                    <FormField id="codigo_hex" label="Codigo hexadecimal" :error="codigo_hexError">
                        <ColorPicker
                            style="width: 2rem"
                            v-model="colorState.codigo_hex"
                            :class="{ 'p-invalid': codigo_hexError }"
                            @input="clearCodigoHex" />
                    </FormField>
                </div>
                <div class="col-span-12">
                    <!-- Botón Submit -->
                    <div class="flex justify-end mt-4">
                        <Button
                            type="submit"
                            :label="isEditMode ? 'Guardar Cambios' : 'Crear Marca'"
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
