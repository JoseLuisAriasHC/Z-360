<script setup lang="ts">
    import EmpresaSettings from '@admin/components/parametrosGenerales/EmpresaSettings.vue';
    import EnvioSettings from '@admin/components/parametrosGenerales/EnvioSettings.vue';
    import EcommerceSettings from '@admin/components/parametrosGenerales/EcommerceSettings.vue';
</script>
<template>
    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12 xl:col-span-6">
            <EmpresaSettings></EmpresaSettings>
        </div>
        <div class="col-span-12 xl:col-span-6">
            <EnvioSettings></EnvioSettings>
            <EcommerceSettings></EcommerceSettings>
        </div>
    </div>
</template>
