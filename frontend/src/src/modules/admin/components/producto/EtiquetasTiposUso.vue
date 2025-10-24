<script setup lang="ts">
    import Etiquetas from '@admin/components/producto/Etiquetas.vue';
    const props = defineProps<{
        productoId: number | null;
    }>();
</script>

<template>
    <TabPanel value="etiquetas_tipos_uso">
        <div class="grid grid-cols-12 gap-8">
            <div class="col-span-12">
                <Etiquetas :producto-id="props.productoId" />
            </div>
            <div class="col-span-12"></div>
        </div>
    </TabPanel>
</template>
