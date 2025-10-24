<script setup lang="ts">
    import { computed } from 'vue';
    import router from '@/router';
    import { getParamId } from '@/utils/utils';
    import AjustesBasicos from '@admin/components/producto/AjustesBasicos.vue';
    import Detalles from '@admin/components/producto/Detalles.vue';
    import EtiquetasTiposUso from '../components/producto/EtiquetasTiposUso.vue';

    const productoId = getParamId();
    const isEditMode = computed(() => productoId.value !== null);
    console.log(isEditMode);

    const goBack = () => {
        router.push({ name: 'admin-productos' });
    };
</script>

<template>
    <div class="card">
        <div class="col-span-12 xl:col-span-12">
            <div class="font-semibold text-xl flex items-center justify-between">
                <h5 class="text-xl font-bold">
                    {{ isEditMode ? 'Editar Producto' : 'Crear Nuevo Producto' }}
                </h5>
                <Button icon="pi pi-arrow-left" label="Volver" severity="secondary" @click="goBack" :fluid="false" />
            </div>
        </div>
        <Tabs value="general" scrollable>
            <TabList>
                <Tab value="general">Ajustes básicos</Tab>
                <Tab value="detalles">Detalles del producto</Tab>
                <Tab value="etiquetas_tipos_uso">Etiquetas y Tipos de de Uso</Tab>
            </TabList>
            <TabPanels>
                <AjustesBasicos :producto-id="productoId" :is-edit-mode="isEditMode" />
                <Detalles :producto-id="productoId" :disabled="isEditMode" :lazy="isEditMode" />
                <EtiquetasTiposUso :producto-id="productoId" :disabled="isEditMode" :lazy="isEditMode" />
            </TabPanels>
        </Tabs>
    </div>
</template>
