<script setup lang="ts">
    import { ref } from 'vue';
    import Button from 'primevue/button';

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
            image: 'https://placehold.co/1920x500/1E3A8A/FFFFFF?text=Lanzamiento%3A+Zapatillas+Pro',
            title: 'Colección Pro 2024',
            subtitle: 'Velocidad y agarre inigualables para tus carreras.',
            cta: 'Comprar Ahora',
        },
        {
            id: 2,
            image: 'https://placehold.co/1920x500/065F46/FFFFFF?text=OFERTA%3A+Hasta+50%25+en+Sandalias',
            title: 'Ofertas de Verano',
            subtitle: 'Máximo estilo, precios increíbles en sandalias y alpargatas.',
            cta: 'Ver Ofertas',
        },
        {
            id: 3,
            image: 'https://placehold.co/1920x500/9D174D/FFFFFF?text=Calzado+Casual+Elegante',
            title: 'Clásico Renovado',
            subtitle: 'Diseño atemporal y elegante para tu día a día con el mejor confort.',
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
                <div class="relative w-full h-[400px] sm:h-[500px] lg:h-[600px] overflow-hidden">
                    <img
                        :src="slotProps.item.image"
                        :alt="slotProps.item.title"
                        class="w-full h-full object-cover"
                        onerror="this.onerror=null;this.src='https://placehold.co/1920x500/CCCCCC/333333?text=Error+de+Carga';" />

                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center p-4">
                        <div class="text-center text-white max-w-3xl p-6 rounded-xl backdrop-blur-sm bg-black/20 shadow-2xl">
                            <h2 class="text-4xl sm:text-6xl font-black mb-3 tracking-tight drop-shadow-lg">
                                {{ slotProps.item.title }}
                            </h2>
                            <p class="text-lg sm:text-2xl font-light mb-8 drop-shadow-md">
                                {{ slotProps.item.subtitle }}
                            </p>
                            <Button
                                :label="slotProps.item.cta"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-full shadow-2xl transition duration-300 transform hover:scale-105" />
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
            height: 600px;
        }
    }

    .p-galleria {
        overflow: auto;
        border-style: none;
        border-radius: 0;
    }
</style>
