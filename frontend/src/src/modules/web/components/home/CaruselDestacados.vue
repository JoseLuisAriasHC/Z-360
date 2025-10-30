<script setup>
    import { ref } from 'vue';

    const slides = ref([
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
        {
            breakpoint: '1024px',
            numVisible: 1,
            numScroll: 1,
        },
        {
            breakpoint: '768px',
            numVisible: 1,
            numScroll: 1,
        },
        {
            breakpoint: '560px',
            numVisible: 1,
            numScroll: 1,
        },
    ]);
</script>

<template>
    <Carousel
        :value="slides"
        :numVisible="1"
        :numScroll="1"
        :responsiveOptions="responsiveOptions"
        circular
        :autoplayInterval="3000"
        :pt="{
            root: {
                class: 'bg-gray-100',
            },
        }">
        <template #item="slotProps">
            <div class="relative w-full h-[400px] sm:h-[500px] lg:h-[600px] overflow-hidden">
                <img :src="slotProps.data.image" :alt="slotProps.data.title" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center p-4">
                    <div class="text-center text-white max-w-3xl p-6 rounded-xl backdrop-blur-sm bg-black bg-opacity-20 shadow-2xl">
                        <h2 class="text-4xl sm:text-6xl font-black mb-3 tracking-tight drop-shadow-lg">
                            {{ slotProps.data.title }}
                        </h2>
                        <p class="text-lg sm:text-2xl font-light mb-8 drop-shadow-md">
                            {{ slotProps.data.subtitle }}
                        </p>
                        <button
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-full shadow-2xl transition duration-300 transform hover:scale-105">
                            {{ slotProps.data.cta }}
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </Carousel>
</template>
