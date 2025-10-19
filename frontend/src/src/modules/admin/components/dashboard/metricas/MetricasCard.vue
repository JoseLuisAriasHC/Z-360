<script setup lang="ts">
    import { computed } from 'vue';

    export interface MetricasCardProps {
        title: string;
        value: string | number;
        icon: string; // Ejemplo: 'pi pi-shopping-cart'
        iconBgClass: string; // Ejemplo: 'bg-blue-100 dark:bg-blue-400/10'
        iconColorClass: string; // Ejemplo: 'text-blue-500'
        comparisonValue: string; // Ejemplo: '+24', '-10%'
        comparisonText: string; // Ejemplo: 'esta semana', 'desde la última semana'
        comparisonPositive: boolean; // Para el color verde/rojo de la comparación
    }

    const props = defineProps<MetricasCardProps>();

    const comparisonClass = computed(() => {
        return props.comparisonPositive ? 'text-green-500' : 'text-red-500';
    });
</script>
<template>
    <div class="card mb-0">
        <div class="flex justify-between mb-4">
            <div>
                <span class="block text-muted-color font-medium mb-4">{{ title }}</span>
                <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">{{ value }}</div>
            </div>
            <div :class="[iconBgClass, 'flex items-center justify-center rounded-border']" style="width: 2.5rem; height: 2.5rem">
                <i :class="[icon, iconColorClass, '!text-xl']"></i>
            </div>
        </div>
        <span :class="[comparisonClass, 'font-medium']">{{ comparisonValue }}</span>
        <span class="text-muted-color ml-1">{{ comparisonText }}</span>
    </div>
</template>
