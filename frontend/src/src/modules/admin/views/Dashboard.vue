<script setup>
    import BestSellingWidget from '@admin/components/dashboard/BestSellingWidget.vue';
    import RevenueStreamWidget from '@admin/components/dashboard/RevenueStreamWidget.vue';
    import MetricasWidget from '@admin/components/dashboard/metricas/MetricasWidget.vue';
</script>

<template>
    <div class="grid grid-cols-12 gap-8">
        <MetricasWidget />
        <div class="col-span-12 xl:col-span-8">
            <RevenueStreamWidget />
        </div>
        <div class="col-span-12 xl:col-span-4">
            <BestSellingWidget />
        </div>
    </div>
</template>
