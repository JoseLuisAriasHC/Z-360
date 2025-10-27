<script setup lang="ts">
    import { ref, onMounted, computed } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import { VarianteProductoService, type VarianteTalla } from '@admin/services/VarianteProductoService';
    import { TallaService, type Talla } from '@admin/services/TallaService';
    import { getParamId } from '@/utils/utils';
    import FormField from '../FormField.vue';

    const varianteId = getParamId();

    // --- ESTADO ---
    const toast = useToast();
    const loading = ref(true);
    const varianteTallas = ref<VarianteTalla[]>([]);

    // Modales y formularios
    const showEditStockModal = ref(false);
    const showNewTallaModal = ref(false);
    const showDeleteConfirmModal = ref(false);

    // erros form
    const stockError = ref('');
    const tallaError = ref('');

    const tallaBorrando = ref<VarianteTalla | null>(null);
    const varianteTallaEdit = ref<VarianteTalla>({
        id: 0,
        product_variant_id: 0,
        sku: '',
        stock: 0,
        talla_id: 0,
        talla: { id: 0, numero: 0 },
    });
    const newVarianteTallaModel = ref<VarianteTalla>({
        id: 0,
        product_variant_id: 0,
        sku: '',
        stock: 0,
        talla_id: 0,
        talla: { id: 0, numero: 0 },
    });

    // Lista de tallas disponibles (para el modal de "Añadir nueva")
    const availableTallas = ref<Talla[]>([]);

    // --- COMPUTADAS ---

    const existingTallaIds = computed(() => varianteTallas.value.map((s) => s.talla_id));

    const tallasForNewSize = computed(() => {
        return availableTallas.value.filter((talla) => !existingTallaIds.value.includes(talla.id));
    });

    /**
     * Carga las tallas existentes para la variante actual.
     */
    const loadVarianteTallas = async (id: number) => {
        loading.value = true;
        try {
            varianteTallas.value = await VarianteProductoService.getVariantesTallas(id);
        } catch (error) {
            console.error('Error al cargar tallas:', error);
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar las tallas.', life: 3000 });
        } finally {
            loading.value = false;
        }
    };

    /**
     * Carga todas las tallas posibles del sistema.
     */
    const loadTallas = async () => {
        try {
            availableTallas.value = await TallaService.getTallas();
        } catch (error) {
            console.error('Error al cargar tallas disponibles:', error);
        }
    };

    onMounted(() => {
        if (varianteId.value) {
            loadVarianteTallas(varianteId.value);
            loadTallas();
        }
    });

    /** * Abre el modal para editar el stock de una talla existente.
     */
    const openEditStock = (size: VarianteTalla) => {
        // Clonar para no modificar el objeto reactivo original hasta que se guarde
        varianteTallaEdit.value = JSON.parse(JSON.stringify(size));
        showEditStockModal.value = true;
    };

    const saveEdit = async () => {
        const formData = new FormData();
        formData.append('stock', varianteTallaEdit.value.stock.toString());

        try {
            const response = await VarianteProductoService.updateVarianteTalla(formData, varianteTallaEdit.value.id);
            if (varianteId.value) loadVarianteTallas(varianteId.value);
            toast.add({ severity: 'success', summary: 'Éxito', detail: response.message, life: 3000 });
            showEditStockModal.value = false;
        } catch (error: any) {
            const responseData = error.response?.data;

            if (error.response?.status === 422 && responseData?.errors) {
                stockError.value = responseData.errors.stock ? responseData.errors.stock[0] : '';
                toast.add({
                    severity: 'error',
                    summary: 'Error de Validación',
                    detail: 'Por favor, corrige los errores en el formulario.',
                    life: 5000,
                });
            } else {
                toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al actualizar el stock.', life: 3000 });
            }
            console.error('Error al guardar el stock:', error);
        }
    };

    const openNew = () => {
        showNewTallaModal.value = true;
    };

    const saveNewSize = async () => {
        const formData = new FormData();
        formData.append('stock', newVarianteTallaModel.value.stock.toString());
        formData.append('talla_id', newVarianteTallaModel.value.talla_id.toString());

        try {
            if (varianteId.value) {
                const response = await VarianteProductoService.createVarianteTalla(formData, varianteId.value);
                loadVarianteTallas(varianteId.value);
                toast.add({ severity: 'success', summary: 'Éxito', detail: response.message, life: 3000 });
                showNewTallaModal.value = false;
            }
        } catch (error: any) {
            const responseData = error.response?.data;

            if (error.response?.status === 422 && responseData?.errors) {
                stockError.value = responseData.errors.stock ? responseData.errors.stock[0] : '';
                tallaError.value = responseData.errors.talla_id ? responseData.errors.talla_id[0] : '';
                toast.add({
                    severity: 'error',
                    summary: 'Error de Validación',
                    detail: 'Por favor, corrige los errores en el formulario.',
                    life: 5000,
                });
            } else {
                toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al actualizar el stock.', life: 3000 });
            }
            console.error('Error al guardar el stock:', error);
        }
    };

    const confirmDelete = (size: VarianteTalla) => {
        tallaBorrando.value = size;
        showDeleteConfirmModal.value = true;
    };

    const deleteVarianteTalla = async () => {
        if (!tallaBorrando.value) return;

        try {
            const response = await VarianteProductoService.deleteVariantTalla(tallaBorrando.value.id);
            toast.add({ severity: 'success', summary: 'Éxito', detail: response.message, life: 3000 });
            showDeleteConfirmModal.value = false;
            tallaBorrando.value = null;
            if (varianteId.value) loadVarianteTallas(varianteId.value);
        } catch (error) {
            console.error('Error al eliminar talla:', error);
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo eliminar la talla.', life: 3000 });
        }
    };
