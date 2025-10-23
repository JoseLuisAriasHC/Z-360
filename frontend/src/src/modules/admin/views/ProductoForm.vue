<script setup lang="ts">
    import AjustesBasicos from '../components/producto/AjustesBasicos.vue';
</script>
<template>
    <div class="card">
        <Tabs value="general">
            <TabList>
                <Tab value="general">Ajustes básicos</Tab>
            </TabList>
            <TabPanels>
                <AjustesBasicos></AjustesBasicos>
            </TabPanels>
        </Tabs>
    </div>
</template>
