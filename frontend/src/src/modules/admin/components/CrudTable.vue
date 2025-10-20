<script setup lang="ts">
    import { FilterMatchMode } from '@primevue/core/api';
    import { useToast } from 'primevue/usetoast';
    import { ref } from 'vue';
    import { useRouter } from 'vue-router';

    // Definición de tipos para la estructura de columnas y el item genérico
    interface CrudColumn {
        field: string;
        header: string;
        sortable: boolean;
        style?: string;
        bodyTemplate?: string; // Nombre del slot para renderizado personalizado
    }

    interface CrudItem {
        id: number;
        [key: string]: any; // Permite cualquier otra propiedad
    }

    // PROPS: Hacen que el componente sea genérico
    const props = defineProps<{
        data: CrudItem[];
        entityName: string;
        columns: CrudColumn[];
        newRouteName: string;
        editRouteName: string;
    }>();

    // EMITS: Para notificar al componente padre de las acciones
    const emit = defineEmits<{
        (e: 'delete-item', id: number): void;
        (e: 'delete-selected', ids: number[]): void;
    }>();

    const router = useRouter();
    const toast = useToast();
    const dt = ref(); // Referencia al DataTable
    const selectedItems = ref<CrudItem[]>([]);
    const itemToDelete = ref<CrudItem | null>(null);
    const deleteItemDialog = ref(false);
    const deleteSelectedDialog = ref(false);

    const filters = ref({
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    });

    // --- LÓGICA DE ACCIÓN DE FILAS ---

    /** Redirige al formulario de creación. */
    function openNew(): void {
        router.push({ name: props.newRouteName });
    }

    /** Redirige al formulario de edición con el ID del elemento. (REQUERIDO POR EL USER) */
    function editItem(item: CrudItem): void {
        router.push({ name: props.editRouteName, params: { id: item.id } });
    }

    /** Muestra el diálogo de confirmación para un solo elemento */
    function confirmDeleteItem(item: CrudItem): void {
        itemToDelete.value = item;
        deleteItemDialog.value = true;
    }

    /** Ejecuta la eliminación de un solo elemento */
    function deleteItem(): void {
        if (itemToDelete.value) {
            emit('delete-item', itemToDelete.value.id);
            deleteItemDialog.value = false;
            itemToDelete.value = null;
        }
    }

    /** Muestra el diálogo de confirmación para elementos seleccionados */
    function confirmDeleteSelected(): void {
        deleteSelectedDialog.value = true;
    }

    /** Ejecuta la eliminación de elementos seleccionados */
    function deleteSelectedItems(): void {
        if (selectedItems.value.length > 0) {
            const ids = selectedItems.value.map((item) => item.id);
            emit('delete-selected', ids);
            deleteSelectedDialog.value = false;
            selectedItems.value = [];
        }
    }
</script>

<template>
    <div class="card">
        <DataTable
            ref="dt"
            v-model:selection="selectedItems"
            :value="props.data"
            dataKey="id"
            :paginator="true"
            :rows="10"
            :filters="filters"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            :rowsPerPageOptions="[5, 10, 25]"
            :currentPageReportTemplate="`Mostrando del {first} al {last}`">
            <template #header>
                <div class="flex flex-wrap gap-2 items-center justify-between">
                    <div class="flex gap-2 items-center mb-3">
                        <Button :label="`New ${entityName}`" icon="pi pi-plus" severity="primary" @click="openNew" />
                        <Button
                            label="Borrar"
                            icon="pi pi-trash"
                            severity="secondary"
                            @click="confirmDeleteSelected"
                            :disabled="!selectedItems || !selectedItems.length" />
                        <Divider layout="vertical"><b></b></Divider>

                        <h3 class="m-0">{{ entityName }}s</h3>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Barra de Búsqueda -->
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Buscar..." />
                        </IconField>
                    </div>
                </div>
            </template>

            <!-- Columnas Genéricas -->
            <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
            <Column v-for="col in columns" :key="col.field" :field="col.field" :header="col.header" :sortable="col.sortable" :style="col.style">
                <template #body="slotProps">
                    <!-- Se utiliza el nombre del slot de la columna, o el campo por defecto -->
                    <slot :name="col.bodyTemplate || col.field" :data="slotProps.data">
                        {{ slotProps.data[col.field] }}
                    </slot>
                </template>
            </Column>

            <!-- Columna de Acciones -->
            <Column :exportable="false" style="min-width: 12rem" header="">
                <template #body="slotProps">
                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editItem(slotProps.data)" />
                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeleteItem(slotProps.data)" />
                </template>
            </Column>
        </DataTable>

        <!-- Diálogos de Eliminación (se mantienen modales para confirmación) -->
        <Dialog v-model:visible="deleteItemDialog" :style="{ width: '450px' }" header="Confirmar Eliminación" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl text-orange-500" />
                <span v-if="itemToDelete">
                    ¿Estás seguro de que quieres eliminar la {{ entityName }}
                    <b>{{ itemToDelete.nombre || itemToDelete.name }}</b>
                    ?
                </span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteItemDialog = false" />
                <Button label="Sí" icon="pi pi-check" @click="deleteItem" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteSelectedDialog" :style="{ width: '450px' }" header="Confirmar Eliminación" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span>¿Estás seguro de que quieres eliminar los {{ selectedItems.length }} {{ entityName }}s seleccionados?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteSelectedDialog = false" />
                <Button label="Sí" icon="pi pi-check" text @click="deleteSelectedItems" />
            </template>
        </Dialog>
    </div>
</template>
