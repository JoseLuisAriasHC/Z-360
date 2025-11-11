<script setup lang="ts">
    import { ref } from 'vue';
    import { GENEROS_VALORES, CRITERIOS_ESPECIALES_VALORES } from '@/constants/productos';
    import card_deporte_hombre from '@/assets/img/card/card_deporte_hombre.webp';
    import card_deporte_mujer from '@/assets/img/card/card_urbano_mujer.avif';
    import card_urbano_hombre from '@/assets/img/card/card_urbano_hombre.webp';
    import card_urbano_mujer from '@/assets/img/card/card_urbano_mujer.webp';

    import CollectionCard from '@web/components/home/CollectionCard.vue';

    const collections = ref([
        {
            title: 'Deporte y Fitness Masculino',
            description: 'No pienses, ¡sal a entrenar! Equípate con la innovación que te llevará a tu próximo récord personal.',
            imageUrl: card_deporte_hombre,
            buttons: [
                {
                    text: 'Explorar Colección',
                    routeName: 'productos',
                    params: { genero: GENEROS_VALORES[0], criterioBusqueda: CRITERIOS_ESPECIALES_VALORES[1] },
                },
            ],
        },
        {
            title: 'Urbano & Street Mujer',
            description: 'Zapatillas de moda para el día a día. Modelos que se adaptan a cualquier plan sin renunciar al confort.',
            imageUrl: card_urbano_mujer,
            buttons: [
                {
                    text: 'Explorar Colección',
                    routeName: 'productos',
                    params: { genero: GENEROS_VALORES[2], criterioBusqueda: CRITERIOS_ESPECIALES_VALORES[1] },
                },
            ],
        },
        {
            title: 'Casual & Street Hombre',
            description: 'Descubre el calzado que define tu estilo en la ciudad. Desde modelos clásicos hasta las últimas tendencias.',
            imageUrl: card_urbano_hombre,
            buttons: [
                {
                    text: 'Explorar Colección',
                    routeName: 'productos',
                    params: { genero: GENEROS_VALORES[1], criterioBusqueda: CRITERIOS_ESPECIALES_VALORES[1] },
                },
            ],
        },
        {
            title: 'Deporte y Fitness Femenino',
            description: 'Prepárate para entrenar. Nuestra selección de calzado técnico de mujer te ayudará a alcanzar tu mejor versión.',
            imageUrl: card_deporte_mujer,
            buttons: [
                {
                    text: 'Explorar Colección',
                    routeName: 'productos',
                    params: { genero: GENEROS_VALORES[1], criterioBusqueda: CRITERIOS_ESPECIALES_VALORES[1] },
                },
            ],
        },
    ]);
</script>

<template>
    <div class="grid grid-cols-12 gap-4 w-full sm:px-4 py-4 xl:py-16 lg:px-4 xl:px-20">
        <div v-for="collection in collections" :key="collection.title" class="col-span-12 lg:col-span-6">
            <CollectionCard
                :title="collection.title"
                :description="collection.description"
                :image-url="collection.imageUrl"
                :buttons="collection.buttons"
                style-button="w-2/5" />
        </div>
    </div>
</template>

<style scoped>
    .card-container {
        min-height: 62rem;
        border-radius: 0;
    }
</style>
