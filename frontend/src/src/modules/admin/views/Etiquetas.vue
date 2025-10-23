<script setup lang="ts">
    import { onMounted, ref } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import CrudTable from '@admin/components/CrudTable.vue';
    import { type Etiqueta, EtiquetaService } from '@/modules/admin/services/EtiquetaService';

    const etiquetas = ref<Etiqueta[]>([]);
    const toast = useToast();
    const isLoading = ref(true);

    const COLUMNAS = [
        { field: 'id', header: 'ID', sortable: true },
        { field: 'nombre', header: 'Nombre', sortable: true },
    ];

    onMounted(() => {
        fetchEtiquetas();
    });

    const fetchEtiquetas = async () => {
        try {
            const data = await EtiquetaService.getEtiquetas();
            etiquetas.value = [...data];
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al cargar las etiquetas.', life: 3000 });
        } finally {
            isLoading.value = false;
        }
    };

    const handleDelete = async (id: number) => {
        try {
            const data = await EtiquetaService.deleteEtiqueta(id);
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 5000 });
            isLoading.value = true;
            await fetchEtiquetas();
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al eliminar la etiqueta.', life: 3000 });
        }
    };

    const handleDeleteSelected = async (ids: number[]) => {
        try {
            const data = await EtiquetaService.deleteMultipleEtiquetas(ids);
            isLoading.value = true;
            await fetchEtiquetas();
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 5000 });
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al eliminar las etiquetas seleccionadas.', life: 3000 });
        }
    };
</script>

<template>
    <div class="grid">
        <div class="col-12">
            <CrudTable
                :data="etiquetas"
                entityName="Etiqueta"
                :columns="COLUMNAS"
                newRouteName="admin-etiquetas-new"
                editRouteName="admin-etiquetas-edit"
                @delete-item="handleDelete"
                @delete-selected="handleDeleteSelected"
                :loading="isLoading" />
        </div>
    </div>
</template>
