<script setup lang="ts">
    import { inject, onMounted, ref, type Ref } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import CrudTable from '@admin/components/CrudTable.vue';
    import { type VarianteProductoVM, VarianteProductoService } from '@/modules/admin/services/VarianteProductoService';
    import { handleImageError } from '@/utils/utils';

    const toast = useToast();

    const backendUrl = `${import.meta.env.VITE_STORAGE_URL}/product_variants/S_`;
    const props = defineProps<{
        productoId: number | null;
    }>();
    const productoValido = inject<Ref<boolean>>('productoValido', ref(true));

    const variantes = ref<VarianteProductoVM[]>([]);
    const isLoading = ref(true);

    const COLUMNAS = [
        { field: 'id', header: 'ID', sortable: true },
        { field: 'imagen_principal', header: 'Imagen principal', sortable: false },
        { field: 'color', header: 'Color', sortable: true },
        { field: 'precio', header: 'Precio', sortable: true },
        { field: 'stock', header: 'Stock', sortable: true },
        { field: 'descuento', header: 'Descuento', sortable: true },
        { field: 'descuento_activo', header: 'Descuato activo', sortable: true },
    ];

    const loadVariantesData = async (idProducto: number) => {
        try {
            const data = await VarianteProductoService.getVariantes(idProducto);
            variantes.value = [...data];
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al cargar las variantes del producto.', life: 3000 });
        } finally {
            isLoading.value = false;
        }
    };

    const handleDelete = async (id: number) => {
        try {
            if (props.productoId) {
                const data = await VarianteProductoService.deleteVariante(id);
                toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 5000 });
                isLoading.value = true;
                await loadVariantesData(props.productoId);
            }
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al eliminar la variante del producto.', life: 3000 });
        }
    };

    const handleDeleteSelected = async (ids: number[]) => {
        try {
            if (props.productoId) {
                const data = await VarianteProductoService.deleteMultipleVariantes(ids);
                isLoading.value = true;
                await loadVariantesData(props.productoId);
                toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 5000 });
            }
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al eliminar las variantes seleccionadas', life: 3000 });
        }
    };

    const getSeverity = (stock: number): string => {
        if (stock < 10) return 'danger';
        else if (stock >= 10 && stock < 20) return 'warn';
        else return 'success';
    };

    onMounted(() => {
        if (productoValido && props.productoId) {
            loadVariantesData(props.productoId);
        }
    });

    defineExpose({
        reloadVariantesData: loadVariantesData,
    });
</script>

<template>
    <div class="grid">
        <div class="col-12">
            <ScrollPanel
                style="width: 100%; height: 70vh"
                :dt="{
                    bar: {
                        background: '{primary.color}',
                    },
                }">
                <CrudTable
                    :data="variantes"
                    entityName="Variante"
                    :columns="COLUMNAS"
                    editRouteName="admin-productos-variantes-edit"
                    @delete-item="handleDelete"
                    @delete-selected="handleDeleteSelected"
                    :loading="isLoading">
                    <template #imagen_principal="{ data }">
                        <img
                            :src="backendUrl + data.imagen_principal"
                            :alt="data.nombre + ' Imagen'"
                            class="rounded"
                            style="width: 78px; height: 78px; object-fit: contain"
                            @error="handleImageError" />
                    </template>

                    <template #color="{ data }">
                        <div class="flex items-center space-x-2">
                            <div class="w-7 h-7 rounded-lg flex-shrink-0" :style="{ backgroundColor: data.color.codigo_hex }"></div>
                            <span>{{ data.color.nombre }}</span>
                        </div>
                    </template>

                    <template #precio="{ data }">
                        {{ Number(data.precio).toFixed(2) }}
                    </template>

                    <template #stock="{ data }">
                        <Tag :severity="getSeverity(data.stock)" :value="data.stock" />
                    </template>

                    <template #descuento="{ data }">
                        {{ Number(data.descuento).toFixed(2) }}
                    </template>

                    <template #descuento_activo="{ data }">
                        <i v-if="data.descuento_activo" class="pi pi-check text-green-500"></i>
                        <i v-else class="pi pi-times text-rose-500 justify-self-center"></i>
                    </template>
                </CrudTable>
            </ScrollPanel>
        </div>
    </div>
</template>
