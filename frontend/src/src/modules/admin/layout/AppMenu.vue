<script setup lang="ts">
    import { ref } from 'vue';

    import AppMenuItem from './AppMenuItem.vue';

    const model = ref([
        {
            label: 'Home',
            items: [{ label: 'Dashboard', icon: 'pi pi-fw pi-home', to: { name: 'admin-dashboard' } }],
        },
        {
            label: 'Productos',
            items: [
                { label: 'Productos', icon: 'pi pi-fw pi-shopping-bag', to: { name: 'admin-dashboard' } },
                { label: 'Marcas', icon: 'pi pi-fw pi-tag', to: { name: 'admin-marcas' } },
                { label: 'Tallas', icon: 'pi pi-sort-alt', to: { name: 'admin-dashboard' } },
                { label: 'Colores', icon: 'pi pi-fw pi-palette', to: { name: 'admin-dashboard' } },
            ],
        },
        {
            label: 'Marketing',
            items: [{ label: 'Cupones', icon: 'pi pi-fw pi-ticket', to: { name: 'admin-dashboard' } }],
        },
        {
            label: 'Configuración',
            items: [{ label: 'Parámetros Generales', icon: 'pi pi-fw pi-cog', to: { name: 'admin-dashboard' } }],
        },
    ]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item :item="item" :index="i"></app-menu-item>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
