<script setup lang="ts">
    import { inject, onMounted, ref, type Ref } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import { type Talla, TallaService } from '@/modules/admin/services/TallaService';
    import { ColorService, type Color } from '@/modules/admin/services/ColorService';
    import { VarianteProductoService } from '@/modules/admin/services/VarianteProductoService';
    import FormField from '../FormField.vue';

    const emit = defineEmits(['refrescar-tabla']);
    const op = ref();
    const toast = useToast();
    const productoValido = inject<Ref<boolean>>('productoValido', ref(true));
    const props = defineProps<{
        productoId: number | null;
    }>();
    const toggle = (event: any) => {
        op.value.toggle(event);
    };

    const loading = ref(false);
    const tallas = ref<Talla[]>([]);
    const colores = ref<Color[]>([]);
    const coloresSeleccionados = ref<number[]>([]);
    const tallasSeleccionadas = ref<number[]>([]);
    const mostrarModalPrecio = ref(false);

    const precio = ref<number>(0);
    const stock = ref<number>(0);

    const precioError = ref('');
    const stockError = ref('');

    const fetchTallas = async () => {
        try {
            const data = await TallaService.getTallas();
            tallas.value = [...data];
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al cargar las tallas.', life: 3000 });
        }
    };

    const fetchColores = async () => {
        try {
            const data = await ColorService.getColores();
            colores.value = [...data];
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al cargar las colores.', life: 3000 });
        }
    };

    function getFormData() {
        const coloresIDs = coloresSeleccionados.value;
        const tallasIDs = tallasSeleccionadas.value;

        const formData = new FormData();
        formData.append('product_id', props.productoId?.toString() ?? '');
        formData.append('precio', precio.value.toString());
        formData.append('stock', stock.value.toString());
        console.log(stock.value);

        coloresIDs.forEach((id) => {
            formData.append('colores[]', id.toString());
        });

        tallasIDs.forEach((id) => {
            formData.append('tallas[]', id.toString());
        });

        return formData;
    }

    const handleSubmit = async () => {
        loading.value = true;
        const formData = getFormData();

        try {
            const data = await VarianteProductoService.generarVaraintes(formData);
            toast.add({ severity: 'success', summary: 'Éxito', detail: data.message, life: 3000 });
            emit('refrescar-tabla');
            mostrarModalPrecio.value = false;
        } catch (error: any) {
            const responseData = error.response?.data;

            if (error.response?.status === 422 && responseData?.errors) {
                precioError.value = responseData.errors.numero ? responseData.errors.precio[0] : '';

                const allErrors = responseData.errors;
                const errorMessages: string[] = Object.values(allErrors)
                    .flatMap((messages: any) => messages)
                    .filter((msg: any) => typeof msg === 'string');

                const consolidatedDetail = errorMessages.length > 0 ? errorMessages.join('\n') : 'Error de validación desconocido.';
                toast.add({
                    severity: 'error',
                    summary: 'Error de Validación',
                    detail: consolidatedDetail,
                    life: 7000,
                });
            } else {
                const detail = responseData?.message || 'Error desconocido generar las variantes.';
                toast.add({ severity: 'error', summary: 'Error al guardar', detail, life: 3000 });
            }
        } finally {
            loading.value = false;
        }
    };

    onMounted(() => {
        if (productoValido && props.productoId) {
            fetchTallas();
            fetchColores();
        }
    });
</script>

