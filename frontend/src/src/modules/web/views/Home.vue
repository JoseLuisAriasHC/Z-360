<script setup lang="ts">
    import CaruselDestacados from '@web/components/home/CaruselDestacados.vue';
    import SeccionColeccion from '@web/components/home/SeccionColeccion.vue';
    import SeccionColeccionTipo from '@web/components/home/SeccionColeccionTipo.vue';
</script>

<template>
    <div class="w-full max-w-full shadow-2xl h-[400px] sm:h-[500px] lg:h-[700px]">
        <CaruselDestacados />
    </div>
    <SeccionColeccion />
    <SeccionColeccionTipo />
</template>
