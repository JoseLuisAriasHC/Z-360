<script setup lang="ts">
    import { ref, onMounted } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import { type VarianteImage, VarianteProductoService } from '@admin/services/VarianteProductoService';
    import FileUpload from 'primevue/fileupload';
    import { getParamId } from '@/utils/utils';

    const varainteId = getParamId();
    const storageUrl = import.meta.env.VITE_STORAGE_URL + '/';

    // --- ESTADO ---
    const toast = useToast();
    const loading = ref(true);
    const mostrarModalDeleteConfirmacion = ref(false);
    const idImageActual = ref(-1);
    const existingImages = ref<VarianteImage[]>([]);
    const fileUploadRef = ref<InstanceType<typeof FileUpload> | null>(null);

    /**
     * Carga las imágenes existentes de la variante.
     */
    const loadExistingImages = async (varainteId: number) => {
        loading.value = true;

        try {
            existingImages.value = await VarianteProductoService.getImagesByIdVarainte(varainteId);
        } catch (error) {
            console.error('Error al cargar imágenes existentes:', error);
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar las imágenes.', life: 3000 });
        } finally {
            loading.value = false;
        }
    };

    onMounted(() => {
        if (varainteId.value) {
            loadExistingImages(varainteId.value);
        }
    });

    const deleteExistingImage = async () => {
        try {
            const response = await VarianteProductoService.deleteVariantImage(idImageActual.value);
            existingImages.value = existingImages.value.filter((img) => img.id !== idImageActual.value);
            toast.add({ severity: 'success', summary: 'Éxito', detail: response.message, life: 3000 });
            mostrarModalDeleteConfirmacion.value = false;
        } catch (error) {
            console.error('Error al eliminar imagen:', error);
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo eliminar la imagen.', life: 3000 });
        }
    };

    /**
     * Manejador de subida de archivos personalizado (customUpload).
     * Se invoca cuando el usuario presiona el botón 'Upload' del componente.
     * @param event Contiene la lista de archivos seleccionados.
     */
    const customUploader = async (event: any) => {
        const files = event.files as File[];
        const formData = new FormData();

        files.forEach((file) => {
            formData.append('files[]', file, file.name);
        });

        try {
            if (varainteId.value) {
                const response = await VarianteProductoService.uploadVariantImages(varainteId.value, formData);
                existingImages.value.push(...response.data);

                toast.add({ severity: 'success', summary: 'Éxito', detail: 'Imágenes subidas correctamente.', life: 3000 });
                if (fileUploadRef.value) {
                    fileUploadRef.value.clear();
                }
            }
        } catch (error) {
            console.error('Error al subir imágenes:', error);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al subir imágenes.', life: 3000 });
        }
    };

    const openModalDelete = (idImage: number) => {
        mostrarModalDeleteConfirmacion.value = true;
        idImageActual.value = idImage;
    }
</script>

<template>
    <Toast />
    <Card class="shadow-lg">
        <template #title>Gestión de Imágenes</template>
        <template #content>
            <h3 class="text-base text-gray-500 dark:text-gray-300 font-semibold mb-4 pb-2">Imágenes Actuales</h3>
            <div v-if="loading" class="text-center py-6">Cargando imágenes...</div>
            <div v-else-if="existingImages.length === 0" class="text-center py-6 text-gray-500">No hay imágenes asignadas a esta variante.</div>
            <div v-else class="grid grid-cols-2 lg:grid-cols-9 gap-4 mb-8">
                <div v-for="image in existingImages" :key="image.id" class="relative group">
                    <Image :src="storageUrl + image.path" alt="Imagen de variante" preview class="h-40 object-cover shadow-md" />
                    <div
                        class="h-40 absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
                        <Button icon="pi pi-trash" severity="danger" rounded aria-label="Eliminar" @click="openModalDelete(image.id)" />
                    </div>
                </div>
            </div>

            <h3 class="text-base text-gray-500 dark:text-gray-300 font-semibold mb-4" sev>Subir Nuevas Imágenes</h3>
            <FileUpload
                ref="fileUploadRef"
                name="files[]"
                :multiple="true"
                accept="image/*"
                :maxFileSize="10000000"
                :customUpload="true"
                @uploader="customUploader"
                :disabled="!varainteId">
                <template #empty>
                    <p>Arrastra y suelta imágenes aquí para subirlas.</p>
                </template>
            </FileUpload>
        </template>
    </Card>

    <Dialog header="Borrar imagen" v-model:visible="mostrarModalDeleteConfirmacion" :style="{ width: '350px' }" :modal="true">
        <div class="flex items-center justify-center">
            <i class="pi pi-exclamation-triangle mr-4" style="font-size: 2rem" />
            <span>¿Estas seguro de que quieres borrar la imagen seleciona?</span>
        </div>
        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" @click="mostrarModalDeleteConfirmacion = false" text severity="secondary" />
            <Button label="Borrar imagen" icon="pi pi-check" @click="deleteExistingImage()" severity="danger" outlined autofocus />
        </template>
    </Dialog>
</template>