<template>
    <div class="font-semibold text-xl p-2 flex justify-between">
        Colores
        <Button type="button" severity="info" icon="pi pi-info-circle" @click="toggle" />
        <Popover ref="op">
            <p>Esta herramineta para generar las variantes de un producto funciona de la siguiente forma:</p>
            <p>- Debes primero seleccionar los colores y las tallas (al menos una de cada)</p>
            <p>- Darle al boton Generar Variantes</p>
            <p>- Se abrira un modal y tendras que poner el precio y el stock (Son opcionales) </p>
            <hr>
            <p>Caracteristicas</p>
            <p>- Si ya existe un producto con ese color y talla, no genera otro ni lo modifica</p>
            <p>- Por el contrario si existe un producto con ese color pero no la talla, si lo genera</p>
        </Popover>
    </div>

    <ScrollPanel
        style="width: 100%; height: 30vh"
        :dt="{
            bar: {
                background: '{primary.color}',
            },
        }"
        :pt="{
            root: {
                class: 'bg-surface-100 dark:bg-surface-950 rounded-xl',
            },
        }">
        <div v-for="color of colores" :key="color.id" class="item-check">
            <Checkbox v-model="coloresSeleccionados" :inputId="`color_${color.id}`" :value="color.id" />
            <label :for="`color_${color.id}`" class="inline-grid grid-cols-2 gap-2 items-center w-full">
                <span class="inline-block text-surface-900 dark:text-surface-0">
                    {{ color.nombre }}
                </span>
                <div>
                    <div
                        class="w-7 h-7 rounded-lg flex-shrink-0 d-block border border-surface-300"
                        :style="{ backgroundColor: color.codigo_hex }"></div>
                </div>
            </label>
        </div>
    </ScrollPanel>

    <div class="font-semibold text-xl p-2">Tallas</div>
    <ScrollPanel
        style="width: 100%; height: 29vh"
        :dt="{
            bar: {
                background: '{primary.color}',
            },
        }"
        :pt="{
            root: {
                class: 'bg-surface-100 dark:bg-surface-950 rounded-xl',
            },
        }">
        <div v-for="talla of tallas" :key="talla.id" class="item-check">
            <Checkbox v-model="tallasSeleccionadas" :inputId="`talla_${talla.id}`" :value="talla.id" />
            <label :for="`talla_${talla.id}`" class="w-full text-surface-900 dark:text-surface-0">
                {{ talla.numero }}
            </label>
        </div>
    </ScrollPanel>

    <Button
        label="Generar Variantes"
        @click="mostrarModalPrecio = true"
        severity="primary"
        icon="pi pi-check"
        class="mt-3 w-full"
        :loading="loading"
        :disabled="loading" />
    <Dialog v-model:visible="mostrarModalPrecio" modal header="Generar variantes con precio" :style="{ width: '30rem' }">
        <form @submit.prevent="handleSubmit">
            <span class="text-surface-500 dark:text-surface-400 block mb-8">
                Si quieres puedes asignar un precio base a todos los productos generados, se pueden cambiar luego. Si no le asignas ningun precio, el
                precio sera 0
            </span>
            <FormField id="precio" label="Precio" :error="precioError">
                <InputNumber
                    v-model="precio"
                    :step="5"
                    :min="0"
                    fluid
                    showButtons
                    mode="currency"
                    currency="EUR"
                    :minFractionDigits="2"
                    :maxFractionDigits="5"
                    :invalid="precioError != ''"
                    @input="precioError = ''" />
            </FormField>
            <FormField id="stock" label="Stock" :error="stockError">
                <InputNumber v-model="stock" :step="2" :min="0" fluid showButtons :invalid="stockError != ''" @input="stockError = ''" />
            </FormField>
            <div class="flex justify-end gap-2 mt-8">
                <Button type="button" label="Cancelar" severity="secondary" icon="pi pi-times" @click="mostrarModalPrecio = false"></Button>
                <Button type="submit" label="Generar Variantes" :loading="loading" severity="primary" icon="pi pi-check" :disabled="loading" />
            </div>
        </form>
    </Dialog>
</template>

<style scoped>
    .item-check {
        display: flex;
        align-items: center;
        position: relative;
        outline: 0 none;
        color: var(--text-color);
        padding: 0.75rem 1rem;
        border-radius: var(--content-border-radius);
        transition:
            background-color var(--element-transition-duration),
            box-shadow var(--element-transition-duration);
    }
    .item-check:hover {
        background-color: var(--surface-hover);
    }
    .p-checkbox-group {
        display: block;
    }
    label {
        margin-left: 0.5rem;
    }
</style>
