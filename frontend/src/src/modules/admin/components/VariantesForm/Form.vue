<script setup lang="ts">
    import { ref, onMounted } from 'vue';
    import { useRouter } from 'vue-router';
    import { useToast } from 'primevue/usetoast';
    import { type VarianteProducto, VarianteProductoService } from '@admin/services/VarianteProductoService';
    import FormField from '@admin/components/FormField.vue';
    import { formatDateForForm, getParamId } from '@/utils/utils';

    // --- PROPS Y HOOKS ---
    const router = useRouter();
    const toast = useToast();
    const emit = defineEmits<{
        (e: 'idProducto', dato: number): void;
        (e: 'idColor', dato: number): void;
    }>();

    const varainteId = getParamId();
    const imagen_principal_file = ref<File | null>(null);
    const fileInputRef = ref<HTMLInputElement | null>(null);
    interface VariantFormState extends VarianteProducto {}

    const varianteState = ref<VariantFormState>({
        id: varainteId.value ?? 0,
        product_id: 0,
        color_id: 0,
        precio: 0,
        precio_sin_iva: 0,
        iva: 0,
        imagen_principal: '',
        descuento: 0,
        descuento_desde: new Date(),
        descuento_hasta: new Date(),
    });

    // Referencias para manejar errores de validación del backend (422)
    const loading = ref(false);
    const precioError = ref('');
    const imagenPrincipalError = ref('');
    const descuentoError = ref('');
    const descuentoDesdeError = ref('');
    const descuentoHastaError = ref('');

    const clearErrores = (field: keyof VariantFormState) => {
        if (field === 'precio') precioError.value = '';
        if (field === 'imagen_principal') imagenPrincipalError.value = '';
        if (field === 'descuento') descuentoError.value = '';
        if (field === 'descuento_desde') descuentoDesdeError.value = '';
        if (field === 'descuento_hasta') descuentoHastaError.value = '';
    };

    const loadData = async (id: number) => {
        loading.value = true;
        try {
            const data = await VarianteProductoService.getVariante(id);
            varianteState.value = data;
            if (data.descuento_desde) {
                varianteState.value.descuento_desde = new Date(data.descuento_desde);
                varianteState.value.descuento_hasta = new Date(data.descuento_hasta);
            }
            if (varianteState.value.imagen_principal) {
                varianteState.value.imagen_principal = `${import.meta.env.VITE_STORAGE_URL}/product_variants/M_${data.imagen_principal}`;
            }
            emit('idProducto', data.product_id);
            emit('idColor', data.color_id);
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar la variante del producto.', life: 3000 });
            router.push({ name: 'admin-not-found' });
        } finally {
            loading.value = false;
        }
    };

    function getFormData() {
        const formData = new FormData();
        formData.append('precio', varianteState.value.precio?.toString() == undefined ? '' : varianteState.value.precio?.toString());
        
        formData.append('descuento', varianteState.value.descuento?.toString() == undefined ? '' : varianteState.value.descuento?.toString());
        formData.append('descuento_desde', formatDateForForm(varianteState.value.descuento_desde));
        formData.append('descuento_hasta', formatDateForForm(varianteState.value.descuento_hasta));
        if (imagen_principal_file.value && varianteState.value.imagen_principal) {
            formData.append('imagen_principal', imagen_principal_file.value, varianteState.value.imagen_principal);
        }

        return formData;
    }

    function saveError(responseData: any) {
        precioError.value = responseData.errors.precio ? responseData.errors.precio[0] : '';
        imagenPrincipalError.value = responseData.errors.imagen_principal ? responseData.errors.imagen_principal[0] : '';
        descuentoError.value = responseData.errors.descuento ? responseData.errors.descuento[0] : '';
        descuentoDesdeError.value = responseData.errors.descuento_desde ? responseData.errors.descuento_desde[0] : '';
        descuentoHastaError.value = responseData.errors.descuento_hasta ? responseData.errors.descuento_hasta[0] : '';
    }

    /** Prepara y envía los datos al servicio API */
    const handleSubmit = async () => {
        loading.value = true;
        const formData = getFormData();

        try {
            const data = await VarianteProductoService.saveVariante(formData, varianteState.value.id);
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 3000 });
        } catch (error: any) {
            const responseData = error.response?.data;

            if (error.response?.status === 422 && responseData?.errors) {
                saveError(responseData);
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

    const onFileChange = (event: Event) => {
        const target = event.target as HTMLInputElement;
        const file = target.files?.[0];

        if (file) {
            if (!file.type.startsWith('image/')) {
                imagenPrincipalError.value = 'El archivo debe ser una imagen.';
                imagen_principal_file.value = null;
                varianteState.value.imagen_principal = null;
                return;
            }

            imagen_principal_file.value = file;
            varianteState.value.imagen_principal = URL.createObjectURL(file);
            clearErrores('imagen_principal');
        }
    };

    const removeImagen = () => {
        varianteState.value.imagen_principal = null;
        varianteState.value.imagen_principal = null;

        if (fileInputRef.value) {
            fileInputRef.value.value = '';
        }

        clearErrores('imagen_principal');
    };

    onMounted(() => {
        if (varainteId.value) loadData(varainteId.value);
        else router.push({ name: 'admin-not-found' });
    });
</script>

<template>
    <form @submit.prevent="handleSubmit" class="grid grid-cols-12 gap-8 mt-4">
        <div class="col-span-12 xl:col-span-8">
            <FormField id="precio" label="Precio" :error="precioError">
                <InputNumber
                    v-model="varianteState.precio"
                    :step="5"
                    :min="0"
                    fluid
                    showButtons
                    mode="currency"
                    currency="EUR"
                    :minFractionDigits="2"
                    :maxFractionDigits="5"
                    :invalid="precioError != ''"
                    @input="clearErrores('precio')" />
            </FormField>

            <FormField id="descuento" label="Descuento" :error="descuentoError">
                <InputNumber
                    v-model="varianteState.descuento"
                    :step="5"
                    :min="0"
                    fluid
                    showButtons
                    mode="currency"
                    currency="EUR"
                    :minFractionDigits="2"
                    :maxFractionDigits="5"
                    :invalid="descuentoError != ''"
                    @input="clearErrores('descuento')" />
            </FormField>
            <div class="grid grid-cols-12 gap-8">
                <div class="col-span-12 xl:col-span-6">
                    <FormField id="descuento_desde" label="Fecha de inicio del descuento" :error="descuentoDesdeError">
                        <DatePicker
                            :showIcon="true"
                            :showButtonBar="true"
                            :invalid="descuentoDesdeError != ''"
                            v-model="varianteState.descuento_desde"
                            dateFormat="dd/mm/yy"
                            placeholder="dd/mm/yy"
                            class="w-full"
                            @input="clearErrores('descuento_desde')" />
                    </FormField>
                </div>
                <div class="col-span-12 xl:col-span-6">
                    <FormField id="descuento_hasta" label="Fecha fin del descuento" :error="descuentoHastaError">
                        <DatePicker
                            :showIcon="true"
                            :showButtonBar="true"
                            :invalid="descuentoHastaError != ''"
                            v-model="varianteState.descuento_hasta"
                            dateFormat="dd/mm/yy"
                            placeholder="dd/mm/yy"
                            class="w-full"
                            @input="clearErrores('descuento_hasta')" />
                    </FormField>
                </div>
            </div>
            <FormField id="imagen_princiapl" label="Imagen pincipal" :error="imagenPrincipalError">
                <div class="flex items-start gap-2 mb-4">
                    <div class="flex flex-col gap-2 flex-grow">
                        <input ref="fileInputRef" type="file" accept="image/*" @change="onFileChange" class="hidden" id="imagen-principal" />
                        <label
                            for="imagen-principal"
                            class="w-full text-center inline-flex items-center justify-center px-4 py-2 border border-surface-300 dark:border-surface-600 rounded-md cursor-pointer hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors"
                            :class="{ 'border-red-500': imagenPrincipalError }">
                            <i class="pi pi-upload mr-2"></i>
                            <span>Seleccionar imagen</span>
                        </label>
                        <small class="text-surface-600 dark:text-surface-400">
                            Formatos aceptados: AVIF, JPEG, JPG, PNG, WEBP (recomendado: 2MB)
                        </small>
                    </div>

                    <Button
                        v-if="varianteState.imagen_principal"
                        icon="pi pi-trash"
                        severity="danger"
                        rounded
                        outlined
                        class="h-[42px] w-[42px] flex-shrink-0"
                        @click="removeImagen"
                        v-tooltip.top="'Borrar Imagen principal Actual'" />
                </div>
            </FormField>
        </div>
        <div class="col-span-12 xl:col-span-4">
            <div v-if="varianteState.imagen_principal" class="w-full">
                <div class="inline-block w-full">
                    <img
                        :src="varianteState.imagen_principal"
                        alt="Imagen princiapl"
                        class="w-full max-w-full h-auto object-contain border border-surface-200 dark:border-surface-700 p-4 rounded-lg" />
                </div>
            </div>
        </div>
        <div class="col-span-12">
            <!-- Botón Submit -->
            <div class="flex justify-end mb-4">
                <Button type="submit" label="Guardar Cambios" :loading="loading" severity="primary" icon="pi pi-check" :disabled="loading" />
            </div>
        </div>
    </form>
</template>
