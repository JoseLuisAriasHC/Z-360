<script setup lang="ts">
    import { onMounted, ref } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import CrudTable from '@admin/components/CrudTable.vue';
    import { type Producto, ProductoService } from '@/modules/admin/services/ProductoService';
    import noImageSvg from '@/assets/img/no-image.svg';
    import { GENEROS_VALORES, TIPOS_VALORES, type Genero, type Tipo, type Cierre, CIERRE_VALORES, SEVERITY_MAP } from '@/constants/productos';
    import { DEFAULT_SEVERITY_TAG_FILTER_ADM } from '@/constants/app';

    const backendUrl = import.meta.env.VITE_STORAGE_URL;
    const toast = useToast();
    const productos = ref<Producto[]>([]);
    const isLoading = ref(true);

    const COLUMNAS = [
        { field: 'id', header: 'ID', sortable: true },
        { field: 'nombre', header: 'Nombre', sortable: true },
        { field: 'marca', header: 'Marca', sortable: true },
        { field: 'tipo', header: 'Tipo', sortable: true, filtrable: true },
        { field: 'genero', header: 'Genero', sortable: true, filtrable: true },
        { field: 'cierre', header: 'Tipo de cierre', sortable: true, filtrable: true },
        { field: 'stock', header: 'Stock', sortable: true },
    ];

    onMounted(() => {
        fetchProductos();
    });

    const fetchProductos = async () => {
        try {
            const data = await ProductoService.getProductos();
            productos.value = [...data];
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al cargar los productos.', life: 3000 });
        } finally {
            isLoading.value = false;
        }
    };

    const handleDelete = async (id: number) => {
        try {
            const data = await ProductoService.deleteProducto(id);
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 5000 });
            isLoading.value = true;
            await fetchProductos();
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al eliminar el producto.', life: 3000 });
        }
    };

    const handleDeleteSelected = async (ids: number[]) => {
        try {
            const data = await ProductoService.deleteMultipleProductos(ids);
            isLoading.value = true;
            await fetchProductos();
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 5000 });
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al eliminar los productos seleccionadas.', life: 3000 });
        }
    };

    const handleImageError = (event: Event) => {
        const imgElement = event.target as HTMLImageElement;
        imgElement.onerror = null;
        imgElement.src = noImageSvg;
    };

    function getSeverity(type: Genero | Tipo | Cierre): string {
        return SEVERITY_MAP[type as keyof typeof SEVERITY_MAP] || DEFAULT_SEVERITY_TAG_FILTER_ADM;
    }
</script>

<template>
    <div class="grid">
        <div class="col-12">
            <CrudTable
                :data="productos"
                entityName="Producto"
                :columns="COLUMNAS"
                newRouteName="admin-productos-new"
                editRouteName="admin-productos-edit"
                @delete-item="handleDelete"
                @delete-selected="handleDeleteSelected"
                :loading="isLoading">
                <template #marca="{ data }">
                    <img
                        :src="backendUrl + '/' + data.marca.logo"
                        :alt="data.nombre + ' Logo'"
                        class="rounded"
                        style="width: 78px; height: 78px; object-fit: contain"
                        v-tooltip="data.marca.nombre"
                        @error="handleImageError" />
                </template>

                <template #tipo="{ data }">
                    <Tag :value="data.tipo" :severity="getSeverity(data.tipo)" />
                </template>

                <template #filter-tipo="{ filterModel }">
                    <Select v-model="filterModel.value" :options="TIPOS_VALORES" placeholder="Seleccionar Tipo" showClear class="w-full">
                        <template #option="slotProps">
                            <Tag :value="slotProps.option" :severity="getSeverity(slotProps.option)" />
                        </template>
                    </Select>
                </template>

                <template #genero="{ data }">
                    <Tag :value="data.genero" :severity="getSeverity(data.genero)" />
                </template>

                <template #filter-genero="{ filterModel }">
                    <Select v-model="filterModel.value" :options="GENEROS_VALORES" placeholder="Seleccionar Género" showClear class="w-full">
                        <template #option="slotProps">
                            <Tag :value="slotProps.option" :severity="getSeverity(slotProps.option)" />
                        </template>
                    </Select>
                </template>

                <template #cierre="{ data }">
                    <Tag :value="data.cierre" :severity="getSeverity(data.cierre)" />
                </template>

                <template #filter-cierre="{ filterModel }">
                    <Select v-model="filterModel.value" :options="CIERRE_VALORES" placeholder="Seleccionar Cierre" showClear class="w-full">
                        <template #option="slotProps">
                            <Tag :value="slotProps.option" :severity="getSeverity(slotProps.option)" />
                        </template>
                    </Select>
                </template>
            </CrudTable>
        </div>
    </div>
</template>
