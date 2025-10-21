<script setup lang="ts">
    import { onMounted, ref } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import CrudTable from '@admin/components/CrudTable.vue';
    import { type Color, ColorService } from '@/modules/admin/services/ColorService';

    const colores = ref<Color[]>([]);
    const toast = useToast();
    const isLoading = ref(true);

    const COLUMNAS = [
        { field: 'id', header: 'ID', sortable: true },
        { field: 'nombre', header: 'Numero', sortable: true },
        { field: 'codigo_hex', header: 'Codigo Hex', sortable: true },
    ];

    onMounted(() => {
        fetchColores();
    });

    const fetchColores = async () => {
        try {
            const data = await ColorService.getColores();
            colores.value = [...data];
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al cargar las colores.', life: 3000 });
        } finally {
            isLoading.value = false;
        }
    };

    const handleDelete = async (id: number) => {
        try {
            const data = await ColorService.deleteColor(id);
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 5000 });
            isLoading.value = true;
            await fetchColores();
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al eliminar la talla.', life: 3000 });
        }
    };

    const handleDeleteSelected = async (ids: number[]) => {
        try {
            debugger;
            const data = await ColorService.deleteMultipleColores(ids);
            isLoading.value = true;
            await fetchColores();
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 5000 });
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al eliminar las colores seleccionadas.', life: 3000 });
        }
    };
</script>

<template>
    <div class="grid">
        <div class="col-12">
            <CrudTable
                :data="colores"
                entityName="Color"
                :columns="COLUMNAS"
                newRouteName="admin-colores-new"
                editRouteName="admin-colores-edit"
                @delete-item="handleDelete"
                @delete-selected="handleDeleteSelected"
                :loading="isLoading">
                <template #codigo_hex="{ data }">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-lg flex-shrink-0" :style="{ backgroundColor: data.codigo_hex }"></div>
                        <span>{{ data.codigo_hex }}</span>
                    </div>
                </template>
            </CrudTable>
        </div>
    </div>
</template>
