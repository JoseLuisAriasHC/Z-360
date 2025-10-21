<script setup lang="ts">
    import { onMounted, ref } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import CrudTable from '@admin/components/CrudTable.vue';
    import { type Talla, TallaService } from '@/modules/admin/services/TallaService';

    const tallas = ref<Talla[]>([]);
    const toast = useToast();
    const isLoading = ref(true);

    const MARCA_COLUMNS = [
        { field: 'id', header: 'ID', sortable: true },
        { field: 'numero', header: 'Numero', sortable: true },
    ];

    onMounted(() => {
        fetchTallas();
    });

    const fetchTallas = async () => {
        try {
            const data = await TallaService.getTallas();
            tallas.value = [...data];
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al cargar las tallas.', life: 3000 });
        } finally {
            isLoading.value = false;
        }
    };

    const handleDelete = async (id: number) => {
        try {
            const data = await TallaService.deleteTalla(id);
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 5000 });
            isLoading.value = true;
            await fetchTallas();
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al eliminar la talla.', life: 3000 });
        }
    };

    const handleDeleteSelected = async (ids: number[]) => {
        try {
            const data = await TallaService.deleteMultipleTallas(ids);
            isLoading.value = true;
            await fetchTallas();
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 5000 });
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al eliminar las tallas seleccionadas.', life: 3000 });
        }
    };
</script>

<template>
    <div class="grid">
        <div class="col-12">
            <CrudTable
                :data="tallas"
                entityName="Talla"
                :columns="MARCA_COLUMNS"
                newRouteName="admin-tallas-new"
                editRouteName="admin-tallas-edit"
                @delete-item="handleDelete"
                @delete-selected="handleDeleteSelected"
                :loading="isLoading">
                <template #numero="{ data }">
                    {{ Number(data.numero).toFixed(2) }}
                </template>
            </CrudTable>
        </div>
    </div>
</template>
