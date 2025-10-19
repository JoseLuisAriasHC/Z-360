<script setup lang="ts">
    import { onMounted, computed } from 'vue';
    import { useMetricStore } from '@admin/stores/metricasStore';
    import MetricasCard from '@admin/components/dashboard/metricas/MetricasCard.vue';
    import { safeNumber } from '@/utils/utils';

    const metricStore = useMetricStore();

    onMounted(() => {
        // Cargar las métricas al montar la vista
        metricStore.fetchMetricas();
    });

    const ordersProps = computed(() => {
        const data = metricStore.metrics?.orders;
        
        // El valor por defecto para comparisonValue si data.difference es nulo o 0
        const difference = safeNumber(data?.difference);
        const compVal = difference >= 0 ? `+${difference}` : `${difference}`;
        
        return {
            title: 'Pedidos',
            // Usamos un valor seguro (0) si data o el valor no existe
            value: safeNumber(data?.current_week_count),
            icon: 'pi pi-shopping-cart',
            iconBgClass: 'bg-blue-100 dark:bg-blue-400/10',
            iconColorClass: 'text-blue-500',
            comparisonValue: compVal,
            comparisonText: 'que la otra semana',
            comparisonPositive: difference >= 0,
        };
    });

    const revenueWeekProps = computed(() => {
        const data = metricStore.metrics?.revenue_week;
        
        const percentage = safeNumber(data?.comparison_percentage);
        const revenue = data?.current_week_revenue;

        const compVal = percentage >= 0 ? `+${percentage}%` : `${percentage}%`;

        return {
            title: 'Ganancias semanal',
            // Si revenue es nulo, usamos '0 €' como string
            value: revenue?.toLocaleString('es-ES', { style: 'currency', currency: 'EUR' }) ?? '0 €',
            icon: 'pi pi-dollar',
            iconBgClass: 'bg-orange-100 dark:bg-orange-400/10',
            iconColorClass: 'text-orange-500',
            comparisonValue: compVal,
            comparisonText: 'desde la semana anterior',
            comparisonPositive: percentage >= 0,
        };
    });

    const revenueMonthProps = computed(() => {
        const data = metricStore.metrics?.revenue_month;

        const percentage = safeNumber(data?.comparison_percentage);
        const revenue = data?.current_month_revenue;

        const compVal = percentage >= 0 ? `+${percentage}%` : `${percentage}%`;

        return {
            title: 'Ganancias mensuales',
            // Si revenue es nulo, usamos '0 €' como string
            value: revenue?.toLocaleString('es-ES', { style: 'currency', currency: 'EUR' }) ?? '0 €',
            icon: 'pi pi-dollar',
            iconBgClass: 'bg-red-100 dark:bg-red-400/10',
            iconColorClass: 'text-red-500',
            comparisonValue: compVal,
            comparisonText: 'desde el mes anterior',
            comparisonPositive: percentage >= 0,
        };
    });

    const usersProps = computed(() => {
        const data = metricStore.metrics?.users;
        
        const totalClients = safeNumber(data?.total_clients);
        const newClients = safeNumber(data?.new_clients_this_week);

        return {
            title: 'Usuarios',
            value: totalClients,
            icon: 'pi pi-users',
            iconBgClass: 'bg-cyan-100 dark:bg-cyan-400/10',
            iconColorClass: 'text-cyan-500',
            comparisonValue: `+${newClients}`,
            comparisonText: 'nuevos esta semana',
            comparisonPositive: true,
        };
    });
</script>

<template>
    <template v-if="metricStore.loading">
        <div class="col-span-12 text-center p-8">Cargando métricas...</div>
    </template>
    <template v-else-if="metricStore.error">
        <div class="col-span-12 text-center p-8 text-red-500">{{ metricStore.error }}</div>
    </template>

    <template v-else> 
        <div class="col-span-12 lg:col-span-6 xl:col-span-3">
            <MetricasCard v-bind="ordersProps" />
        </div>

        <div class="col-span-12 lg:col-span-6 xl:col-span-3">
            <MetricasCard v-bind="revenueWeekProps" />
        </div>

        <div class="col-span-12 lg:col-span-6 xl:col-span-3">
            <MetricasCard v-bind="revenueMonthProps" />
        </div>

        <div class="col-span-12 lg:col-span-6 xl:col-span-3">
            <MetricasCard v-bind="usersProps" />
        </div>
    </template>
</template>
