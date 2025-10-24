<script setup lang="ts">
    import { ref, onMounted, computed } from 'vue';
    import { useRouter } from 'vue-router';
    import { useToast } from 'primevue/usetoast';
    import { type Producto, ProductoService } from '@admin/services/ProductoService';
    import FormField from '@admin/components/FormField.vue';
    import { MarcaService, type Marca } from '@admin/services/MarcaService';
    import noImageSvg from '@/assets/img/no-image.svg';
    import { ALTURA_SUELA_VALORES, CIERRE_VALORES, GENEROS_VALORES, TIPOS_VALORES } from '@/constants/productos';

    // --- PROPS Y HOOKS ---
    const router = useRouter();
    const toast = useToast();

    const props = defineProps<{
        productoId: number | null;
        isEditMode: boolean;
    }>();

    interface ProductoFormState extends Omit<Producto, 'id'> {}
    const productoState = ref<ProductoFormState & { id?: number }>({
        id: props.productoId || undefined,
        nombre: '0.0',
        marca_id: 0,
        tipo: 'urbanas',
        descripcion: '',
        cierre: 'Cordones',
        altura_suela: 'media',
        plantilla: '',
        genero: 'unisex',
    });

    const marcas = ref<Marca[]>([]);
    const loading = ref(false);
    const currentLogoUrl = computed<string>(() => {
        const marcaId = productoState.value.marca_id;
        const currentMarca = marcas.value.find((m) => m.id === marcaId);

        if (currentMarca && currentMarca.logo) {
            return `${import.meta.env.VITE_STORAGE_URL}/${currentMarca.logo}`;
        }
        return noImageSvg;
    });
    const nombreError = ref('');
    const marcaIdError = ref('');
    const tipoError = ref('');
    const descripcionError = ref('');
    const cierreError = ref('');
    const alturaSuelaError = ref('');
    const plantillaError = ref('');
    const generoError = ref('');

    const clearErrores = (field: keyof ProductoFormState) => {
        if (field === 'nombre') nombreError.value = '';
        if (field === 'marca_id') marcaIdError.value = '';
        if (field === 'tipo') tipoError.value = '';
        if (field === 'descripcion') descripcionError.value = '';
        if (field === 'cierre') cierreError.value = '';
        if (field === 'altura_suela') alturaSuelaError.value = '';
        if (field === 'plantilla') plantillaError.value = '';
        if (field === 'genero') generoError.value = '';
    };

    const loadData = async (id: number) => {
        loading.value = true;
        try {
            const data = await ProductoService.getProducto(id);
            productoState.value = data;
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar la talla.', life: 3000 });
            router.push({ name: 'admin-tallas' });
        } finally {
            loading.value = false;
        }
    };

    const loadMarcaData = async () => {
        try {
            const data = await MarcaService.getMarcas();
            marcas.value = data;
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al cargar las marcas.', life: 3000 });
        } finally {
            // isLoading.value = false;
        }
    };

    function getFomrData(): FormData {
        const formData = new FormData();
        formData.append('nombre', productoState.value.nombre);
        formData.append('marca_id', productoState.value.marca_id.toString());
        formData.append('tipo', productoState.value.tipo);
        formData.append('descripcion', productoState.value.descripcion);
        formData.append('cierre', productoState.value.cierre);
        formData.append('altura_suela', productoState.value.altura_suela);
        formData.append('plantilla', productoState.value.plantilla);
        formData.append('genero', productoState.value.genero);

        return formData;
    }

    function saveErrors(responseData: any) {
        nombreError.value = responseData.errors.nombre ? responseData.errors.nombre[0] : '';
        marcaIdError.value = responseData.errors.marca_id ? responseData.errors.marca_id[0] : '';
        tipoError.value = responseData.errors.tipo ? responseData.errors.tipo[0] : '';
        descripcionError.value = responseData.errors.descripcion ? responseData.errors.descripcion[0] : '';
        alturaSuelaError.value = responseData.errors.altura_suela ? responseData.errors.altura_suela[0] : '';
        plantillaError.value = responseData.errors.plantilla ? responseData.errors.plantilla[0] : '';
        generoError.value = responseData.errors.genero ? responseData.errors.genero[0] : '';
    }

    /** Prepara y envía los datos al servicio API */
    const handleSubmit = async () => {
        loading.value = true;
        const formData = getFomrData();

        try {
            const data = await ProductoService.saveProducto(formData, productoState.value.id);
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 3000 });
        } catch (error: any) {
            const responseData = error.response?.data;
            saveErrors(responseData);

            if (error.response?.status === 422 && responseData?.errors) {
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

    const handleImageError = (event: Event) => {
        const imgElement = event.target as HTMLImageElement;
        imgElement.onerror = null;
        imgElement.src = noImageSvg;
    };

    onMounted(() => {
        loadMarcaData();
        if (props.isEditMode && props.productoId) {
            loadData(props.productoId);
        }
    });
</script>

<template>
    <TabPanel value="general">
        <form @submit.prevent="handleSubmit" class="card grid grid-cols-12 gap-8">
            <div class="col-span-12 xl:col-span-4">
                <FormField id="marca" label="Marca" :error="marcaIdError">
                    <Select
                        id="marca"
                        v-model="productoState.marca_id"
                        :options="marcas"
                        placeholder="Seleccionar Marca"
                        showClear
                        class="w-full"
                        optionLabel="nombre"
                        optionValue="id"
                        @input="clearErrores('marca_id')" />
                </FormField>
                <img
                    :src="currentLogoUrl"
                    alt="Logo"
                    class="w-full max-w-full h-auto object-contain border border-surface-200 dark:border-surface-700 p-8 rounded-lg"
                    @error="handleImageError" />
            </div>
            <div class="col-span-12 xl:col-span-8">
                <FormField id="nombre" label="Nombre" :error="nombreError" v-model="productoState.nombre">
                    <InputText
                        id="nombre"
                        type="text"
                        v-model="productoState.nombre"
                        :invalid="nombreError != ''"
                        class="w-full"
                        @input="clearErrores('nombre')" />
                </FormField>

                <FormField id="descripcion" label="Descripción" :error="descripcionError" v-model="productoState.descripcion">
                    <Textarea
                        id="descripcion"
                        class="w-full"
                        size="large"
                        rows="5"
                        v-model="productoState.descripcion"
                        :invalid="descripcionError != ''"
                        @input="clearErrores('descripcion')" />
                </FormField>
                <div class="grid grid-cols-12 gap-8">
                    <FormField id="altura_suela" label="Altura de la suela" :error="alturaSuelaError" class="col-span-12 xl:col-span-6">
                        <Select
                            id="altura_suela"
                            v-model="productoState.altura_suela"
                            :options="[...ALTURA_SUELA_VALORES]"
                            placeholder="Seleccionar Altura de la Suela"
                            :invalid="alturaSuelaError != ''"
                            showClear
                            class="w-full"
                            @input="clearErrores('altura_suela')" />
                    </FormField>

                    <FormField id="cierre" label="Tiopo de cierre" :error="cierreError" class="col-span-12 xl:col-span-6">
                        <Select
                            id="cierre"
                            v-model="productoState.cierre"
                            :options="[...CIERRE_VALORES]"
                            placeholder="Seleccionar Tipo de cierre"
                            :invalid="cierreError != ''"
                            showClear
                            class="w-full"
                            @input="clearErrores('cierre')" />
                    </FormField>
                </div>

                <div class="grid grid-cols-12 gap-8">
                    <FormField id="tipo" label="Tipo" :error="tipoError" class="col-span-12 xl:col-span-6">
                        <Select
                            id="tipo"
                            v-model="productoState.tipo"
                            :options="[...TIPOS_VALORES]"
                            placeholder="Seleccionar Tipo"
                            :invalid="tipoError != ''"
                            showClear
                            class="w-full"
                            @input="clearErrores('tipo')" />
                    </FormField>

                    <FormField id="genero" label="Genero" :error="generoError" class="col-span-12 xl:col-span-6">
                        <Select
                            id="genero"
                            v-model="productoState.genero"
                            :options="[...GENEROS_VALORES]"
                            placeholder="Seleccionar Genero"
                            :invalid="generoError != ''"
                            showClear
                            class="w-full"
                            @input="clearErrores('genero')" />
                    </FormField>
                </div>

                <FormField id="plantilla" label="Plantilla" :error="plantillaError" v-model="productoState.plantilla">
                    <InputText
                        id="plantilla"
                        type="text"
                        v-model="productoState.plantilla"
                        :invalid="plantillaError != ''"
                        class="w-full"
                        @input="clearErrores('plantilla')" />
                </FormField>
            </div>

            <div class="col-span-12 xl:col-span-12">
                <div class="flex justify-end mt-4">
                    <Button
                        type="submit"
                        :label="props.isEditMode ? 'Guardar Cambios' : 'Crear Producto'"
                        :loading="loading"
                        severity="primary"
                        icon="pi pi-check"
                        :disabled="loading" />
                </div>
            </div>
        </form>
    </TabPanel>
</template>