</script>

<template>
    <Toast />
    <Card class="shadow-lg">
        <template #title>Gestión de Tallas e Inventario</template>
        <template #content>
            <div>
                <div class="flex justify-end">
                    <Button
                        label="Añadir Talla"
                        icon="pi pi-plus"
                        severity="primary"
                        @click="openNew"
                        :disabled="loading || tallasForNewSize.length === 0" />
                </div>

                <DataTable
                    :value="varianteTallas"
                    dataKey="id"
                    :loading="loading"
                    stripedRows
                    :rows="10"
                    :paginator="varianteTallas.length > 10"
                    :rowHover="true"
                    tableStyle="min-width: 50rem">
                    <template #empty>
                        <div class="text-center py-4 text-gray-500">No hay tallas asignadas a esta variante.</div>
                    </template>
                    <template #loading>Cargando información de tallas. Por favor, espere.</template>

                    <Column field="talla.numero" header="Número de Talla" style="width: 25%">
                        <template #body="{ data }">
                            <Tag :value="data.talla.numero" severity="info" class="text-lg font-bold" />
                        </template>
                    </Column>

                    <Column field="sku" header="SKU" style="width: 35%" sortable></Column>

                    <Column field="stock" header="Stock" style="width: 20%" sortable>
                        <template #body="{ data }">
                            <Tag :value="data.stock" :severity="data.stock > 0 ? 'success' : 'warning'" />
                        </template>
                    </Column>

                    <!-- Columna de Acciones -->
                    <Column :exportable="false" header="Acciones" style="width: 20%; text-align: center">
                        <template #body="{ data }">
                            <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="openEditStock(data)" />
                            <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDelete(data)" />
                        </template>
                    </Column>
                </DataTable>
            </div>
        </template>
    </Card>

    <Dialog v-model:visible="showEditStockModal" modal header="Editando Talla" :style="{ width: '30rem' }">
        <div v-if="varianteTallaEdit" class="p-fluid">
            <div class="field mb-4">
                <label for="tallaNumero" class="font-semibold block mb-2">Talla</label>
                <InputNumber id="stock" v-model="varianteTallaEdit.talla.numero" :min="0" disabled class="w-full" />
            </div>
            <FormField id="stock" label="Stock" :error="stockError">
                <InputNumber id="stock" v-model="varianteTallaEdit.stock" showButtons :min="0" class="w-full" :invalid="stockError != ''" />
            </FormField>
        </div>
        <template #footer>
            <Button type="button" label="Cancelar" icon="pi pi-times" severity="secondary" @click="showEditStockModal = false" />
            <Button type="button" label="Guardar" icon="pi pi-check" @click="saveEdit" :disabled="stockError != ''" />
        </template>
    </Dialog>

    <Dialog v-model:visible="showNewTallaModal" modal header="Añadir Nueva Talla" :style="{ width: '30rem' }">
        <div class="p-fluid">
            <FormField id="talla" label="Talla" :error="tallaError">
                <Select
                    id="talla"
                    v-model="newVarianteTallaModel.talla_id"
                    :options="tallasForNewSize"
                    optionLabel="numero"
                    optionValue="id"
                    placeholder="Seleccione una talla"
                    :disabled="tallasForNewSize.length === 0"
                    class="w-full"
                    :invalid="tallaError != ''" />
                <small v-if="tallasForNewSize.length === 0" class="text-red-500 mt-2">No hay tallas disponibles para asignar.</small>
            </FormField>
            <FormField id="stock" label="Stock" :error="stockError">
                <InputNumber id="stock" v-model="newVarianteTallaModel.stock" showButtons :min="0" class="w-full" :invalid="stockError != ''" />
            </FormField>
        </div>
        <template #footer>
            <Button type="button" label="Cancelar" icon="pi pi-times" severity="secondary" @click="showNewTallaModal = false" />
            <Button type="button" label="Añadir" icon="pi pi-check" @click="saveNewSize" />
        </template>
    </Dialog>

    <Dialog v-model:visible="showDeleteConfirmModal" :style="{ width: '450px' }" header="Confirmar Eliminación" :modal="true">
        <div class="flex items-center gap-4">
            <i class="pi pi-exclamation-triangle !text-3xl text-orange-500" />
            <span v-if="tallaBorrando">
                ¿Estás seguro de que quieres eliminar la talla
                <b>{{ tallaBorrando.talla?.numero }}</b>
                (SKU: {{ tallaBorrando.sku }})? Esta acción no se puede deshacer.
            </span>
        </div>
        <template #footer>
            <Button label="No" icon="pi pi-times" text @click="showDeleteConfirmModal = false" />
            <Button label="Sí, Eliminar" icon="pi pi-check" severity="danger" @click="deleteVarianteTalla" />
        </template>
    </Dialog>
</template>
