<script setup lang="ts">
    import { ref } from 'vue';
    import banner_1 from '@/assets/img/banner/banner-1.webp';
    import banner_2 from '@/assets/img/banner/banner-2.webp';
    import banner_3 from '@/assets/img/banner/banner-3.webp';

    interface Slide {
        id: number;
        image: string;
        title: string;
        subtitle: string;
        cta: string;
    }

    const slides = ref<Slide[]>([
        {
            id: 1,
            image: banner_1,
            title: 'Colección Pro 2025',
            subtitle: 'Máximo estilo, nuevas novedades en lo Urbano',
            cta: 'Comprar Ahora',
        },
        {
            id: 2,
            image: banner_2,
            title: 'Ofertas de Invierno',
            subtitle: 'Ya paso el verano, pero no las mejores ofertas',
            cta: 'Ver Ofertas',
        },
        {
            id: 3,
            image: banner_3,
            title: 'El Mejor Momento',
            subtitle: 'Velocidad y agarre inigualables para tus carreras.',
            cta: 'Descubrir',
        },
    ]);

    const responsiveOptions = ref([
        { breakpoint: '1024px', numVisible: 1, numScroll: 1 },
        { breakpoint: '768px', numVisible: 1, numScroll: 1 },
        { breakpoint: '560px', numVisible: 1, numScroll: 1 },
    ]);

    const transitionInterval = 5000;
</script>

<template>
    <div class="hero-galleria-container">
        <Galleria
            :value="slides"
            :responsiveOptions="responsiveOptions"
            :numVisible="1"
            :circular="true"
            :autoPlay="true"
            :transitionInterval="transitionInterval"
            :showThumbnails="false"
            :showIndicators="true"
            :showIndicatorsOnItem="true"
            :showItemNavigators="true"
            :showItemNavigatorsOnHover="true"
            indicatorsPosition="bottom"
            containerStyle="width: 100%;">
            <template #item="slotProps">
                <div class="relative w-full h-[400px] sm:h-[500px] lg:h-[700px] overflow-hidden">
                    <img
                        :src="slotProps.item.image"
                        :alt="slotProps.item.title"
                        class="w-full h-full object-cover"
                        onerror="this.onerror=null;this.src='https://placehold.co/1920x500/CCCCCC/333333?text=Error+de+Carga';" />

                    <div class="absolute inset-0 bg-black bg-opacity-10 flex items-center justify-center p-4">
                        <div class="text-center text-white max-w-3xl p-6 rounded-xl backdrop-blur-sm bg-black/20 shadow-2xl">
                            <h2 class="text-4xl sm:text-6xl font-black mb-3 text-surface-light drop-shadow-lg">
                                {{ slotProps.item.title }}
                            </h2>
                            <p class="text-lg sm:text-2xl font-semibold text-muted-border mb-8 drop-shadow-md">
                                {{ slotProps.item.subtitle }}
                            </p>
                        </div>
                    </div>
                </div>
            </template>
        </Galleria>
    </div>
</template>

<style scoped>
    /* Asegura que el contenedor tenga una altura mínima consistente */
    .hero-galleria-container {
        height: 400px;
        width: 100%;
    }
    @media (min-width: 640px) {
        .hero-galleria-container {
            height: 500px;
        }
    }
    @media (min-width: 1024px) {
        .hero-galleria-container {
            height: 700px;
        }
    }

    .p-galleria {
        overflow: auto;
        border-style: none;
        border-radius: 0;
    }
</style>
