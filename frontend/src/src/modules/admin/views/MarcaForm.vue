<script setup lang="ts">
    import { ref, onMounted, computed } from 'vue';
    import { useRouter } from 'vue-router';
    import { useToast } from 'primevue/usetoast';
    import { type Marca, MarcaService } from '@admin/services/MarcaService';
    import FormField from '@admin/components/FormField.vue';
    import { getParamId } from '@/utils/utils';

    // --- PROPS Y HOOKS ---
    const router = useRouter();
    const toast = useToast();

    const marcaId = getParamId();
    const isEditMode = computed(() => marcaId.value !== null);

    interface MarcaFormState extends Omit<Marca, 'id' | 'logo'> {
        logoFile: File | null;
    }

    const marcaState = ref<MarcaFormState & { id?: number }>({
        id: marcaId.value || undefined,
        nombre: '',
        talla_offset: 0.0,
        logoFile: null,
    });

    // Referencia para la imagen de logo actual (para edición)
    const currentLogoUrl = ref<string | null>(null);

    // Referencias para manejar errores de validación del backend (422)
    const nombreError = ref('');
    const tallaOffsetError = ref('');
    const logoError = ref('');

    // Referencia para el input file nativo
    const fileInputRef = ref<HTMLInputElement | null>(null);
    const loading = ref(false);

    const clearErrores = (field: keyof MarcaFormState) => {
        if (field === 'nombre') nombreError.value = '';
        if (field === 'talla_offset') tallaOffsetError.value = '';
        if (field === 'logoFile') logoError.value = '';
    };

    // --- DATOS Y SUBIDA ---

    const loadMarcaData = async (id: number) => {
        loading.value = true;
        try {
            const data = await MarcaService.getMarca(id);
            marcaState.value.id = data.id;
            marcaState.value.nombre = data.nombre;
            marcaState.value.talla_offset = parseFloat(data.talla_offset as any);

            if (data.logo) {
                currentLogoUrl.value = `${import.meta.env.VITE_STORAGE_URL}/${data.logo}`;
            }
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar la marca.', life: 3000 });
            router.push({ name: 'admin-not-found' });
        } finally {
            loading.value = false;
        }
    };

    /** Maneja la selección del archivo desde el input nativo */
    const onFileChange = (event: Event) => {
        const target = event.target as HTMLInputElement;
        const file = target.files?.[0];

        if (file) {
            // Validar tipo de archivo
            if (!file.type.startsWith('image/')) {
                logoError.value = 'El archivo debe ser una imagen.';
                marcaState.value.logoFile = null;
                currentLogoUrl.value = null;
                return;
            }

            // Validar tamaño (máx 2MB)
            if (file.size > 2048 * 1024) {
                logoError.value = 'La imagen no puede pesar más de 2MB.';
                marcaState.value.logoFile = null;
                currentLogoUrl.value = null;
                return;
            }

            marcaState.value.logoFile = file;
            // Crear una URL temporal para previsualizar la nueva imagen
            currentLogoUrl.value = URL.createObjectURL(file);
            clearErrores('logoFile');
        }
    };

    /** Remueve el logo seleccionado */
    const removeLogo = () => {
        marcaState.value.logoFile = null;
        currentLogoUrl.value = null;

        // Limpiar el input file
        if (fileInputRef.value) {
            fileInputRef.value.value = '';
        }

        clearErrores('logoFile');
    };

    /** Prepara y envía los datos al servicio API */
    const handleSubmit = async () => {
        loading.value = true;

        // Crear FormData para manejar la subida de archivos
        const formData = new FormData();
        formData.append('nombre', marcaState.value.nombre);
        formData.append('talla_offset', marcaState.value.talla_offset !== null ? marcaState.value.talla_offset.toString() : '0');

        if (marcaState.value.logoFile) {
            formData.append('logo', marcaState.value.logoFile, marcaState.value.logoFile.name);
        }

        try {
            const data = await MarcaService.saveMarca(formData, marcaState.value.id);
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 3000 });
            router.push({ name: 'admin-marcas' });
        } catch (error: any) {
            const responseData = error.response?.data;

            if (error.response?.status === 422 && responseData?.errors) {
                nombreError.value = responseData.errors.nombre ? responseData.errors.nombre[0] : '';
                tallaOffsetError.value = responseData.errors.talla_offset ? responseData.errors.talla_offset[0] : '';
                logoError.value = responseData.errors.logo ? responseData.errors.logo[0] : '';

                toast.add({
                    severity: 'error',
                    summary: 'Error de Validación',
                    detail: 'Por favor, corrige los errores en el formulario.',
                    life: 5000,
                });
            } else {
                const detail = responseData?.message || 'Error desconocido al guardar la marca.';
                toast.add({ severity: 'error', summary: 'Error al guardar', detail, life: 3000 });
            }
        } finally {
            loading.value = false;
        }
    };

    const goBack = () => {
        router.push({ name: 'admin-marcas' });
    };

    // --- CICLO DE VIDA ---
    onMounted(() => {
        if (isEditMode.value && marcaId.value) {
            loadMarcaData(marcaId.value);
        }
    });
