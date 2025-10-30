<script setup lang="ts">
    import { useRouter } from 'vue-router';

    const props = defineProps<{
        text: string;
        routeName: string;
    }>();

    const router = useRouter();

    const navigate = () => {
        router.push({ name: props.routeName });
    };
</script>

<template>
    <button
        @click="navigate"
        class="w-full text-lg font-semibold py-3 rounded-xl shadow-lg backdrop-blur-md bg-white/20 text-white/90 border border-white/50 transition duration-500 ease-in-out hover:bg-white/40 hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] drop-shadow-lg"
        title="Clic para navegar a la colección">
        {{ text }}
    </button>
</template>
