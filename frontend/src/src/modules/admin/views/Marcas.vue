<script setup lang="ts">
    import { onMounted, ref } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import CrudTable from '@admin/components/CrudTable.vue';
    import { type Marca, MarcaService } from '@/modules/admin/services/MarcaService';
    import noImageSvg from '@/assets/img/no-image.svg';

    const marcas = ref<Marca[]>([]);
    const toast = useToast();

    // Definición de las columnas de la tabla 'marcas'
    const MARCA_COLUMNS = [
        { field: 'id', header: 'ID', sortable: true, style: 'width: 5rem' },
        { field: 'nombre', header: 'Nombre', sortable: true, style: 'min-width: 15rem' },
        { field: 'logo', header: 'Logo', sortable: false, style: 'min-width: 8rem', bodyTemplate: 'logo-body' },
        { field: 'talla_offset', header: 'Offset de Talla', sortable: true, style: 'min-width: 10rem' },
    ];

    onMounted(() => {
        fetchMarcas();
    });

    const fetchMarcas = async () => {
        try {
            const data = await MarcaService.getMarcas();
            marcas.value = data;
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al cargar las marcas.', life: 3000 });
        }
    };

    /** Llama al servicio para eliminar una marca por ID */
    const handleDelete = async (id: number) => {
        try {
            const data = await MarcaService.deleteMarca(id);
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 5000 });
            await fetchMarcas();
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al eliminar la marca.', life: 3000 });
        }
    };

    /** Llama al servicio para eliminar marcas seleccionadas por IDs */
    const handleDeleteSelected = async (ids: number[]) => {
        try {
            const data = await MarcaService.deleteMultipleMarcas(ids);
            await fetchMarcas();
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 5000 });
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al eliminar las marcas seleccionadas.', life: 3000 });
        }
    };

    const handleImageError = (event: Event) => {
        const imgElement = event.target as HTMLImageElement;
        imgElement.onerror = null; // Evita un bucle infinito si el no-logo.svg también falla
        imgElement.src = noImageSvg; // Usa la URL resuelta del SVG importado
    };
</script>

<template>
    <div class="grid">
        <div class="col-12">
            <CrudTable
                :data="marcas"
                entityName="Marca"
                :columns="MARCA_COLUMNS"
                newRouteName="admin-marcas-new"
                editRouteName="admin-marcas-edit"
                @delete-item="handleDelete"
                @delete-selected="handleDeleteSelected">
                <!-- Implementación del Slot 'logo-body' para renderizar la imagen -->
                <template #logo-body="{ data }">
                    <img
                        :src="data.logo"
                        :alt="data.nombre + ' Logo'"
                        class="rounded"
                        style="width: 78px; height: 78px; object-fit: contain"
                        @error="handleImageError" />
                </template>

                <!-- Slot para formatear el talla_offset a 2 decimales -->
                <template #talla_offset="{ data }">
                    {{ (data.talla_offset as number).toFixed(2) }}
                </template>
            </CrudTable>
        </div>
    </div>
</template>
