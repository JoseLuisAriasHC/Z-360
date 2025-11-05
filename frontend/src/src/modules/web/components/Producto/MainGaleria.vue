<script setup lang="ts">
    import { ref } from 'vue';

    const props = defineProps<{
        imagenes: string[];
    }>();

    const responsiveOptionsProducto = ref([
        { breakpoint: '1900px', numVisible: 5 },
        { breakpoint: '1600px', numVisible: 4 },
        { breakpoint: '650px', numVisible: 3 },
        { breakpoint: '450px', numVisible: 2 },
        { breakpoint: '350px', numVisible: 1 },
    ]);
</script>

<template>
    <Galleria
        :value="imagenes"
        :responsiveOptions="responsiveOptionsProducto"
        :numVisible="10"
        :circular="true"
        thumbnailsPosition="bottom"
        indicatorsPosition="bottom"
        :showItemNavigators="true"
        :pt="{
            thumbnailsViewport: { style: 'height: 100%;' },
            nextButton: { style: 'color: #1A1A1A;' },
            prevButton: { style: 'color: #1A1A1A;' },
        }">
        <template #item="slotProps">
            <div class="imagen-main">
                <img :src="slotProps.item" :alt="slotProps.item.alt" class="w-full h-full object-contain" />
            </div>
        </template>
        <template #thumbnail="slotProps">
            <div class="imagen-miniatura">
                <img :src="slotProps.item" :alt="slotProps.item.alt" />
            </div>
        </template>
    </Galleria>
</template>

<style scoped>
    .imagen-miniatura {
        width: 109px;
        height: 109px;
    }

    @media (min-width: 1280px) {
        .imagen-main {
            width: 680px;
            height: 680px;
        }
    }
</style>
