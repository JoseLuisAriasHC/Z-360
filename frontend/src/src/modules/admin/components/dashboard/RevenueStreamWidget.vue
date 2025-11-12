<script setup lang="ts">
    import { useLayout } from '@admin/layout/composables/layout';
    import { ref, watch, onMounted, computed } from 'vue';

    import Chart from 'primevue/chart';
    import apiClient from '@/services/api';

    // Definimos las interfaces para los tipos
    interface ChartDataset {
        label: string;
        data: number[];
        fill: boolean;
        backgroundColor: string;
        borderColor: string;
        tension: number;
    }

    interface ChartData {
        labels: string[];
        datasets: ChartDataset[];
    }

    interface ChartOptions {
        plugins: {
            legend: {
                labels: {
                    color: string;
                };
            };
        };
        scales: {
            x: {
                ticks: {
                    color: string;
                };
                grid: {
                    color: string;
                    drawBorder: boolean;
                };
            };
            y: {
                ticks: {
                    color: string;
                };
                grid: {
                    color: string;
                    drawBorder: boolean;
                };
            };
        };
    }

    interface RawDataset {
        label: string;
        data: number[];
    }

    interface RawChartData {
        labels: string[];
        datasets: RawDataset[];
    }

    // Estado local para los datos del gráfico
    const { getPrimary, getSurface, isDarkTheme } = useLayout();
    const lineData = ref<ChartData>({ labels: [], datasets: [] });
    const lineOptions = ref<ChartOptions>({
        plugins: {
            legend: {
                labels: {
                    color: '#000000',
                },
            },
        },
        scales: {
            x: {
                ticks: {
                    color: '#000000',
                },
                grid: {
                    color: '#e5e7eb',
                    drawBorder: false,
                },
            },
            y: {
                ticks: {
                    color: '#000000',
                },
                grid: {
                    color: '#e5e7eb',
                    drawBorder: false,
                },
            },
        },
    });

    // Estado para la carga y errores
    const loading = ref(true);
    const error = ref<string | null>(null);

    // Datos brutos obtenidos del backend
    const rawChartData = ref<RawChartData | null>(null);

    // Computed para verificar si hay datos
    const hasData = computed(() => {
        return lineData.value && lineData.value.datasets.length > 0;
    });

    // Función para cargar los datos del backend
    const fetchData = async () => {
        loading.value = true;
        error.value = null;
        try {
            const response = await apiClient.get('/admin/dashboard/weekly-performance');
            if (response.data && response.data.data) {
                rawChartData.value = response.data.data;
            } else {
                rawChartData.value = { labels: [], datasets: [] };
            }
        } catch (e) {
            error.value = 'No se pudo cargar la información de rendimiento semanal.';
            rawChartData.value = { labels: [], datasets: [] };
        } finally {
            loading.value = false;
        }
    };

    // Función para configurar opciones de color y asignar datos
    function setColorOptions() {
        // Si aún no tenemos datos, no hacemos nada
        if (!rawChartData.value) return;

        const documentStyle = getComputedStyle(document.documentElement);

        const textColor = documentStyle.getPropertyValue('--text-color');
        const textColorSecondary = documentStyle.getPropertyValue('--text-color-secondary');
        const surfaceBorder = documentStyle.getPropertyValue('--surface-border');

        // Para las líneas, usamos las clases de color primarias y secundarias
        const primaryColor = documentStyle.getPropertyValue('--p-primary-500');
        const secondaryColor = documentStyle.getPropertyValue('--p-primary-300');

        // Mapeamos los datos del backend al formato de Chart.js
        const datasets: ChartDataset[] = rawChartData.value.datasets.map((dataset: RawDataset, index: number) => {
            const color = index === 0 ? primaryColor : secondaryColor;
            return {
                label: dataset.label,
                data: dataset.data,
                fill: false,
                backgroundColor: color,
                borderColor: color,
                tension: 0.4,
            };
        });

        lineData.value = {
            labels: rawChartData.value.labels,
            datasets: datasets,
        };

        // Las opciones del gráfico
        lineOptions.value = {
            plugins: {
                legend: {
                    labels: {
                        color: textColor,
                    },
                },
            },
            scales: {
                x: {
                    ticks: {
                        color: textColorSecondary,
                    },
                    grid: {
                        color: surfaceBorder,
                        drawBorder: false,
                    },
                },
                y: {
                    ticks: {
                        color: textColorSecondary,
                    },
                    grid: {
                        color: surfaceBorder,
                        drawBorder: false,
                    },
                },
            },
        };
    }

    // Watcher que reacciona a los cambios de tema O la carga de datos
    watch(
        [getPrimary, getSurface, isDarkTheme, rawChartData],
        () => {
            setTimeout(() => {
                setColorOptions();
            }, 400);
        },
        { immediate: true }
    );

    onMounted(() => {
        fetchData();
    });
</script>
<template>
    <div class="col-span-12 xl:col-span-6">
        <div class="card">
            <div class="font-semibold text-xl mb-4">Rendimiento Semanal ({{ new Date().getFullYear() }})</div>

            <div v-if="loading" class="text-center p-8">Cargando datos del gráfico...</div>

            <div v-else-if="error" class="text-center p-8 text-red-500">
                {{ error }}
            </div>

            <div v-else-if="!hasData" class="text-center p-8 text-muted-color">No hay datos de rendimiento para el año actual.</div>

            <Chart v-else type="line" :data="lineData" :options="lineOptions"></Chart>
        </div>
    </div>
</template>
