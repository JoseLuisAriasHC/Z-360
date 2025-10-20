<script setup lang="ts">
    import { ref, onMounted, computed } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { useToast } from 'primevue/usetoast';
    import { type Marca, MarcaService } from '@admin/services/MarcaService';
    import InputText from 'primevue/inputtext';
    import InputNumber from 'primevue/inputnumber';
    import Button from 'primevue/button';
    import FormField from '@admin/components/FormField.vue';

    // --- PROPS Y HOOKS ---
    const route = useRoute();
    const router = useRouter();
    const toast = useToast();

    // Determinar el ID de la marca (será null para creación)
    const marcaId = computed<number | null>(() => {
        const idParam = route.params.id;
        return Array.isArray(idParam) ? null : idParam ? parseInt(idParam) : null;
    });

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

    // --- MANEJO DE ERRORES ---

    const clearNombreError = () => {
        if (nombreError.value) nombreError.value = '';
    };

    const clearTallaOffsetError = () => {
        if (tallaOffsetError.value) tallaOffsetError.value = '';
    };

    const clearLogoError = () => {
        if (logoError.value) logoError.value = '';
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
                currentLogoUrl.value = data.logo.startsWith('http') ? data.logo : `/storage/${data.logo}`;
            }
        } catch (error) {
            console.error('Error al cargar la marca:', error);
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar la marca.', life: 3000 });
            router.push({ name: 'admin-marcas' });
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
            clearLogoError();
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
        
        clearLogoError();
    };

    /** Prepara y envía los datos al servicio API */
    const handleSubmit = async () => {
        loading.value = true;

        // Limpiar errores previos
        nombreError.value = '';
        tallaOffsetError.value = '';
        logoError.value = '';

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
                console.error('Error al enviar formulario:', error);
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

        <form @submit.prevent="handleSubmit" class="card flex flex-col gap-4 pt-0">
            <!-- Campo Nombre -->
            <FormField id="nombre" label="Nombre" :error="nombreError">
                <InputText 
                    id="nombre" 
                    type="text" 
                    v-model="marcaState.nombre" 
                    :class="{ 'p-invalid': nombreError }" 
                    @input="clearNombreError" 
                />
            </FormField>

            <!-- Campo Offset de Talla -->
            <FormField id="talla_offset" label="Offset de Talla" :error="tallaOffsetError">
                <InputNumber
                    id="talla_offset"
                    v-model="marcaState.talla_offset"
                    mode="decimal"
                    :minFractionDigits="1"
                    :maxFractionDigits="1"
                    :class="{ 'p-invalid': tallaOffsetError }"
                    @update:modelValue="clearTallaOffsetError" 
                />
            </FormField>

            <!-- Campo Logo con Input File Nativo -->
            <FormField id="logo" label="Logo" :error="logoError">
                <!-- Previsualización del logo actual -->
                <div v-if="currentLogoUrl" class="mb-4">
                    <p class="text-sm text-surface-600 dark:text-surface-400 mb-2">Logo Actual:</p>
                    <div class="relative inline-block">
                        <img
                            :src="currentLogoUrl"
                            alt="Logo"
                            class="max-h-32 max-w-full object-contain border border-surface-200 dark:border-surface-700 p-2 rounded-lg" 
                        />
                        <Button
                            v-if="marcaState.logoFile"
                            icon="pi pi-times"
                            severity="danger"
                            rounded
                            text
                            class="absolute top-1 right-1"
                            @click="removeLogo"
                            v-tooltip.top="'Remover logo'"
                        />
                    </div>
                </div>

                <!-- Input file nativo estilizado -->
                <div class="flex flex-col gap-2">
                    <input
                        ref="fileInputRef"
                        type="file"
                        accept="image/*"
                        @change="onFileChange"
                        class="hidden"
                        id="logo-input"
                    />
                    <label
                        for="logo-input"
                        class="inline-flex items-center justify-center px-4 py-2 border border-surface-300 dark:border-surface-600 rounded-md cursor-pointer hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors"
                        :class="{ 'border-red-500': logoError }"
                    >
                        <i class="pi pi-upload mr-2"></i>
                        <span>{{ marcaState.logoFile ? marcaState.logoFile.name : (currentLogoUrl ? 'Cambiar Logo' : 'Seleccionar Logo') }}</span>
                    </label>
                    <small class="text-surface-600 dark:text-surface-400">
                        Formatos aceptados: JPG, PNG, GIF, SVG, WEBP (máx. 2MB)
                    </small>
                </div>
            </FormField>

            <!-- Botón Submit -->
            <div class="flex justify-end mt-4">
                <Button
                    type="submit"
                    :label="isEditMode ? 'Guardar Cambios' : 'Crear Marca'"
                    :loading="loading"
                    severity="primary"
                    icon="pi pi-check"
                    :disabled="loading" 
                />
            </div>
        </form>
    </div>
</template>

<style scoped>
/* Estilos adicionales si son necesarios */
</style>