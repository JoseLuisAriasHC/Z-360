<script setup lang="ts">
    import { ref, watch, onMounted, onUnmounted, computed } from 'vue';
    import { useRoute } from 'vue-router';
    // Asumo que estas importaciones son correctas en tu proyecto
    import { ProductService, type Producto, type ProductsResponseWrapper } from '@web/services/ProductoService';
    import type { Genero, CriterioBusqueda } from '@/constants/productos';
    import CardProducto from '@web/components/CardProducto.vue';

    // --- Estado Reactivo ---
    const route = useRoute();
    const products = ref<Producto[]>([]);
    const pagination = ref<ProductsResponseWrapper['pagination'] | null>(null);
    const currentPage = ref(1);
    const isLoading = ref(false);
    const hasMorePages = ref(true);
    const error = ref<string | null>(null);

    // --- Propiedades Computadas para Parámetros de Ruta y Título ---

    /** Mapeo de parámetros de ruta a tipos estrictos de forma reactiva. */
    const currentGenero = computed(() => route.params.genero as Genero);
    const currentCriterio = computed(() => route.params.criterioBusqueda as CriterioBusqueda);

    const headerTitle = computed(() => {
        const genero = currentGenero.value || 'Productos';
        const criterio = currentCriterio.value || 'todos';
        const criterioDisplay = typeof criterio === 'string' ? criterio.replace('-', ' ') : criterio;
        return `Resultados para ${genero} en ${criterioDisplay}`;
    });

    // --- Lógica de Carga de Datos ---

    /**
     * Carga productos de la API para la página actual.
     * @param pageToLoad Número de página a cargar.
     * @param reset Si es verdadero, vacía la lista de productos antes de cargar (para nuevas búsquedas).
     */
    const loadProducts = async (pageToLoad: number, reset: boolean = false) => {
        // Si la carga ya está en curso O no hay más páginas para cargar (y no es la primera página), salimos.
        if (isLoading.value || (pageToLoad > 1 && !hasMorePages.value)) return;

        isLoading.value = true;
        error.value = null;

        if (reset) {
            products.value = [];
            pagination.value = null;
            currentPage.value = 1;
            hasMorePages.value = true;
            // Opcional: Podrías añadir un estado temporal aquí para mostrar un esqueleto mientras se carga la primera página de la nueva ruta.
        }

        try {
            // Usamos los valores reactivos (computed)
            const response = await ProductService.getProducts(currentGenero.value, currentCriterio.value, pageToLoad);

            // Actualizar el estado
            if (reset) {
                products.value = response.data;
            } else {
                // Concatenar nuevos productos para scroll infinito
                products.value.push(...response.data);
            }

            console.log(products);

            pagination.value = response.pagination;
            currentPage.value = response.pagination.current_page;
            hasMorePages.value = response.pagination.has_more_pages;
        } catch (e) {
            console.error('Error al cargar productos:', e);
            error.value = 'No se pudieron cargar los productos. Intenta de nuevo.';
        } finally {
            isLoading.value = false;
        }
    };

    /**
     * Función para cargar la siguiente página, utilizada por el scroll handler.
     */
    const loadNextPage = () => {
        if (hasMorePages.value && !isLoading.value) {
            loadProducts(currentPage.value + 1);
        }
    };

    // --- Lógica de Scroll Infinito ---

    /**
     * Handler que se ejecuta al hacer scroll en la ventana.
     * Llama a loadNextPage si se está cerca del final de la página.
     */
    const handleScroll = () => {
        // Calcular si el usuario está cerca del final de la página (e.g., a 200px del final)
        // Se usa 200px como margen para empezar a cargar antes de llegar al final
        const nearBottom = window.innerHeight + window.scrollY >= document.documentElement.offsetHeight - 200;

        if (nearBottom) {
            loadNextPage();
        }
    };

    // --- Ciclo de Vida y Observadores ---

    // 1. Cargar la primera página al montar el componente
    onMounted(() => {
        // Añadir listener de scroll para infinite scrolling
        window.addEventListener('scroll', handleScroll);
        loadProducts(1, true);
    });

    // 2. Limpiar listener de scroll al desmontar
    onUnmounted(() => {
        window.removeEventListener('scroll', handleScroll);
    });

    // 3. Observar los parámetros de ruta para recargar la lista
    watch(
        // Observamos un array de las props que nos interesan
        () => [route.params.genero, route.params.criterioBusqueda],
        () => {
            // Cuando los parámetros cambien, reiniciamos la lista (reset: true) y cargamos la página 1.
            loadProducts(1, true);
        }
        // No necesitamos immediate: true porque onMounted ya llama a loadProducts(1, true)
    );
</script>

<template>
    <div class="container mx-auto p-4 md:p-8 min-h-screen">
        <!-- Título del listado -->
        <h1 class="text-3xl font-extrabold text-gray-800 mb-6 border-b pb-2">{{ headerTitle }}</h1>

        <!-- Mensaje de Error -->
        <div v-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg" role="alert">
            <p class="font-bold">Error de Carga</p>
            <p>{{ error }}</p>
        </div>

        <!-- Mensaje de Sin Productos -->
        <!-- Condición: NO está cargando, la lista está vacía, Y no hay error -->
        <div
            v-if="!isLoading && products.length === 0 && !error"
            class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-6 rounded-lg my-10">
            <p class="font-bold text-xl">¡Vaya!</p>
            <p>No se encontraron productos para el criterio seleccionado.</p>
        </div>

        <!-- Listado de Productos (Grid) -->
        <div v-if="products.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <CardProducto v-for="product in products" :key="product.id" :producto="product" />
        </div>

        <!-- Indicador de Carga (Loading Spinner) -->
        <div v-if="isLoading" class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-t-2 border-indigo-600"></div>
            <p class="ml-3 text-indigo-600 font-semibold">Cargando más productos...</p>
        </div>

        <!-- Mensaje de Fin de Resultados -->
        <div v-if="!hasMorePages && products.length > 0 && !isLoading" class="text-center text-gray-500 py-10">
            <p>Has llegado al final de los resultados.</p>
        </div>
    </div>
</template>

<style scoped>
    /* Estilos específicos para la vista si son necesarios */
    .container {
        max-width: 1400px;
    }

</style>
