<script setup lang="ts">
    import { ref } from 'vue';

    import CollectionCard from '@web/components/home/CollectionCard.vue';

    const collections = ref([
        {
            title: 'Colección Hombre',
            description: 'Descubre la moda masculina con diseños minimalistas y funcionales. Ropa que se adapta a tu ritmo, con máxima comodidad.',
            imageUrl: 'https://placehold.co/600x400/055996/ffffff?text=HOMBRE',
            buttons: [
                { text: 'Top ventas', routeName: 'catalogo-hombre-casual' },
                { text: 'Ofertas', routeName: 'catalogo-hombre-accesorios' },
                { text: 'Novedades', routeName: 'catalogo-hombre-accesorios' },
            ],
        },
        {
            title: 'Colección Unisex',
            description: 'Prendas versátiles que desafían las convenciones. Diseños modernos y neutrales para un armario sin límites.',
            imageUrl: 'https://placehold.co/600x400/1D2024/ffffff?text=UNISEX',
            buttons: [
                { text: 'Top ventas', routeName: 'catalogo-hombre-casual' },
                { text: 'Ofertas', routeName: 'catalogo-hombre-accesorios' },
                { text: 'Novedades', routeName: 'catalogo-hombre-accesorios' },
            ],
        },
        {
            title: 'Colección Mujer',
            description: 'Las últimas tendencias en moda femenina. Estilo atemporal que combina elegancia y comodidad para cualquier ocasión.',
            imageUrl: 'https://placehold.co/600x400/D35D96/ffffff?text=MUJER',
            buttons: [
                { text: 'Top ventas', routeName: 'catalogo-hombre-casual' },
                { text: 'Ofertas', routeName: 'catalogo-hombre-accesorios' },
                { text: 'Novedades', routeName: 'catalogo-hombre-accesorios' },
            ],
        },
    ]);
</script>

<template>
    <div class="grid grid-cols-12 gap-8 w-full px-4 sm:px-10 xl:py-16 xl:px-20">
        <div class="col-span-12">
            <h2 class="text-4xl font-extrabold text-gray-800 text-center mt-8 mb-12 pb-3">Más que una Tienda, un Estilo de Vida</h2>
        </div>

        <div v-for="collection in collections" :key="collection.title" class="col-span-12 md:col-span-6 xl:col-span-4">
            <CollectionCard
                :title="collection.title"
                :description="collection.description"
                :image-url="collection.imageUrl"
                :buttons="collection.buttons" />
        </div>
    </div>
</template>
