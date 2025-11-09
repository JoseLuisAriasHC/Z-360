<script setup lang="ts">
    import { computed } from 'vue';
    import { useCestaStore } from '../../stores/cesta';
    import { useCuponStore } from '../../stores/cupon';
    import { formatPrice } from '@/utils/utils';
    import { useSettingsStore } from '@/stores/settings';

    const cestaStore = useCestaStore();
    const cuponStore = useCuponStore();
    const settings = useSettingsStore();

    const envioGratisDesde = computed<number>(() => {
        settings.envioSettings?.free_coste_envio_from ?? 0;
    });

    const descuentoCupon = computed(() => {
        if (!cuponStore.cupon) return 0;

        if (cuponStore.tipo === 'porcentaje') {
            return (subtotal.value * cuponStore.descuento) / 100;
        }

        return cuponStore.descuento;
    });

    const envioGratis = computed<boolean>(() => {
        if (!settings.envioSettings) return false;
        return cestaStore.total - descuentoCupon.value >= settings.envioSettings.free_coste_envio_from;
    });

    const costeEnvio = computed(() => {
        if (!settings.envioSettings) return 0;
        return envioGratis.value ? 0 : settings.envioSettings.coste_envio;
    });

    const totalMasEnvioCupon = computed(() => {
        return cestaStore.total - descuentoCupon.value + costeEnvio.value;
    });

    const subtotal = computed(() => cestaStore.total);
    const subtotalConDescuento = computed(() => {
        return Math.max(0, subtotal.value - descuentoCupon.value);
    });
</script>

<template>
    <div class="order-summary font-rubik">
        <h3 class="text-2xl font-semibold mb-4 font-rubik">Resumen del pedido</h3>

        <!-- Lista de productos -->
        <div class="products-list mb-4">
            <div v-for="item in cestaStore.items" :key="`${item.id}-${item.idTalla}`" class="product-item">
                <div class="flex gap-3">
                    <div class="product-image">
                        <img :src="item.imagen" :alt="item.nombre" />
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold line-clamp-2">{{ item.nombre }}</p>
                        <p class="text-sm text-gray-500">{{ item.marca }}</p>
                        <p class="text-sm text-gray-500">Talla: {{ item.talla }} | Color: {{ item.color }}</p>
                        <p class="font-medium mt-1">{{ formatPrice(item.precio_con_descuento) }}€ x {{ item.cantidad }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">{{ formatPrice(item.precio_con_descuento * item.cantidad) }}€</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Separador -->
        <div class="border-t border-gray-200 my-4"></div>

        <!-- Cálculos -->
        <div class="calculations space-y-2">
            <div class="flex justify-between text-sm">
                <span>Subtotal</span>
                <span>{{ formatPrice(subtotal) }}€</span>
            </div>

            <!-- Descuento por cupón -->
            <div v-if="descuentoCupon > 0" class="flex justify-between text-sm text-green-600">
                <span>
                    Descuento
                    <span class="font-semibold">({{ cuponStore.cupon?.codigo }})</span>
                </span>
                <span>-{{ formatPrice(descuentoCupon) }}€</span>
            </div>

            <!-- Envío -->
            <div class="flex justify-between text-sm">
                <span>Envío</span>
                <span v-if="envioGratis" class="text-green-600 font-semibold">¡Gratis!</span>
                <span v-else>{{ formatPrice(costeEnvio) }}€</span>
            </div>

            <!-- Mensaje de envío gratis -->
            <div v-if="!envioGratis && subtotalConDescuento < envioGratisDesde" class="text-xs text-gray-500 py-2">
                Añade {{ formatPrice(envioGratisDesde - subtotalConDescuento) }}€ más para envío gratis
            </div>
        </div>

        <!-- Separador -->
        <div class="border-t border-gray-300 my-4"></div>

        <!-- Total -->
        <div class="flex justify-between text-lg font-bold">
            <span>Total</span>
            <span>{{ formatPrice(totalMasEnvioCupon) }}€</span>
        </div>

        <!-- Nota IVA -->
        <p class="text-xs text-gray-500 mt-2">IVA incluido</p>
    </div>
</template>

<style scoped>
    .order-summary {
        @apply bg-white rounded-lg p-6 shadow-sm border border-gray-200;
        @apply sticky top-32;
    }

    .products-list {
        @apply max-h-96 overflow-y-auto;
    }

    .product-item {
        @apply py-3 border-b border-gray-100 last:border-b-0;
    }

    .product-image {
        @apply w-[96px] h-[96px] bg-gray-100 rounded overflow-hidden flex-shrink-0;
    }

    .product-image img {
        @apply w-full h-full object-contain;
    }

    .calculations {
        @apply font-rubik;
    }
</style>
