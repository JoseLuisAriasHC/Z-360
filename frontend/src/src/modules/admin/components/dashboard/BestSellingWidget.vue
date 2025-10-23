<script setup lang="ts">
import { ref, onMounted } from 'vue';
import apiClient from '@/services/api';

// Interfaz para definir la estructura de los datos del backend
interface TopProduct {
    id: number;
    nombre: string;
    tipo_producto: string;
    total_vendido: number;
    porcentaje_tipo: number;
}

// Estado reactivo
const topProducts = ref<TopProduct[] | null>(null);
const loading = ref(true);
const error = ref<string | null>(null);

// Helper para asignar colores Tailwind basados en el tipo de producto
// Esto es para que el color de la barra de progreso y el texto coincidan
const getColorClasses = (tipo: string) => {
    switch (tipo.toLowerCase()) {
        case 'ropa':
            return { bar: 'bg-orange-500', text: 'text-orange-500' };
        case 'deportivas':
            return { bar: 'bg-cyan-500', text: 'text-cyan-500' };
        case 'botas':
            return { bar: 'bg-pink-500', text: 'text-pink-500' };
        case 'sandalias':
            return { bar: 'bg-green-500', text: 'text-green-500' };
        default:
            return { bar: 'bg-indigo-500', text: 'text-indigo-500' };
    }
};

// 4. Función para cargar los datos
const fetchTopProducts = async () => {
    loading.value = true;
    error.value = null;
    try {
        const response = await apiClient.get('/admin/dashboard/top-ventas');
        
        if (response.data && Array.isArray(response.data.data)) {
            topProducts.value = response.data.data as TopProduct[];
        } else {
            topProducts.value = [];
        }
    } catch (e) {
        error.value = 'No se pudo cargar el listado de productos más vendidos.';
        topProducts.value = [];
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchTopProducts();
});
</script>

<template>
    <div class="card">
        <div class="flex justify-between items-center mb-6">
            <div class="font-semibold text-xl">Top Productos Más Vendidos</div>
        </div>

        <div v-if="loading" class="text-center p-8">
            Cargando el top de productos...
        </div>

        <div v-else-if="error" class="text-center p-8 text-red-500">
            {{ error }}
        </div>

        <div v-else-if="!topProducts || topProducts.length === 0" class="text-center p-8 text-muted-color">
            <p>No hay datos de ventas en los últimos 3 meses.</p>
        </div>

        <ul v-else class="list-none p-0 m-0">
            <li v-for="product in topProducts" :key="product.id"
                class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                
                <!-- Nombre y Tipo -->
                <div>
                    <span class="text-surface-900 dark:text-surface-0 font-medium mr-2 mb-1 md:mb-0">{{ product.nombre }}</span>
                    <div class="mt-1 text-muted-color">{{ product.tipo_producto }}</div>
                </div>

                <!-- Barra de Progreso y Porcentaje -->
                <div class="mt-2 md:mt-0 flex items-center">
                    <div class="bg-surface-300 dark:bg-surface-500 rounded-border overflow-hidden w-40 lg:w-24" style="height: 8px">
                        <div :class="[getColorClasses(product.tipo_producto).bar, 'h-full']" 
                             :style="{ width: product.porcentaje_tipo + '%' }">
                        </div>
                    </div>
                    <!-- Porcentaje -->
                    <span :class="[getColorClasses(product.tipo_producto).text, 'ml-4 font-medium']">{{ product.porcentaje_tipo }}%</span>
                </div>
            </li>
        </ul>
    </div>
</template>
