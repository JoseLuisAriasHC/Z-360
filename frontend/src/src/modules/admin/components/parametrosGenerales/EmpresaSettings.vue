<script setup lang="ts">
    import { ref, onMounted } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import FormField from '@admin/components/FormField.vue';
    import { type WebSetting, WebSettingService } from '@admin/services/WebSettingService';

    const toast = useToast();

    const loading = ref(false);
    const direccionError = ref('');
    const nombreError = ref('');
    const telefonoError = ref('');
    const emailError = ref('');

    const clearErrores = (field: EmpresaKeys) => {
        if (field === 'empresa_nombre') nombreError.value = '';
        if (field === 'empresa_direccion') direccionError.value = '';
        if (field === 'empresa_telefono') telefonoError.value = '';
        if (field === 'empresa_email') emailError.value = '';
    };

    const createEmptySetting = (clave: string): WebSetting => ({
        clave: clave,
        valor: '',
        nombre: '',
        descripcion: '',
    });

    type EmpresaKeys = 'empresa_nombre' | 'empresa_direccion' | 'empresa_telefono' | 'empresa_email';
    const EmpresaState = ref<Record<EmpresaKeys, WebSetting>>({
        empresa_nombre: createEmptySetting('empresa_nombre'),
        empresa_direccion: createEmptySetting('empresa_direccion'),
        empresa_telefono: createEmptySetting('empresa_telefono'),
        empresa_email: createEmptySetting('empresa_email'),
    });

    function loadStates(settingsArray: WebSetting[]) {
        settingsArray.forEach((setting) => {
            switch (setting.clave as EmpresaKeys) {
                case 'empresa_nombre':
                    EmpresaState.value.empresa_nombre = setting;
                    break;
                case 'empresa_direccion':
                    EmpresaState.value.empresa_direccion = setting;
                    break;
                case 'empresa_telefono':
                    EmpresaState.value.empresa_telefono = setting;
                    break;
                case 'empresa_email':
                    EmpresaState.value.empresa_email = setting;
                    break;
                default:
                    break;
            }
        });
    }

    const loadSettingsData = async () => {
        loading.value = true;
        try {
            const data = await WebSettingService.getEmpresaSettings();
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
        formData.append('empresa_nombre', EmpresaState.value.empresa_nombre.valor);
        formData.append('empresa_direccion', EmpresaState.value.empresa_direccion.valor);
        formData.append('empresa_telefono', EmpresaState.value.empresa_telefono.valor);
        formData.append('empresa_email', EmpresaState.value.empresa_email.valor);
        return formData;
    }

    function saveErrors(responseData: any) {
        nombreError.value = responseData.errors.empresa_nombre ? responseData.errors.empresa_nombre[0] : '';
        direccionError.value = responseData.errors.empresa_direccion ? responseData.errors.empresa_direccion[0] : '';
        telefonoError.value = responseData.errors.empresa_telefono ? responseData.errors.empresa_telefono[0] : '';
        emailError.value = responseData.errors.empresa_email ? responseData.errors.empresa_email[0] : '';
    }

    const handleSubmit = async () => {
        loading.value = true;
        const formData = getFormData();

        try {
            const data = await WebSettingService.saveEmpresaSetting(formData);
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
                const detail = responseData?.message || 'Error desconocido al guardar los datos de la empresa.';
                toast.add({ severity: 'error', summary: 'Error al guardar', detail, life: 3000 });
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
        <div class="font-semibold text-xl mb-4">Datos de la empresa</div>
        <form @submit.prevent="handleSubmit" class="flex flex-col gap-4 p-8 pb-4">
            <div class="flex flex-col gap-2">
                <FormField
                    :id="EmpresaState.empresa_nombre.clave"
                    :label="EmpresaState.empresa_nombre.nombre"
                    :error="nombreError"
                    :hint="EmpresaState.empresa_nombre.descripcion">
                    <InputText
                        :id="EmpresaState.empresa_nombre.clave"
                        type="text"
                        v-model="EmpresaState.empresa_nombre.valor"
                        :invalid="nombreError != ''"
                        class="w-full"
                        @input="clearErrores('empresa_nombre')" />
                </FormField>
                <FormField
                    :id="EmpresaState.empresa_direccion.clave"
                    :label="EmpresaState.empresa_direccion.nombre"
                    :error="direccionError"
                    :hint="EmpresaState.empresa_direccion.descripcion">
                    <InputText
                        :id="EmpresaState.empresa_direccion.clave"
                        type="text"
                        v-model="EmpresaState.empresa_direccion.valor"
                        :invalid="direccionError != ''"
                        class="w-full"
                        @input="clearErrores('empresa_direccion')" />
                </FormField>

                <FormField
                    :id="EmpresaState.empresa_email.clave"
                    :label="EmpresaState.empresa_email.nombre"
                    :error="emailError"
                    :hint="EmpresaState.empresa_email.descripcion">
                    <InputText
                        :id="EmpresaState.empresa_email.clave"
                        type="email"
                        v-model="EmpresaState.empresa_email.valor"
                        :invalid="emailError != ''"
                        class="w-full"
                        @input="clearErrores('empresa_email')" />
                </FormField>
                <FormField
                    :id="EmpresaState.empresa_telefono.clave"
                    :label="EmpresaState.empresa_telefono.nombre"
                    :error="telefonoError"
                    :hint="EmpresaState.empresa_telefono.descripcion">
                    <InputText
                        :id="EmpresaState.empresa_telefono.clave"
                        type="text"
                        v-model="EmpresaState.empresa_telefono.valor"
                        :invalid="telefonoError != ''"
                        class="w-full"
                        @input="clearErrores('empresa_telefono')" />
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
