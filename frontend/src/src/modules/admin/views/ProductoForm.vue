<script setup lang="ts">
    import { useToast } from 'primevue/usetoast';
    import { useRouter } from 'vue-router';
    import AjustesBasicos from '../components/producto/AjustesBasicos.vue';
    import Detalles from '../components/producto/Detalles.vue';
    import EtiquetasTiposUso from '../components/producto/EtiquetasTiposUso.vue';
    import { getParamId } from '@/utils/utils';
    import { computed, onMounted, ref, provide } from 'vue';
    import { ProductoService, type Producto } from '../services/ProductoService';
    import VariantesProducto from '../components/producto/VariantesProducto.vue';

    const router = useRouter();
    const toast = useToast();

    const productoId = getParamId();
    const isEditMode = computed(() => productoId.value !== null);

    interface ProductoFormState extends Omit<Producto, 'id'> {}

    const loading = ref(true);
    const productoValido = ref(false);
    const productoState = ref<ProductoFormState & { id?: number }>({
        id: productoId.value || undefined,
        nombre: '',
        marca_id: 0,
        tipo: 'urbanas',
        descripcion: '',
        cierre: 'Cordones',
        altura_suela: 'media',
        plantilla: '',
        genero: 'unisex',
    });

    provide('productoValido', productoValido);

    const fetchProductoData = async (id: number) => {
        loading.value = true;
        try {
            const data = await ProductoService.getProducto(id);
            productoState.value = data;
            productoValido.value = true; // Marcar como válido
        } catch (error) {
            productoValido.value = false;
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'No se ha encontrado el producto solicitado',
                life: 5000,
            });
            router.push({ name: 'admin-not-found' });
        } finally {
            loading.value = false;
        }
    };

    onMounted(async () => {
        if (productoId.value) {
            await fetchProductoData(productoId.value);
        } else {
            loading.value = false;
            productoValido.value = true;
        }
    });

    const goBack = () => {
        router.push({ name: 'admin-productos' });
    };
</script>

<template>
    <div class="card">
        <div class="col-span-12 xl:col-span-12">
            <div class="font-semibold text-xl flex items-center justify-between">
                <h5 class="text-xl font-bold">
                    {{ isEditMode ? 'Editar Producto' : 'Crear Nuevo Producto' }}
                </h5>
                <Button icon="pi pi-arrow-left" label="Volver" severity="secondary" @click="goBack" :fluid="false" />
            </div>
        </div>

        <!-- Mostrar loader mientras carga -->
        <div v-if="loading" class="text-center py-8">
            <i class="pi pi-spin pi-spinner text-4xl text-primary"></i>
            <p class="mt-4 text-muted-color">Cargando producto...</p>
        </div>

        <!-- Mostrar contenido solo si terminó de cargar y es válido -->
        <Tabs v-else-if="productoValido" value="general" scrollable lazy>
            <TabList>
                <Tab value="general">Ajustes básicos</Tab>
                <Tab value="variantes_producto" :disabled="!isEditMode">Variantes del producto</Tab>
                <Tab value="detalles" :disabled="!isEditMode">Detalles del producto</Tab>
                <Tab value="etiquetas_tipos_uso" :disabled="!isEditMode">Etiquetas y Tipos de Uso</Tab>
            </TabList>
            <TabPanels>
                <AjustesBasicos :producto-state="productoState" :is-edit-mode="isEditMode" />
                <VariantesProducto :producto-id="productoId" :disabled="!isEditMode" />
                <Detalles :producto-id="productoId" :disabled="!isEditMode" />
                <EtiquetasTiposUso :producto-id="productoId" :disabled="!isEditMode" />
            </TabPanels>
        </Tabs>

        <!-- Mensaje si el producto no es válido (opcional, porque se redirige) -->
        <div v-else class="text-center py-8">
            <i class="pi pi-exclamation-triangle text-4xl text-red-500"></i>
            <p class="mt-4 text-muted-color">Producto no encontrado. Redirigiendo...</p>
        </div>
    </div>
</template>
