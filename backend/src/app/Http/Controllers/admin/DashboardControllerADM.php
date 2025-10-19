<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardControllerADM extends Controller
{
    /**
     * Obtiene todas las métricas del dashboard
     */
    public function getMetricas()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $this->getOrderMetricas(),
                'revenue_week' => $this->getRevenueWeekMetricas(),
                'revenue_month' => $this->getRevenueMonthMetricas(),
                'users' => $this->getUserMetricas(),
            ]
        ]);
    }

    // --- Métodos de Pedidos (Card 1) ---

    /**
     * Obtiene el número de pedidos de esta semana y su comparación con la anterior.
     */
    private function getOrderMetricas(): array
    {
        // Rango de la semana actual (Lunes a Domingo)
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        // Rango de la semana anterior
        $startOfLastWeek = $startOfWeek->copy()->subWeek();
        $endOfLastWeek = $endOfWeek->copy()->subWeek();

        // Pedidos de esta semana (solo pedidos pagados o confirmados)
        $currentWeekOrders = Order::whereBetween('fecha_pedido', [$startOfWeek, $endOfWeek])
            ->whereIn('estado', ['confirmado', 'procesando', 'enviado', 'entregado'])
            ->count();

        // Pedidos de la semana anterior
        $lastWeekOrders = Order::whereBetween('fecha_pedido', [$startOfLastWeek, $endOfLastWeek])
            ->whereIn('estado', ['confirmado', 'procesando', 'enviado', 'entregado'])
            ->count();

        $difference = $currentWeekOrders - $lastWeekOrders;
        $comparison = 0;

        if ($lastWeekOrders > 0) {
            $comparison = round(($difference / $lastWeekOrders) * 100, 2);
        } elseif ($currentWeekOrders > 0) {
            $comparison = 100; // Caso donde la semana pasada hubo 0 y esta semana > 0
        }

        return [
            'current_week_count' => $currentWeekOrders,
            'last_week_count' => $lastWeekOrders,
            'difference' => $difference, // positivo, negativo o cero
            'comparison_percentage' => $comparison,
        ];
    }

    // --- Métodos de Ganancias Semanales (Card 2) ---

    /**
     * Obtiene la ganancia semanal y su comparación con la semana anterior.
     */
    private function getRevenueWeekMetricas(): array
    {
        // Rangos de tiempo (igual que en getOrderMetrics)
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);
        $startOfLastWeek = $startOfWeek->copy()->subWeek();
        $endOfLastWeek = $endOfWeek->copy()->subWeek();

        // Ganancia de esta semana (total de pedidos pagados o confirmados)
        $currentWeekRevenue = Order::whereBetween('fecha_pedido', [$startOfWeek, $endOfWeek])
            ->whereIn('estado', ['confirmado', 'procesando', 'enviado', 'entregado'])
            ->sum('total');

        // Ganancia de la semana anterior
        $lastWeekRevenue = Order::whereBetween('fecha_pedido', [$startOfLastWeek, $endOfLastWeek])
            ->whereIn('estado', ['confirmado', 'procesando', 'enviado', 'entregado'])
            ->sum('total');

        $difference = round($currentWeekRevenue - $lastWeekRevenue, 2);
        $comparison = 0;

        if ($lastWeekRevenue > 0) {
            $comparison = round(($difference / $lastWeekRevenue) * 100, 2);
        } elseif ($currentWeekRevenue > 0) {
            $comparison = 100;
        }

        return [
            'current_week_revenue' => round($currentWeekRevenue, 2),
            'last_week_revenue' => round($lastWeekRevenue, 2),
            'difference' => $difference,
            'comparison_percentage' => $comparison,
        ];
    }

    // --- Métodos de Ganancias Mensuales (Card 3) ---

    /**
     * Obtiene la ganancia mensual y su comparación con el mes anterior.
     */
    private function getRevenueMonthMetricas(): array
    {
        // Rango del mes actual
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Rango del mes anterior
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        // Ganancia de este mes
        $currentMonthRevenue = Order::whereBetween('fecha_pedido', [$startOfMonth, $endOfMonth])
            ->whereIn('estado', ['confirmado', 'procesando', 'enviado', 'entregado'])
            ->sum('total');

        // Ganancia del mes anterior
        $lastMonthRevenue = Order::whereBetween('fecha_pedido', [$startOfLastMonth, $endOfLastMonth])
            ->whereIn('estado', ['confirmado', 'procesando', 'enviado', 'entregado'])
            ->sum('total');

        $difference = round($currentMonthRevenue - $lastMonthRevenue, 2);
        $comparison = 0;

        if ($lastMonthRevenue > 0) {
            $comparison = round(($difference / $lastMonthRevenue) * 100, 2);
        } elseif ($currentMonthRevenue > 0) {
            $comparison = 100;
        }

        return [
            'current_month_revenue' => round($currentMonthRevenue, 2),
            'last_month_revenue' => round($lastMonthRevenue, 2),
            'difference' => $difference,
            'comparison_percentage' => $comparison,
        ];
    }

    // --- Métodos de Usuarios (Card 4) ---

    /**
     * Obtiene el total de usuarios no-admin y los registrados esta semana.
     */
    private function getUserMetricas(): array
    {
        // Total de usuarios 'cliente'
        $totalClients = User::where('rol', 'cliente')->count();

        // Rango de la semana actual (Lunes a Domingo) para la creación
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        $newClientsThisWeek = User::where('rol', 'cliente')
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->count();

        return [
            'total_clients' => $totalClients,
            'new_clients_this_week' => $newClientsThisWeek,
        ];
    }

    /**
     * Obtiene el top 10 de productos más vendidos en los últimos 3 meses.
     */
    public function getTopVentasProducts()
    {
        // 1. Definir el rango de 3 meses
        $threeMonthsAgo = Carbon::now()->subMonths(3);

        // 2. Consulta avanzada para obtener ventas de productos base
        $topProductsQuery = DB::table('orders')
            // Filtrar solo pedidos pagados/confirmados
            ->where('orders.fecha_pedido', '>=', $threeMonthsAgo)
            ->whereIn('orders.estado', ['confirmado', 'procesando', 'enviado', 'entregado'])

            // Unir a OrderItem
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            // Unir a VariantSize
            ->join('variant_sizes', 'order_items.variant_size_id', '=', 'variant_sizes.id')
            // Unir a ProductVariant
            ->join('product_variants', 'variant_sizes.product_variant_id', '=', 'product_variants.id')
            // Unir a Product (El producto base)
            ->join('products', 'product_variants.product_id', '=', 'products.id')

            ->select(
                'products.id as product_id',
                'products.nombre as product_nombre',
                'products.tipo as product_tipo',
                DB::raw('SUM(order_items.cantidad) as total_vendido')
            )
            // Agrupar por producto base
            ->groupBy('products.id', 'products.nombre', 'products.tipo')
            ->orderByDesc('total_vendido')
            ->get();

        // 3. Obtener el total de ventas (en ítems) por cada TIPO de producto en el periodo
        $totalVentasPorTipo = $topProductsQuery->groupBy('product_tipo')->mapWithKeys(function ($items, $key) {
            return [$key => $items->sum('total_vendido')];
        });

        // 4. Calcular el porcentaje y limitar al top 10
        $topProducts = $topProductsQuery->map(function ($product) use ($totalVentasPorTipo) {
            $totalTipo = $totalVentasPorTipo[$product->product_tipo] ?? 1; // Evitar división por cero

            $percentage = round(($product->total_vendido / $totalTipo) * 100, 2);

            return [
                'id' => $product->product_id,
                'nombre' => $product->product_nombre,
                'tipo_producto' => $product->product_tipo,
                'total_vendido' => (int)$product->total_vendido,
                'porcentaje_tipo' => $percentage, // El porcentaje que ocupa en las ventas de su tipo (urbana, deportiva, etc.)
            ];
        })->take(10)->values(); // Tomar los 10 primeros y reindexar

        return response()->json([
            'success' => true,
            'data' => $topProducts
        ]);
    }

    /**
     * Obtiene las métricas semanales de pedidos y ganancias del año actual.
     * @return JsonResponse
     */
    public function getWeeklyPerformance()
    {
        // Usaremos Carbon para obtener el inicio y fin del año actual.
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();
        $currentYear = Carbon::now()->year;

        // Generar la colección de semanas para asegurar 52/53 etiquetas
        $start = $startOfYear->copy();
        $weeklyData = collect();
        $weekNumber = 1;

        // Iterar desde el inicio del año hasta la semana actual (o fin de año si ya terminó)
        while ($start->lessThanOrEqualTo(Carbon::now()) && $start->year === $currentYear) {
            $weeklyData->put($weekNumber, [
                'label' => "Semana " . $weekNumber,
                'orders_count' => 0,
                'total_revenue' => 0.00,
            ]);
            $start->addWeek();
            $weekNumber++;
        }

        // Si el año termina antes de la última iteración, es posible que no se incluyan semanas.
        // Usaremos el número de semana ISO (modificable si tu DB no usa ISO 8601).

        // Obtener el número de semanas en el año (normalmente 52 o 53)
        $maxWeeks = (int)$endOfYear->weekOfYear;

        // Si necesitas rellenar hasta la semana 52/53
        for ($i = $weekNumber; $i <= $maxWeeks; $i++) {
            $weeklyData->put($i, [
                'label' => "Semana " . $i,
                'orders_count' => 0,
                'total_revenue' => 0.00,
            ]);
        }

        // Obtener los datos reales de la base de datos agrupados por semana
        // NOTA IMPORTANTE: WEEK(fecha, 3) usa el modo 3 (ISO 8601, Lunes es el primer día)
        // Asegúrate de que este modo de semana coincida con tu configuración de DB/región.
        $results = Order::select(
            DB::raw('WEEK(fecha_pedido, 3) as week'),
            DB::raw('COUNT(id) as orders_count'),
            DB::raw('SUM(total) as total_revenue')
        )
            ->whereYear('fecha_pedido', $currentYear)
            ->whereIn('estado', ['confirmado', 'procesando', 'enviado', 'entregado'])
            ->groupBy(DB::raw('WEEK(fecha_pedido, 3)'))
            ->orderBy(DB::raw('WEEK(fecha_pedido, 3)'))
            ->get();

        // Llenar el array inicial con los datos obtenidos
        foreach ($results as $result) {
            $week = (int)$result->week;
            if ($weeklyData->has($week)) {
                $weeklyData[$week]['orders_count'] = (int)$result->orders_count;
                $weeklyData[$week]['total_revenue'] = round((float)$result->total_revenue, 2);
            }
        }

        // Transformar al formato JSON requerido
        $labels = $weeklyData->pluck('label');
        $ordersData = $weeklyData->pluck('orders_count');
        $revenueData = $weeklyData->pluck('total_revenue');

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Ganancias (€)',
                        'data' => $revenueData,
                    ],
                    [
                        'label' => 'Pedidos',
                        'data' => $ordersData,
                    ],
                ]
            ]
        ]);
    }
}
