<script setup lang="ts">
    import { ref } from 'vue';
    import type { VarianteDisponible } from '@web/services/ProductoService';

    const props = defineProps<{
        variantes: VarianteDisponible[] | undefined;
    }>();

    const responsiveOptionsVariantes = ref([
        { breakpoint: '1000px', numVisible: 7 },
        { breakpoint: '600px', numVisible: 3 },
        { breakpoint: '350px', numVisible: 1 },
    ]);
</script>

<template>
    <Galleria
        :value="variantes"
        :responsiveOptions="responsiveOptionsVariantes"
        :numVisible="10"
        :circular="true"
        :showItemNavigators="true"
        :pt="{
            nextButton: { style: 'color: #1A1A1A;' },
            prevButton: { style: 'color: #1A1A1A;' },
            itemsContainer: { style: 'display: none;' },
            root: { style: 'border: none;' },
            thumbnailItem: { style: 'opacity: 1;' },
        }">
        <template #thumbnail="data">
            <div
                class="container-variantes-galeria mx-2"
                :class="{
                    'border border-muted-light shadow-md': data.item.seleccionada,
                }">
                <img :src="data.item.imagen_principal" :alt="data.item.id" class="w-full h-full object-contain" />
            </div>
        </template>
    </Galleria>
</template>

<style scoped>
    .container-variantes-galeria {
        height: 78px;
        width: 78px;
    }
</style>
