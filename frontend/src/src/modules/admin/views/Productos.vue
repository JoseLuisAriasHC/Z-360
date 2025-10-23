<script setup lang="ts">
    import { onMounted, ref } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import CrudTable from '@admin/components/CrudTable.vue';
    import { type Producto, ProductoService } from '@/modules/admin/services/ProductoService';
    import noImageSvg from '@/assets/img/no-image.svg';

    function getSeverity(status) {
        switch (status) {
            case 'unqualified':
                return 'danger';

            case 'qualified':
                return 'success';

            case 'new':
                return 'info';

            case 'negotiation':
                return 'warn';

            case 'renewal':
                return null;
        }
    }

    const backendUrl = import.meta.env.VITE_STORAGE_URL;
    const toast = useToast();
    const productos = ref<Producto[]>([]);
    const isLoading = ref(true);

    const COLUMNAS = [
        { field: 'id', header: 'ID', sortable: true },
        { field: 'nombre', header: 'Nombre', sortable: true },
        { field: 'marca', header: 'Marca', sortable: true },
        { field: 'tipo', header: 'Tipo', sortable: true },
        { field: 'genero', header: 'Genero', sortable: true },
        { field: 'cierre', header: 'Tipo de cierre', sortable: true },
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
            </CrudTable>
        </div>
    </div>
</template>
