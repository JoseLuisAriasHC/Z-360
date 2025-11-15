<script setup lang="ts">
    import { useSettingsStore } from '@/stores/settings';
    import { computed } from 'vue';
    import { useCestaStore } from '../stores/cesta';
    import ListaProductos from '../components/Carrito/ListaProductos.vue';
    import Resumen from '../components/Carrito/Resumen.vue';

    const cesta = useCestaStore();
    const settings = useSettingsStore();

    const envioGratis = computed(() => {
        if (!settings.envioSettings) return false;
        return cesta.total >= settings.envioSettings.free_coste_envio_from;
    });

    const costeEnvio = computed(() => {
        if (!settings.envioSettings) return 0;
        return envioGratis.value ? 0 : settings.envioSettings.coste_envio;
    });
</script>

<template>
    <div class="grid grid-cols-12 gap-8 mx-auto xl:pt-20 xl:w-2/3 text-text-light">
        <div class="col-span-12 xl:col-span-8 flex flex-col" style="min-height: 80vh">
            <Card>
                <template #title>
                    <span class="text-naranja font-bold text-xl uppercase">Envio gratis</span>
                </template>
                <template #content>
                    <p class="font-semibold text-lg text-muted-light">
                        No te quedes sin ese producto que tanto deseas. Es el momento ideal para completar tu carrito, ya que tienes el envío por
                        nuestra cuenta al superar los {{ costeEnvio }} € de compra. ¡Aprovecha y haz tu pedido ahora mismo antes de que se agote tu
                        talla!
                    </p>
                </template>
            </Card>

            <div v-if="cesta.totalItems > 0" class="mb-8 mt-4">
                <h1 class="font-semibold text-3xl mb-8 text-text-light text-inter">Cesta</h1>
                <div class="flex flex-col gap-4 mt-4 px-8 xl:p-0">
                    <ListaProductos />
                </div>
            </div>

            <router-link v-else :to="{ name: 'home' }">
                <Button severity="contrast" class="mt-4">
                    <span class="text-2xl mr-3">Comenzar</span>
                    <i class="pi pi-arrow-right" style="font-size: 1.5rem" />
                </Button>
            </router-link>


            <!-- TODO: Usuario -->
            <!-- <div class="pt-4 mt-auto border-solid border-t-2 border-muted-border px-8 xl:p-0">
                <div class="font-semibold text-3xl">Novedades</div>
                <div class="flex">
                    <p class="font-semibold text-lg">¿Quieres resivir novedades?</p>
                    <div class="ml-2 font-semibold text-lg">
                        <a href="#" class="text-muted-dark hover:border-b border-black">Únete a nosotros</a>
                        <span class="mx-1">o</span>
                        <a href="#" class="text-muted-dark hover:border-b border-black">Iniciar sesión</a>
                    </div>
                </div>
            </div> -->
        </div>

        <div class="col-span-12 xl:col-span-4 px-8 xl:p-0">
            <div class="xl:sticky xl:top-32">
                <Resumen />
            </div>
        </div>
    </div>
</template>
