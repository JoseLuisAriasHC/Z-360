<script setup lang="ts">
    import Form from '@admin/components/VariantesForm/Form.vue';
    import VaraintesImagenes from '@admin/components/VariantesForm/VaraintesImagenes.vue';
    import Tallas from '@admin/components/VariantesForm/Tallas.vue';
    import { useRouter } from 'vue-router';
    import { ref } from 'vue';
    import { type Color, ColorService } from '@admin/services/ColorService';
    import { useToast } from 'primevue';

    const router = useRouter();
    const toast = useToast();
    const idProducto = ref<number>(0);
    const idColor = ref<number>(0);
    const color = ref<Color>({
        id: 0,
        codigo_hex: '',
        nombre: '',
    });

    function handleLoadIdProdcto(id: number) {
        idProducto.value = id;
    }

    async function handleLoadIdColor(id: number) {
        idColor.value = id;
        try {
            const data = await ColorService.getColor(id);
            color.value = data;
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar el color.', life: 3000 });
        }
    }

    const goBack = () => {
        router.push({ name: 'admin-productos-edit', params: { id: idProducto.value } });
    };
</script>

<template>
    <div class="card">
        <div class="font-semibold text-xl flex items-center justify-between mb-4">
            <div class="flex">
                <h5 class="text-xl font-bold mr-3">Editar Variante {{ color?.nombre }}</h5>
                <div class="w-7 h-7 rounded-lg flex-shrink-0" :style="{ backgroundColor: color.codigo_hex }"></div>
            </div>
            <Button icon="pi pi-arrow-left" label="Volver" severity="secondary" @click="goBack" :fluid="false" />
        </div>

        <hr />
        <VaraintesImagenes />

        <hr />
        <Form @id-producto="handleLoadIdProdcto" @id-color="handleLoadIdColor" />

        <hr />
        <Tallas />
    </div>
</template>
