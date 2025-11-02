<script setup lang="ts">
    import { computed, ref } from 'vue';
    import type { Producto, VarianteProducto } from '@web/services/ProductoService';

    const props = defineProps<{
        producto: Producto;
    }>();

    // --- Propiedades Computadas y Estado ---

    // Objeto de la variante principal (posición 0) para inicialización. Siempre será null si no hay variantes.
    const mainVariant = computed(() => (props.producto.variants.length > 0 ? props.producto.variants[0] : null));

    // Lista de todas las variantes
    const allVariants = computed(() => props.producto.variants);

    // Estado para almacenar el ID de la variante activa. Se inicializa con el ID de la primera variante.
    // Usamos -1 como fallback seguro para indicar "ninguna variante".
    const activeVariantId = ref(mainVariant.value?.id || -1);

    // Propiedad computada para encontrar la variante activa actual (CORREGIDA LA INFERENCIA DE TIPO)
    const activeVariant = computed<VarianteProducto | null>(() => {
        // 1. Buscamos la variante por el ID activo.
        const foundVariant = props.producto.variants.find((v) => v.id === activeVariantId.value);

        // 2. Si se encuentra, la retornamos.
        if (foundVariant) {
            return foundVariant;
        }

        // 3. Si no se encuentra (activeVariantId = -1 o error), volvemos a la principal.
        return mainVariant.value;
    });

    // Propiedad computada para la URL de la imagen que se muestra
    const currentImage = computed(() => {
        return activeVariant.value?.imagen_principal;
    });

    // --- Handler de Interacción ---

    /**
     * Establece el ID de la variante activa.
     * @param variantId El ID de la variante.
     */
    const setActiveVariant = (variantId: number) => {
        activeVariantId.value = variantId;
    };
</script>

<template>
    <!-- 
        El card entero es un RouterLink. 
        Importante: Usamos el ID de la variante activa (activeVariant.id) en el link.
    -->
    <RouterLink
        v-if="activeVariant"
        :to="{ name: 'producto-detalles', params: { id: activeVariant.id.toString() } }"
        class="card-producto bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden cursor-pointer transform hover:scale-[1.01] block">
        <!-- Tarjeta de Producto -->
        <div class="card-img relative w-full bg-gray-100 flex items-center justify-center">
            <img
                :src="currentImage"
                :alt="props.producto.nombre"
                class="object-cover w-full h-full transition-transform duration-500 hover:scale-105" />
            <div
                v-if="props.producto.etiquetas && props.producto.etiquetas.length"
                class="dark:bg-surface-900 absolute rounded-border"
                style="left: 5px; top: 5px">
                <div class="flex flex-col gap-2">
                    <Tag v-for="(etiqueta, index) in props.producto.etiquetas" :value="etiqueta.nombre" severity="secondary" rounded />
                </div>
            </div>
        </div>

        <div class="p-4 pt-1">
            <span class="text-sm font-semibold text-gray-500 capitalize">{{ props.producto.marca.nombre }}</span>

            <!-- Selectores de color -->
            <div v-if="allVariants.length > 1" class="mt-3 flex space-x-2 justify-start mb-3">
                <div
                    v-for="variant in allVariants"
                    :key="variant.id"
                    :style="{ backgroundColor: variant.color.codigo_hex }"
                    class="w-5 h-5 rounded-full border-2 border-white ring-2 shadow-sm transition-transform duration-200 cursor-pointer hover:ring-naranja hover:scale-110"
                    :class="{
                        'ring-naranja ring-4': variant.id === activeVariantId,
                        'ring-gray-300': variant.id !== activeVariantId,
                    }"
                    @mouseover.prevent="setActiveVariant(variant.id)"
                    :title="variant.color.nombre"
                    aria-label="Seleccionar color"></div>
            </div>
            <h3 class="text-lg text-background-dark font-bold truncate mt-1 capitalize" :title="props.producto.nombre">
                {{ props.producto.nombre }}
            </h3>
            <p class="text-lg text-muted-dark font-semibold capitalize">{{ props.producto.tipo }} - {{ props.producto.genero }}</p>
            <template v-if="activeVariant">
                <!-- Mostrar el precio con descuento si aplica -->
                <div v-if="activeVariant.descuento_activo" class="mt-2 flex items-center">
                    <span class="text-lg font-bold text-red-600 mr-2">{{ activeVariant.precio_con_descuento.toFixed(2) }} €</span>
                    <span class="text-sm text-gray-400 line-through">{{ activeVariant.precio.toFixed(2) }} €</span>
                </div>
                <!-- Mostrar el precio normal -->
                <p v-else class="text-lg text-background-dark font-bold mt-2">{{ activeVariant.precio.toFixed(2) }} €</p>
            </template>
        </div>
    </RouterLink>

    <!-- Fallback si no hay variantes para asegurar la renderización base -->
    <div v-else class="card-producto bg-white rounded-xl shadow-lg p-4">
        <span class="text-gray-500 text-sm">Producto sin variantes.</span>
    </div>
</template>

<style scoped>
    /* Aseguramos que RouterLink se comporte como un bloque para ocupar el espacio */
    .card-producto {
        height: 25rem;
        display: block; /* Importante para RouterLink */
    }

    .card-img {
        height: 13rem;
    }

    @media (min-width: 1280px) {
        .card-producto {
            height: 45rem;
        }
        .card-img {
            height: 70%;
        }
    }
</style>
