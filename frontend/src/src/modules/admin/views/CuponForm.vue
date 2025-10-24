<script setup lang="ts">
    import { ref, onMounted, computed } from 'vue';
    import { useRouter } from 'vue-router';
    import { useToast } from 'primevue/usetoast';
    import { type Cupon, CuponService } from '@admin/services/CuponService';
    import FormField from '@admin/components/FormField.vue';
    import { getParamId } from '@/utils/utils';

    const getOneWeekFromNow = (): Date => {
        const date = new Date();
        date.setDate(date.getDate() + 7);
        return date;
    };

    // --- PROPS Y HOOKS ---
    const router = useRouter();
    const toast = useToast();

    const cuponId = getParamId();
    const isEditMode = computed(() => cuponId.value !== null);

    interface CuponFormState extends Omit<Cupon, 'id'> {}

    // Inicialización del estado
    const cuponState = ref<CuponFormState & { id?: number }>({
        id: cuponId.value || undefined,
        codigo: '',
        descuento: 0,
        tipo: 'porcentaje',
        fecha_expiracion: getOneWeekFromNow(),
        uso_maximo: 1,
    });

    // Referencias para manejar errores de validación del backend (422)
    const codigoError = ref('');
    const descuentoError = ref('');
    const usoMaximoError = ref('');
    const fechaExpiracionError = ref('');
    const loading = ref(false);

    const clearErrores = (field: keyof CuponFormState) => {
        if (field === 'codigo') codigoError.value = '';
        if (field === 'descuento') descuentoError.value = '';
        if (field === 'uso_maximo') usoMaximoError.value = '';
        if (field === 'fecha_expiracion') fechaExpiracionError.value = '';
    };

    const loadData = async (id: number) => {
        loading.value = true;
        try {
            const data = await CuponService.getCupon(id);
            cuponState.value = data;
            if (data.fecha_expiracion) {
                cuponState.value.fecha_expiracion = new Date(data.fecha_expiracion);
            } else {
                cuponState.value.fecha_expiracion = new Date();
            }
            cuponState.value.uso_maximo = data.uso_maximo;
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar el cupón.', life: 3000 });
            router.push({ name: 'admin-not-found' });
        } finally {
            loading.value = false;
        }
    };

    /** Prepara y envía los datos al servicio API */
    const handleSubmit = async () => {
        loading.value = true;
        codigoError.value = '';
        descuentoError.value = '';
        usoMaximoError.value = '';

        const formData = new FormData();
        formData.append('codigo', cuponState.value.codigo);
        formData.append('descuento', cuponState.value.descuento.toString());
        formData.append('tipo', cuponState.value.tipo);
        if (cuponState.value.fecha_expiracion) {
            const date = cuponState.value.fecha_expiracion;
            const formattedDate = date.toISOString().slice(0, 19).replace('T', ' ');
            formData.append('fecha_expiracion', formattedDate);
        } else {
            formData.append('fecha_expiracion', '');
        }

        formData.append('uso_maximo', cuponState.value.uso_maximo.toString());

        try {
            const data = await CuponService.saveCupon(formData, cuponState.value.id);
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 3000 });
            router.push({ name: 'admin-cupones' });
        } catch (error: any) {
            const responseData = error.response?.data;

            if (error.response?.status === 422 && responseData?.errors) {
                // Mapeo de errores de validación
                codigoError.value = responseData.errors.codigo ? responseData.errors.codigo[0] : '';
                descuentoError.value = responseData.errors.descuento ? responseData.errors.descuento[0] : '';
                usoMaximoError.value = responseData.errors.uso_maximo ? responseData.errors.uso_maximo[0] : '';
                fechaExpiracionError.value = responseData.errors.fecha_expiracion ? responseData.errors.fecha_expiracion[0] : '';

                toast.add({
                    severity: 'error',
                    summary: 'Error de Validación',
                    detail: 'Por favor, corrige los errores en el formulario.',
                    life: 5000,
                });
            } else {
                const detail = responseData?.message || 'Error desconocido al guardar el cupón.';
                toast.add({ severity: 'error', summary: 'Error al guardar', detail, life: 3000 });
            }
        } finally {
            loading.value = false;
        }
    };

    const goBack = () => {
        router.push({ name: 'admin-cupones' });
    };

    onMounted(() => {
        if (isEditMode.value && cuponId.value) {
            loadData(cuponId.value);
        }
    });
</script>

<template>
    <div class="flex flex-col md:flex-row">
        <div class="w-full">
            <form @submit.prevent="handleSubmit" class="card flex flex-col gap-4">
                <div class="font-semibold text-xl flex items-center justify-between">
                    <h5 class="text-xl font-bold">
                        {{ isEditMode ? 'Editar Cupón' : 'Crear Nuevo Cupón' }}
                    </h5>
                    <Button icon="pi pi-arrow-left" label="Volver" severity="secondary" @click="goBack" :fluid="false" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Código -->
                    <FormField id="codigo" label="Código" :error="codigoError">
                        <InputText
                            id="codigo"
                            type="text"
                            v-model="cuponState.codigo"
                            :invalid="codigoError != ''"
                            class="w-full"
                            @input="clearErrores('codigo')" />
                    </FormField>

                    <!-- Descuento -->
                    <FormField id="descuento" label="Descuento" :error="descuentoError">
                        <InputNumber
                            id="descuento"
                            v-model="cuponState.descuento"
                            mode="decimal"
                            :minFractionDigits="2"
                            :maxFractionDigits="2"
                            locale="es-ES"
                            :invalid="descuentoError != ''"
                            class="w-full"
                            @input="clearErrores('descuento')" />
                    </FormField>

                    <FormField id="tipo" label="Tipo de Descuento">
                        <div class="flex items-center gap-4 p-inputgroup">
                            <RadioButton id="tipo_porcentaje" value="porcentaje" v-model="cuponState.tipo" />
                            <label for="tipo_porcentaje">Porcentaje (%)</label>

                            <RadioButton id="tipo_fijo" value="fijo" v-model="cuponState.tipo" />
                            <label for="tipo_fijo">Fijo</label>
                        </div>
                    </FormField>

                    <!-- Uso Máximo -->
                    <FormField id="uso_maximo" label="Uso Máximo (Veces)" :error="usoMaximoError">
                        <InputNumber
                            id="uso_maximo"
                            v-model="cuponState.uso_maximo"
                            mode="decimal"
                            showButtons
                            :min="1"
                            :max="99999"
                            :invalid="usoMaximoError != ''"
                            class="w-full"
                            @input="clearErrores('uso_maximo')" />
                    </FormField>

                    <FormField id="fecha_expiracion" label="Fecha de Expiración" :error="fechaExpiracionError">
                        <DatePicker
                            :showIcon="true"
                            :showButtonBar="true"
                            :invalid="fechaExpiracionError != ''"
                            v-model="cuponState.fecha_expiracion"
                            dateFormat="dd/mm/yy"
                            placeholder="dd/mm/yy"
                            class="w-2/4"
                            @input="clearErrores('fecha_expiracion')" />
                    </FormField>
                </div>

                <div class="col-span-12">
                    <div class="flex justify-end mt-4">
                        <Button
                            type="submit"
                            :label="isEditMode ? 'Guardar Cambios' : 'Crear Cupón'"
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
