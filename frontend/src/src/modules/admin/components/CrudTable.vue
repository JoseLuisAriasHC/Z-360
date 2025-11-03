<script setup lang="ts">
    import { FilterMatchMode } from '@primevue/core/api';
    import { onMounted, ref } from 'vue';
    import { useRouter } from 'vue-router';

    interface CrudColumn {
        field: string;
        header: string;
        sortable: boolean;
        style?: string;
        bodyTemplate?: string; // Nombre del slot para renderizado personalizado
        filtrable?: boolean;
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
        newRouteName?: string;
        editRouteName: string;
        loading: boolean;
    }>();

    // EMITS: Para notificar al componente padre de las acciones
    const emit = defineEmits<{
        (e: 'delete-item', id: number): void;
        (e: 'delete-selected', ids: number[]): void;
    }>();

    const router = useRouter();
    const dt = ref(); // Referencia al DataTable
    const selectedItems = ref<CrudItem[]>([]);
    const itemToDelete = ref<CrudItem | null>(null);
    const deleteItemDialog = ref(false);
    const deleteSelectedDialog = ref(false);

    // con DataTableFilterMeta de PrimeVue y la inicialización. (Errores 1 y 2)
    const filters = ref<any>({
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    });

    /**
     * Inicializa el objeto de filtros para la tabla, cubriendo el filtro global
     * y todos los campos de las columnas para permitir filtros por menú.
     */
    const initFilters = () => {
        const initialFilters: Record<string, any> = {
            global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        };

        props.columns.forEach((col) => {
            if (col.filtrable) {
                initialFilters[col.field] = {};
            }
        });

        filters.value = initialFilters;
    };

    // Inicializa los filtros al montar el componente
    onMounted(() => {
        initFilters();
    });

    // --- LÓGICA DE ACCIÓN DE FILAS ---

    /** Redirige al formulario de creación. */
    function openNew(): void {
        router.push({ name: props.newRouteName });
    }

    /** Redirige al formulario de edición con el ID del elemento */
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
        <div class="p-4 border border-surface-200 dark:border-surface-700 rounded-t-xl bg-surface-50 dark:bg-surface-800">
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <div class="flex gap-2 items-center mb-3">
                    <Button v-if="props.newRouteName" :label="`New ${entityName}`" icon="pi pi-plus" severity="primary" @click="openNew" :disabled="props.loading" />
                    <Button
                        label="Borrar"
                        icon="pi pi-trash"
                        severity="secondary"
                        @click="confirmDeleteSelected"
                        :disabled="!selectedItems || !selectedItems.length || props.loading" />
                    <Divider layout="vertical"><b></b></Divider>
                    <h3 class="m-0">{{ entityName }}s</h3>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Barra de Búsqueda -->
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="filters['global'].value" placeholder="Buscar..." :disabled="props.loading" />
                    </IconField>
                </div>
            </div>
        </div>

        <!-- SKELETON LOADER: Se muestra si loading es true -->
        <div v-if="props.loading" class="p-4 border border-t-0 border-surface-200 dark:border-surface-700 rounded-b-xl h-full">
            <template v-for="i in 10" :key="i">
                <div class="flex items-center gap-3 py-3 border-b border-surface-100 dark:border-surface-800 last:border-b-0">
                    <Skeleton width="3rem" height="1.5rem"></Skeleton>
                    <Skeleton width="10rem" height="1.5rem"></Skeleton>
                    <Skeleton width="15rem" height="1.5rem"></Skeleton>
                    <Skeleton width="5rem" height="3rem"></Skeleton>
                    <Skeleton width="8rem" height="3rem" class="mr-4"></Skeleton>
                    <Skeleton width="10rem" height="1.5rem"></Skeleton>
                    <Skeleton width="3rem" height="1.5rem"></Skeleton>
                    <Skeleton width="10rem" height="1.5rem"></Skeleton>
                    <Skeleton width="15rem" height="1.5rem"></Skeleton>
                    <div class="flex ml-auto gap-2">
                        <Skeleton width="2.5rem" height="2.5rem" shape="circle"></Skeleton>
                        <Skeleton width="2.5rem" height="2.5rem" shape="circle"></Skeleton>
                    </div>
                </div>
            </template>
        </div>

        <!-- DATA TABLE: Se muestra si loading es false -->
        <DataTable
            v-else
            ref="dt"
            v-model:selection="selectedItems"
            :value="props.data"
            dataKey="id"
            :paginator="true"
            :rowHover="true"
            :rows="10"
            v-model:filters="filters"
            filterDisplay="menu"
            :rowsPerPageOptions="[5, 10, 25]"
            :currentPageReportTemplate="`Mostrando del {first} al {last}`"
            :pt="{
                // Aplicamos bordes para que continúe el div del header
                root: { class: 'rounded-t-none border border-t-0 border-surface-200 dark:border-surface-700 rounded-b-xl' },
            }">
            <!-- Columnas-->
            <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
            <Column
                v-for="col in columns"
                :key="col.field"
                :field="col.field"
                :header="col.header"
                :sortable="col.sortable"
                :style="col.style"
                :showFilterMatchModes="false"
                :showFilterMenu="col.filtrable ?? false">
                <template #body="slotProps">
                    <slot :name="col.bodyTemplate || col.field" :data="slotProps.data">
                        {{ slotProps.data[col.field] }}
                    </slot>
                </template>

                <!-- Slot para el filtro personalizado -->
                <template #filter="{ filterModel }">
                    <slot :name="`filter-${col.field}`" :filterModel="filterModel"></slot>
                </template>
            </Column>

            <!-- Columna de Acciones -->
            <Column :exportable="false" style="width: 10rem" header="">
                <template #body="slotProps">
                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editItem(slotProps.data)" />
                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeleteItem(slotProps.data)" />
                </template>
            </Column>
        </DataTable>

        <!-- Diálogos de Eliminación -->
        <Dialog v-model:visible="deleteItemDialog" :style="{ width: '450px' }" header="Confirmar Eliminación" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl text-orange-500" />
                <span v-if="itemToDelete">
                    ¿Estás seguro de que quieres eliminar {{ entityName }}
                    <b>{{ itemToDelete.nombre || itemToDelete.name || itemToDelete.numero || itemToDelete.id }}</b>
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