</script>

<template>
    <div class="card flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h5 class="text-xl font-bold">
                {{ isEditMode ? 'Editar Marca' : 'Crear Nueva Marca' }}
            </h5>
            <Button icon="pi pi-arrow-left" label="Volver" severity="secondary" @click="goBack" :fluid="false" />
        </div>

        <form @submit.prevent="handleSubmit" class="grid grid-cols-12 gap-8">
            <div class="col-span-12 xl:col-span-8">
                <div class="flex flex-col gap-4 mb-4">
                    <FormField id="nombre" label="Nombre" :error="nombreError">
                        <InputText
                            id="nombre"
                            type="text"
                            v-model="marcaState.nombre"
                            :invalid="nombreError != ''"
                            class="w-full"
                            @input="clearErrores('nombre')" />
                    </FormField>
                </div>
                <div class="flex flex-col gap-4">
                    <FormField id="talla_offset" label="Offset de Talla" :error="tallaOffsetError">
                        <InputNumber
                            id="talla_offset"
                            v-model="marcaState.talla_offset"
                            mode="decimal"
                            :minFractionDigits="1"
                            :maxFractionDigits="1"
                            :invalid="tallaOffsetError != ''"
                            class="w-full"
                            @input="clearErrores('talla_offset')" />
                    </FormField>
                </div>
            </div>

            <div class="col-span-12 xl:col-span-4">
                <FormField id="logo" label="Logo" :error="logoError">
                    <div class="flex items-start gap-2 mb-4">
                        <div class="flex flex-col gap-2 flex-grow">
                            <input ref="fileInputRef" type="file" accept="image/*" @change="onFileChange" class="hidden" id="logo-input" />
                            <label
                                for="logo-input"
                                class="w-full text-center inline-flex items-center justify-center px-4 py-2 border border-surface-300 dark:border-surface-600 rounded-md cursor-pointer hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors"
                                :class="{ 'border-red-500': logoError }">
                                <i class="pi pi-upload mr-2"></i>
                                <span>
                                    {{ marcaState.logoFile ? marcaState.logoFile.name : currentLogoUrl ? 'Cambiar Logo' : 'Seleccionar Logo' }}
                                </span>
                            </label>
                            <small class="text-surface-600 dark:text-surface-400">
                                Formatos aceptados: JPG, PNG, GIF, SVG, WEBP (recomendado: 2MB)
                            </small>
                        </div>

                        <Button
                            v-if="currentLogoUrl"
                            icon="pi pi-trash"
                            severity="danger"
                            rounded
                            outlined
                            class="h-[42px] w-[42px] flex-shrink-0"
                            @click="removeLogo"
                            v-tooltip.top="'Remover logo actual'" />
                    </div>

                    <div v-if="currentLogoUrl" class="w-full">
                        <div class="inline-block w-full">
                            <img
                                :src="currentLogoUrl"
                                alt="Logo"
                                class="w-full max-w-full h-auto object-contain border border-surface-200 dark:border-surface-700 p-4 rounded-lg" />
                        </div>
                    </div>
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
</template>
