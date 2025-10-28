<script setup lang="ts">
    import Form from '@admin/components/VariantesForm/Form.vue';
    import VaraintesImagenes from '@admin/components/VariantesForm/VaraintesImagenes.vue';
    import Tallas from '@admin/components/VariantesForm/Tallas.vue';
    import { useRouter } from 'vue-router';
    import { ref } from 'vue';

    const router = useRouter();
    const idProducto = ref<number>(0);

    function handleLoadIdProdcto(id: number) {
        idProducto.value = id;
    }
    const goBack = () => {
        router.push({ name: 'admin-productos-edit', params: { id: idProducto.value } });
    };
</script>

<template>
    <div class="card">
        <div class="font-semibold text-xl flex items-center justify-between">
            <h5 class="text-xl font-bold">Editar Variante</h5>
            <Button icon="pi pi-arrow-left" label="Volver" severity="secondary" @click="goBack" :fluid="false" />
        </div>
        <hr />
        <VaraintesImagenes />
        <hr />
        <Form @id-producto="handleLoadIdProdcto" />
        <hr />
        <Tallas />
    </div>
</template>
