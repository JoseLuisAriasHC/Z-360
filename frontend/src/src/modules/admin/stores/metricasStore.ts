import { defineStore } from 'pinia';
import { ref } from 'vue';
import apiClient from '@/services/api';

// Interfaz de los datos del backend
export interface MetricData {
    current_week_count?: number;
    difference?: number;
    comparison_percentage?: number;
    current_week_revenue?: number;
    current_month_revenue?: number;
    total_clients?: number;
    new_clients_this_week?: number;
}

export interface DashboardMetrics {
    orders: MetricData;
    revenue_week: MetricData;
    revenue_month: MetricData;
    users: MetricData;
}

export const useMetricStore = defineStore('metrics', () => {
    const metrics = ref<DashboardMetrics | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const fetchMetricas = async () => {
        loading.value = true;
        error.value = null;
        try {
            const response = await apiClient.get<{ data: DashboardMetrics }>('/admin/dashboard/metricas');
            metrics.value = response.data.data;
        } catch (err: any) {
            error.value = 'Error al cargar las métricas del dashboard.';
            console.error('Metrics loading error:', err);
        } finally {
            loading.value = false;
        }
    };

    return {
        metrics,
        loading,
        error,
        fetchMetricas,
    };
});