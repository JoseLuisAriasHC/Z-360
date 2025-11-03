<script setup lang="ts">
    import { ref, watch, onMounted, onUnmounted, computed } from 'vue';
    import { useRoute } from 'vue-router';
    import { ProductoListadoService, type Producto, type ProductsResponseWrapper } from '@/modules/web/services/ProductoListadoService';
    import type { Genero, CriterioBusqueda } from '@/constants/productos';
    import CardProducto from '@web/components/CardProducto.vue';

    const route = useRoute();
    const products = ref<Producto[]>([]);
    const pagination = ref<ProductsResponseWrapper['pagination'] | null>(null);
    const currentPage = ref(1);
    const loading = ref(false);
    const hasMorePages = ref(true);
    const error = ref<string | null>(null);

    const currentGenero = computed(() => route.params.genero as Genero);
    const currentCriterio = computed(() => route.params.criterioBusqueda as CriterioBusqueda);

    const headerTitle = computed(() => {
        const genero = currentGenero.value || 'Productos';
        const criterio = currentCriterio.value || 'todos';
        const criterioDisplay = typeof criterio === 'string' ? criterio.replace('-', ' ') : criterio;
        return `Resultados para ${genero} en ${criterioDisplay}`;
    });

    const loadProductos = async (pageToLoad: number, reset: boolean = false) => {
        // Si la carga ya está en curso O no hay más páginas para cargar (y no es la primera página), salimos.
        if (loading.value || (pageToLoad > 1 && !hasMorePages.value)) return;

        loading.value = true;
        error.value = null;

        if (reset) {
            products.value = [];
            pagination.value = null;
            currentPage.value = 1;
            hasMorePages.value = true;
        }

        try {
            const response = await ProductoListadoService.getProductos(currentGenero.value, currentCriterio.value, pageToLoad);

            if (reset) {
                products.value = response.data;
            } else {
                products.value.push(...response.data);
            }

            pagination.value = response.pagination;
            currentPage.value = response.pagination.current_page;
            hasMorePages.value = response.pagination.has_more_pages;
        } catch (e) {
            console.error('Error al cargar productos:', e);
            error.value = 'No se pudieron cargar los productos. Intenta de nuevo.';
        } finally {
            loading.value = false;
        }
    };

    const loadMoreTrigger = ref<HTMLElement | null>(null);
    let observer: IntersectionObserver | null = null;

    const setupIntersectionObserver = () => {
        debugger;
        if (!loadMoreTrigger.value) return;

        observer = new IntersectionObserver(
            (entries) => {
                const target = entries[0];
                if (target.isIntersecting && hasMorePages.value && !loading.value && products.value.length > 0) {
                    loadProductos(currentPage.value + 1);
                }
            },
            {
                rootMargin: '300px',
            }
        );

        observer.observe(loadMoreTrigger.value);
    };

    onMounted(() => {
        loadProductos(1, true);
        setTimeout(setupIntersectionObserver, 100);
    });

    onUnmounted(() => {
        if (observer) {
            observer.disconnect();
        }
    });

    watch(
        () => [route.params.genero, route.params.criterioBusqueda],
        () => {
            loadProductos(1, true);
        }
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
            v-if="!loading && products.length === 0 && !error"
            class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-6 rounded-lg my-10">
            <p class="font-bold text-xl">¡Vaya!</p>
            <p>No se encontraron productos para el criterio seleccionado.</p>
        </div>

        <!-- Listado de Productos (Grid) -->
        <div v-if="products.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <CardProducto v-for="product in products" :key="product.id" :producto="product" />
        </div>

        <!-- Elemento trigger para infinite scroll -->
        <div ref="loadMoreTrigger" class="h-1"></div>

        <!-- Indicador de Carga (Loading Spinner) -->
        <div v-if="loading" class="flex justify-center items-center py-8">
            <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
            <p class="ml-3 font-semibold">Cargando más productos...</p>
        </div>

        <!-- Mensaje de Fin de Resultados -->
        <div v-if="!hasMorePages && products.length > 0 && !loading" class="text-center text-gray-500 py-10">
            <p>Has llegado al final de los resultados.</p>
        </div>
    </div>
</template>

<style scoped>
    .container {
        max-width: 100rem;
    }
</style>
