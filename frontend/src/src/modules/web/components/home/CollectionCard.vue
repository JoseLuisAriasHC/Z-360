<script setup lang="ts">
    import type { RouteParamsRawGeneric } from 'vue-router';
    import CardButton from './CardButton.vue';

    interface CardButtonConfig {
        text: string;
        routeName: string;
        params: RouteParamsRawGeneric | undefined;
    }

    const props = defineProps<{
        title: string;
        description: string;
        imageUrl: string;
        buttons: CardButtonConfig[];
    }>();
</script>

<template>
    <div
        class="card-container flex flex-col justify-between p-6 rounded-xl shadow-lg hover:shadow-2xl transition duration-300 transform hover:-translate-y-1 h-full bg-cover bg-center relative overflow-hidden"
        :style="{ backgroundImage: `url(${imageUrl})` }">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm rounded-xl"></div>

        <div class="relative flex flex-col justify-between h-full">
            <div>
                <h3 class="text-3xl font-extrabold text-white mb-4 drop-shadow-lg">{{ title }}</h3>
                <p class="text-white/80 text-lg mb-6 drop-shadow-md">{{ description }}</p>
            </div>

            <div class="space-y-3 mt-6">
                <CardButton v-for="(button, index) in buttons" :key="index" :text="button.text" :route-name="button.routeName" :params="button.params" />
            </div>
        </div>
    </div>
</template>

<style scoped>
    @media (min-width: 1280px) {
        .card-container {
            min-height: 40rem;
        }
    }
</style>
