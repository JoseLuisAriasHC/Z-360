<script setup lang="ts">
    import { onMounted, ref } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import CrudTable from '@admin/components/CrudTable.vue';
    import { type TiposUso, TiposUsoService } from '@/modules/admin/services/TiposUsoService';

    const tiposUsos = ref<TiposUso[]>([]);
    const toast = useToast();
    const isLoading = ref(true);

    const COLUMNAS = [
        { field: 'id', header: 'ID', sortable: true },
        { field: 'nombre', header: 'Nombre', sortable: true },
    ];

    onMounted(() => {
        fetchTiposUsos();
    });

    const fetchTiposUsos = async () => {
        try {
            const data = await TiposUsoService.getTiposUsos();
            tiposUsos.value = [...data];
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al cargar los tipos de usos.', life: 3000 });
        } finally {
            isLoading.value = false;
        }
    };

    const handleDelete = async (id: number) => {
        try {
            const data = await TiposUsoService.deleteTipoUso(id);
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 5000 });
            isLoading.value = true;
            await fetchTiposUsos();
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al eliminar el tipo de uso.', life: 3000 });
        }
    };

    const handleDeleteSelected = async (ids: number[]) => {
        try {
            const data = await TiposUsoService.deleteMultipleTiposUsos(ids);
            isLoading.value = true;
            await fetchTiposUsos();
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 5000 });
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al eliminar los tipos de usos seleccionadas.', life: 3000 });
        }
    };
</script>

<template>
    <div class="grid">
        <div class="col-12">
            <CrudTable
                :data="tiposUsos"
                entityName="Tipos de uso"
                :columns="COLUMNAS"
                newRouteName="admin-tipos-uso-new"
                editRouteName="admin-tipos-uso-edit"
                @delete-item="handleDelete"
                @delete-selected="handleDeleteSelected"
                :loading="isLoading" />
        </div>
    </div>
</template>
