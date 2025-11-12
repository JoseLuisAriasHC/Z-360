<script setup lang="ts">
    import not_found from '@/assets/img/not-found.avif';
    import CardButton from '@web/components/home/CardButton.vue';
    import SeccionColeccion from '../components/home/SeccionColeccion.vue';

    const styleButton = 'px-6 py-4 ';
</script>

<template>
    <div class="main-container w-full lg:flex justify-center items-center">
        <div class="main-background" :style="{ backgroundImage: 'url(' + not_found + ')' }"></div>

        <div class="main-content">
            <div class="mb-20">
                <h1 class="text-oswald text-yellow-300 font-semibold text-7xl">404</h1>
                <p class="text-oswald text-yellow-300 font-semibold text-3xl">UUUUPS, no hemos encontrado lo que estás buscando...</p>
            </div>
            <CardButton text="Explorar otras categorias" :style="styleButton" />
        </div>
    </div>
    <SeccionColeccion />
</template>

<style scoped>
    .main-container {
        position: relative;
        min-height: 60vh;
    }

    .main-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;

        background-size: cover, contain;
        background-position: center, center;
        background-repeat: no-repeat, no-repeat;
    }

    .main-content {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        text-align: center;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
    }
</style>
