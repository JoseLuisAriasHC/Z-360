<script setup lang="ts">
    import { onMounted, ref } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import CrudTable from '@admin/components/CrudTable.vue';
    import { type Cupon, CuponService } from '@/modules/admin/services/CuponService';
import { formatDate } from '@/utils/utils';

    const cupones = ref<Cupon[]>([]);
    const toast = useToast();
    const isLoading = ref(true);

    const COLUMNAS = [
        { field: 'id', header: 'ID', sortable: true },
        { field: 'codigo', header: 'Cupon', sortable: true },
        { field: 'descuento', header: 'Descuento', sortable: true },
        { field: 'tipo', header: 'Tipo', sortable: true },
        { field: 'fecha_expiracion', header: 'Fecha de expiración', sortable: true },
        { field: 'uso_maximo', header: 'Numero de usos', sortable: true },
    ];

    onMounted(() => {
        fetchCupones();
    });

    const fetchCupones = async () => {
        try {
            const data = await CuponService.getCupones();
            cupones.value = [...data];
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al cargar los cupones.', life: 3000 });
        } finally {
            isLoading.value = false;
        }
    };

    const handleDelete = async (id: number) => {
        try {
            const data = await CuponService.deleteCupon(id);
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 5000 });
            isLoading.value = true;
            await fetchCupones();
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al eliminar el cupon.', life: 3000 });
        }
    };

    const handleDeleteSelected = async (ids: number[]) => {
        try {
            const data = await CuponService.deleteMultipleCupones(ids);
            isLoading.value = true;
            await fetchCupones();
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 5000 });
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al eliminar los cupones seleccionadas.', life: 3000 });
        }
    };

</script>

<template>
    <div class="grid">
        <div class="col-12">
            <CrudTable
                :data="cupones"
                entityName="Cupon"
                :columns="COLUMNAS"
                newRouteName="admin-cupones-new"
                editRouteName="admin-cupones-edit"
                @delete-item="handleDelete"
                @delete-selected="handleDeleteSelected"
                :loading="isLoading">
                <template #descuento="{ data }">
                    {{ Number(data.descuento).toFixed(2) }}
                </template>
                <template #fecha_expiracion="{ data }">
                    <span v-if="data.fecha_expiracion">
                        {{ formatDate(data.fecha_expiracion) }}
                    </span>
                    <span v-else class="text-gray-500">N/A</span>
                </template>
            </CrudTable>
        </div>
    </div>
</template>
