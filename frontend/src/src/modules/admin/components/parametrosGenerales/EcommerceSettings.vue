<script setup lang="ts">
    import { ref, onMounted } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import FormField from '@admin/components/FormField.vue';
    import { type WebSetting, WebSettingService } from '@admin/services/WebSettingService';

    const toast = useToast();

    const loading = ref(false);
    const ivaError = ref('');
    const emailAdminError = ref('');
    const maxFotosProductoPorUsuarioError = ref('');

    const clearErrores = (field: EnvioKeys) => {
        if (field === 'iva') ivaError.value = '';
        if (field === 'email_admin') emailAdminError.value = '';
        if (field === 'max_fotos_producto_por_usuario') maxFotosProductoPorUsuarioError.value = '';
    };

    const createEmptySetting = (clave: string): WebSetting => ({
        clave: clave,
        valor: '',
        nombre: '',
        descripcion: '',
    });

    type EnvioKeys = 'iva' | 'email_admin' | 'max_fotos_producto_por_usuario';
    const EcommerceState = ref<Record<EnvioKeys, WebSetting>>({
        iva: createEmptySetting('iva'),
        email_admin: createEmptySetting('email_admin'),
        max_fotos_producto_por_usuario: createEmptySetting('max_fotos_producto_por_usuario'),
    });

    function loadStates(settingsArray: WebSetting[]) {
        settingsArray.forEach((setting) => {
            switch (setting.clave as EnvioKeys) {
                case 'iva':
                    EcommerceState.value.iva = setting;
                    break;
                case 'email_admin':
                    EcommerceState.value.email_admin = setting;
                    break;
                case 'max_fotos_producto_por_usuario':
                    EcommerceState.value.max_fotos_producto_por_usuario = setting;
                    break;
                default:
                    break;
            }
        });
    }

    const loadSettingsData = async () => {
        loading.value = true;
        try {
            const data = await WebSettingService.getEcommerceSettings();
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
        formData.append('iva', EcommerceState.value.iva.valor);
        formData.append('email_admin', EcommerceState.value.email_admin.valor);
        formData.append('max_fotos_producto_por_usuario', EcommerceState.value.max_fotos_producto_por_usuario.valor);
        return formData;
    }

    function saveErrors(responseData: any) {
        ivaError.value = responseData.errors.iva ? responseData.errors.iva[0] : '';
        emailAdminError.value = responseData.errors.email_admin ? responseData.errors.email_admin[0] : '';
        maxFotosProductoPorUsuarioError.value = responseData.errors.max_fotos_producto_por_usuario
            ? responseData.errors.max_fotos_producto_por_usuario[0]
            : '';
    }

    const handleSubmit = async () => {
        loading.value = true;
        const formData = getFormData();

        try {
            const data = await WebSettingService.saveEcommerceSetting(formData);
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
                const detail = responseData?.message || 'Error desconocido al guardar los parametros del ecommerce.';
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
        <div class="font-semibold text-xl mb-4">Configurar parámetros del ecommerce</div>
        <form @submit.prevent="handleSubmit" class="flex flex-col gap-4 p-8 pb-4">
            <div class="flex flex-col gap-2">
                <FormField :id="EcommerceState.iva.clave" :label="EcommerceState.iva.nombre" :error="ivaError" :hint="EcommerceState.iva.descripcion">
                    <InputText
                        :id="EcommerceState.iva.clave"
                        type="text"
                        v-model="EcommerceState.iva.valor"
                        :invalid="ivaError != ''"
                        class="w-full"
                        @input="clearErrores('iva')" />
                </FormField>
                <FormField
                    :id="EcommerceState.email_admin.clave"
                    :label="EcommerceState.email_admin.nombre"
                    :error="emailAdminError"
                    :hint="EcommerceState.email_admin.descripcion">
                    <InputText
                        :id="EcommerceState.email_admin.clave"
                        type="email"
                        v-model="EcommerceState.email_admin.valor"
                        :invalid="emailAdminError != ''"
                        class="w-full"
                        @input="clearErrores('email_admin')" />
                </FormField>

                <FormField
                    :id="EcommerceState.max_fotos_producto_por_usuario.clave"
                    :label="EcommerceState.max_fotos_producto_por_usuario.nombre"
                    :error="maxFotosProductoPorUsuarioError"
                    :hint="EcommerceState.max_fotos_producto_por_usuario.descripcion">
                    <InputText
                        :id="EcommerceState.max_fotos_producto_por_usuario.clave"
                        type="text"
                        v-model="EcommerceState.max_fotos_producto_por_usuario.valor"
                        :invalid="maxFotosProductoPorUsuarioError != ''"
                        class="w-full"
                        @input="clearErrores('max_fotos_producto_por_usuario')" />
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
