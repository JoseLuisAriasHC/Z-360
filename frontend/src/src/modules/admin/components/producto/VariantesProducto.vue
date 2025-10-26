<script setup lang="ts">
    import { ref } from 'vue';
    import ListadoColoresTallas from '@admin/components/variantesProducto/ListadoColoresTallas.vue';
    import TablaVariantesProducto from '../variantesProducto/TablaVariantesProducto.vue';

    interface TablaVariantesProductoInstance {
        reloadVariantesData: (productoId: number) => void;
    }

    const props = defineProps<{ productoId: number | null }>();
    const tablaRef = ref<TablaVariantesProductoInstance | null>(null);

    const handleTableRefresh = () => {
        if (tablaRef.value && tablaRef.value.reloadVariantesData && props.productoId) {
            tablaRef.value.reloadVariantesData(props.productoId);
        }
    };
</script>
<template>
    <TabPanel value="variantes_producto">
        <div class="main grid grid-cols-12 gap-8">
            <div class="col-span-12 xl:col-span-10 rounded-lg">
                <TablaVariantesProducto ref="tablaRef" :producto-id="props.productoId"></TablaVariantesProducto>
            </div>
            <div class="col-span-12 xl:col-span-2 rounded-lg">
                <ListadoColoresTallas :producto-id="props.productoId" @refrescar-tabla="handleTableRefresh"></ListadoColoresTallas>
            </div>
        </div>
    </TabPanel>
</template>

<style scoped>
    .main {
        height: calc(100vh - 20rem);
    }
</style>
