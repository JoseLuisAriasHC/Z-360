<script setup lang="ts">
    // Asegúrate de que este componente esté disponible
    import Divider from 'primevue/divider';
    import ButtonDark from '../components/ButtonDark.vue';
    import { onBeforeRouteUpdate } from 'vue-router';
    import { useCestaStore } from '../stores/cesta';
    import router from '@/router';

    const cestaStore = useCestaStore();

    onBeforeRouteUpdate(() => {
        if (cestaStore.items.length === 0) {
            router.push({ name: 'carrito' });
        }
    });

    if (cestaStore.items.length === 0) {
        router.push({ name: 'carrito' });
    }
</script>

<template>
    <div class="flex justify-center w-full text-text-light text-rubik">
        <div class="max-w-6xl w-full h-min my-auto xl:mt-40">
            <h1 class="text-center text-3xl md:text-4xl font-rubik font-bold mb-10 text-text-light text-rubik">
                Elige cómo te gustaría finalizar tu compra
            </h1>

            <div class="flex flex-col xl:flex-row gap-8 xl:gap-0 justify-between">
                <div class="w-full xl:w-5/12 flex flex-col items-center text-center p-6">
                    <h2 class="text-2xl font-semibold mb-6 text-text-light text-rubik">
                        Pasa por caja como Member para disfrutar de muchas ventajas
                    </h2>
                    <p class="text-muted-light text-lg mb-6">
                        Ser miembro te ofrece una experiencia de compra mejorada y ventajosa: facilita las transacciones al guardar tus datos y te
                        recompensa con descuentos y ofertas exclusivas por fidelidad. Además, garantiza el acceso prioritario a colecciones nuevas y
                        rebajas, permitiéndote comprar antes que el resto y gestionar tus pedidos de manera más cómoda.
                    </p>
                    <div class="w-full space-y-3">
                        <ButtonDark variant="primary" size="xl" full-width border-radius="full">Iniciar sesión</ButtonDark>
                        <ButtonDark variant="primary" size="xl" full-width border-radius="full">Unirse</ButtonDark>
                    </div>
                </div>

                <div class="w-full xl:w-2/12 flex items-center justify-center">
                    <Divider layout="vertical" class="!hidden xl:!flex"><b>O</b></Divider>
                    <Divider layout="horizontal" class="!flex xl:!hidden w-full" align="center"><b>O</b></Divider>
                </div>

                <div class="w-full xl:w-5/12 flex flex-col items-center text-center p-6">
                    <h2 class="text-2xl font-semibold mb-6 text-text-light text-rubik">Finalizar la compra como invitado</h2>
                    <p class="text-muted-light text-lg mb-4">Puedes crear un perfil Member en cualquier momento durante el proceso de compra.</p>
                    <div style="flex-grow: 0.7"></div>
                    <RouterLink :to="{ name: 'checkout' }">
                        <ButtonDark variant="primary" size="xl" full-width border-radius="full">Comprar como invitado</ButtonDark>
                    </RouterLink>
                </div>
            </div>
        </div>
    </div>
</template>
