<script setup lang="ts">
    import type { ProductoDetalleData, TallaStock } from '../../services/ProductoService';

    const props = defineProps<{
        visible: boolean;
        productoDetalle: ProductoDetalleData | undefined;
        mainImage: string;
    }>();

    const emit = defineEmits(['update:visible', 'talla-seleccionada']);

    const handleSelectTalla = (tallaStock: TallaStock) => {
        emit('talla-seleccionada', tallaStock);
        emit('update:visible', false);
    };

    const closeModal = () => {
        emit('update:visible', false);
    };
</script>

<template>
    <Dialog
        v-model:visible="props.visible"
        @update:visible="closeModal"
        modal
        :header="productoDetalle?.producto.nombre"
        :dismissableMask="true"
        :style="{ width: '50vw' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        :pt="{
            header: { style: 'font-family: Oswald, sans-serif; text-transform: capitalize;' },
            title: { style: 'font-size: 1.875rem; font-weight: 700;' },
        }">
        <div class="grid grid-cols-12 gap-8">
            <div class="col-span-12 xl:col-span-6">
                <img :src="mainImage" :alt="productoDetalle?.producto.nombre" class="hidden xl:block w-full h-auto" />
            </div>

            <div class="col-span-12 xl:col-span-6">
                <h3 class="text-lg font-bold mb-4">Selecciona tu talla</h3>
                <div class="grid grid-cols-3 gap-2">
                    <div v-for="tallaStock in productoDetalle?.tallas" class="shrink-0">
                        <Button
                            severity="secondary"
                            class="w-full"
                            :class="{ 'line-through': !tallaStock.disponible }"
                            :disabled="!tallaStock.disponible"
                            @click="handleSelectTalla(tallaStock)">
                            {{ tallaStock.talla.numero }}
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </Dialog>
</template>
