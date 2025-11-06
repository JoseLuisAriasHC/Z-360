<script setup lang="ts">
    import { computed } from 'vue';
    import router from '@/router';
    import ButtonDark from '../ButtonDark.vue';
    import { useCestaStore } from '../../stores/cesta';
    import { useSettingsStore } from '@/stores/settings';

    // Props y emits
    const props = defineProps<{ visible: boolean }>();
    const emit = defineEmits(['update:visible']);

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

    const totalConEnvio = computed(() => cesta.total + costeEnvio.value);

    // Cerrar modal
    const closeModal = () => {
        emit('update:visible', false);
    };

    // Cantidad total
    const totalItemsText = computed(() => {
        const n = cesta.totalItems;
        return n === 1 ? '1 artículo' : `${n} artículos`;
    });
</script>

<template>
    <Drawer
        v-model:visible="props.visible"
        @update:visible="closeModal"
        header="#"
        position="right"
        :pt="{
            root: { style: 'width: 33rem;' },
            title: { style: 'opacity: 0;' },
        }">
        <!-- Mensaje de añadido -->
        <div class="flex border-2 px-6 py-4 rounded-lg border-verde">
            <i class="pi pi-check-circle mr-2 text-verde" style="font-size: 1.5rem" />
            El artículo se ha añadido a la cesta de la compra.
        </div>

        <div class="mt-6 font-rubik">
            <div class="text-center font-semibold text-2xl mb-2">Tu cesta de la compra</div>

            <!-- Lista de productos -->
            <div class="cesta-container pb-4 border-b-2">
                <div v-for="item in cesta.items" :key="item.id + item.talla" class="flex gap-4 my-4">
                    <div style="height: 128px; width: 128px">
                        <img :src="item.imagen" alt="imagen producto" class="w-full h-full object-cover rounded-lg border" />
                    </div>

                    <div class="flex-1">
                        <div class="text-lg mb-1 text-rojo font-bold">
                            {{ item.precio_con_descuento }} €
                            <span v-if="item.descuento_activo" class="text-muted-light line-through ml-2 font-normal">{{ item.precio }} €</span>
                        </div>
                        <p class="text-lg leading-5 capitalize text-muted-light">
                            {{ item.marca }}
                        </p>
                        <p class="text-lg leading-5 capitalize font-semibold mb-4">
                            {{ item.nombre }}
                        </p>
                        <p class="text-md leading-5 capitalize">
                            <span class="text-muted-light">Color:</span>
                            {{ item.color }}
                        </p>
                        <p class="text-md leading-5">
                            <span class="text-muted-light">Talla:</span>
                            {{ item.talla }}
                        </p>
                        <p class="text-md leading-5">
                            <span class="text-muted-light">Cantidad:</span>
                            {{ item.cantidad }}
                        </p>
                    </div>
                </div>

                <div v-if="cesta.items.length === 0" class="text-center py-6 text-muted-light">Tu cesta está vacía.</div>
            </div>

            <!-- Mensaje de aviso -->
            <div class="my-4 text-sm">
                <i class="pi pi-info-circle mr-2"></i>
                Los artículos de la cesta de la compra no están reservados.
            </div>

            <!-- Envío gratuito -->
            <div v-if="envioGratis" class="content-center px-6 py-4 rounded-lg bg-verde-claro">
                <i class="pi pi-box text-verde mr-2" style="font-size: 1.2rem" />
                Envío gratuito conseguido
            </div>

            <div class="py-4 border-b-2 font-semibold text-lg">
                {{ totalItemsText }}
            </div>

            <!-- Totales -->
            <div class="py-4 border-b-2 font-semibold text-lg">
                <div class="flex justify-between">
                    <div>Importe total (IVA incluido)</div>
                    <div>{{ totalConEnvio.toFixed(2) }} €</div>
                </div>
                <div v-if="envioGratis" class="text-verde">Envío gratis</div>
                <div v-else class="text-muted-light">Coste de envío: {{ costeEnvio.toFixed(2) }} €</div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col gap-4 pt-4">
                <ButtonDark variant="primary" size="xl" full-width>Caja</ButtonDark>
                <RouterLink :to="{ name: 'carrito' }">
                    <ButtonDark variant="secondary" full-width>Cesta de la compra</ButtonDark>
                </RouterLink>
            </div>
        </div>
    </Drawer>
</template>

<style scoped>
    .cesta-container {
        max-height: 27rem;
        overflow-y: scroll;
        scrollbar-width: none;
    }
</style>
