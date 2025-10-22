<script setup lang="ts">
    import { ref, onMounted } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import FormField from '@admin/components/FormField.vue';
    import { type WebSetting, WebSettingService } from '@admin/services/WebSettingService';

    const toast = useToast();

    const loading = ref(false);
    const costeEnvioError = ref('');
    const freeCosteEnvioFromError = ref('');

    const clearErrores = (field: EnvioKeys) => {
        if (field === 'coste_envio') costeEnvioError.value = '';
        if (field === 'free_coste_envio_from') freeCosteEnvioFromError.value = '';
    };

    const createEmptySetting = (clave: string): WebSetting => ({
        clave: clave,
        valor: '0',
        nombre: '',
        descripcion: '',
    });

    type EnvioKeys = 'coste_envio' | 'free_coste_envio_from';
    const EnvioState = ref<Record<EnvioKeys, WebSetting>>({
        coste_envio: createEmptySetting('coste_envio'),
        free_coste_envio_from: createEmptySetting('free_coste_envio_from'),
    });

    function loadStates(settingsArray: WebSetting[]) {
        settingsArray.forEach((setting) => {
            switch (setting.clave as EnvioKeys) {
                case 'coste_envio':
                    EnvioState.value.coste_envio = setting;
                    break;
                case 'free_coste_envio_from':
                    EnvioState.value.free_coste_envio_from = setting;
                    break;
                default:
                    break;
            }
        });
    }

    const loadSettingsData = async () => {
        loading.value = true;
        try {
            const data = await WebSettingService.getEnvioSettings();
            const settingsArray = Object.values(data) as WebSetting[];
            loadStates(settingsArray);
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar las configuraciones', life: 5000 });
        } finally {
            loading.value = false;
        }
    };

    function getFormData(): FormData {
        const formData = new FormData();
        formData.append('coste_envio', EnvioState.value.coste_envio.valor);
        formData.append('free_coste_envio_from', EnvioState.value.free_coste_envio_from.valor);
        return formData;
    }

    function saveErrors(responseData: any) {
        costeEnvioError.value = responseData.errors.coste_envio ? responseData.errors.coste_envio[0] : '';
        freeCosteEnvioFromError.value = responseData.errors.free_coste_envio_from ? responseData.errors.free_coste_envio_from[0] : '';
    }

    const handleSubmit = async () => {
        loading.value = true;
        const formData = getFormData();

        try {
            const data = await WebSettingService.saveEnvioSetting(formData);
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 3000 });
        } catch (error: any) {
            const responseData = error.response?.data;

            if (error.response?.status === 422 && responseData?.errors) {
                saveErrors(responseData);
                toast.add({
                    severity: 'error',
                    summary: 'Error de Validación',
                    detail: 'Por favor, corrige los errores en el formulario.',
                    life: 5000,
                });
            } else {
                const detail = responseData?.message || 'Error desconocido al guardar los parametros de envio.';
                toast.add({ severity: 'error', summary: 'Error al guardar', detail, life: 3000 });
                console.error('Error al enviar formulario:', error);
            }
        } finally {
            loading.value = false;
        }
    };

    onMounted(() => {
        loadSettingsData();
    });
</script>
<template>
    <div class="card">
        <div class="font-semibold text-xl mb-4">Configurar parámetros del envio</div>
        <form @submit.prevent="handleSubmit" class="flex flex-col gap-4 p-8 pb-4">
            <div class="flex flex-col gap-2">
                <FormField :id="EnvioState.coste_envio.clave" :label="EnvioState.coste_envio.nombre" :error="costeEnvioError" :hint="EnvioState.coste_envio.descripcion">
                    <InputText
                        :id="EnvioState.coste_envio.clave"
                        mode="decimal"
                        :minFractionDigits="2"
                        :maxFractionDigits="2"
                        locale="es-ES"
                        v-model="EnvioState.coste_envio.valor"
                        :invalid="costeEnvioError != ''"
                        class="w-full"
                        @input="clearErrores('coste_envio')" />
                </FormField>
                <FormField
                    :id="EnvioState.free_coste_envio_from.clave"
                    :label="EnvioState.free_coste_envio_from.nombre"
                    :error="freeCosteEnvioFromError" :hint="EnvioState.free_coste_envio_from.descripcion">
                    <InputText
                        :id="EnvioState.free_coste_envio_from.clave"
                        mode="decimal"
                        :minFractionDigits="2"
                        :maxFractionDigits="2"
                        locale="es-ES"
                        v-model="EnvioState.free_coste_envio_from.valor"
                        :invalid="freeCosteEnvioFromError != ''"
                        class="w-full"
                        @input="clearErrores('free_coste_envio_from')" />
                </FormField>
            </div>
            <div class="col-span-12">
                <div class="flex justify-end">
                    <Button type="submit" label="Guardar Cambios" :loading="loading" severity="primary" icon="pi pi-check" :disabled="loading" />
                </div>
            </div>
        </form>
    </div>
</template>
