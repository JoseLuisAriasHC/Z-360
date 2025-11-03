<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductListResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Obtener información completa del producto basándose en una variante
     * 
     * @param ProductVariant $productVariant
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductDetail(ProductVariant $productVariant)
    {
        $product = $productVariant->product()->with([
            'marca',
            'detail',
            'etiquetas',
            'productVariants.color',
            'productVariants.images',
        ])->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        $productVariant->load(['color', 'images', 'sizes.talla']);

        return response()->json([
            'success' => true,
            'data' => (new ProductDetailResource($product))->additional([
                'selected_variant' => $productVariant
            ]),
        ]);
    }

    /**
     * Obtener productos más vendidos (top ventas)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function topVentas(Request $request)
    {
        $genero = $request->query('genero'); // hombre, mujer, unisex
        $perPage = $request->query('per_page', 12);
        $page = $request->query('page', 1);

        // Obtener los productos más vendidos
        $topProductsIds = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('variant_sizes', 'order_items.variant_size_id', '=', 'variant_sizes.id')
            ->join('product_variants', 'variant_sizes.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('orders.estado', '!=', 'cancelado')
            ->when($genero, function ($query) use ($genero) {
                $query->where('products.genero', $genero);
            })
            ->select('products.id', DB::raw('SUM(order_items.cantidad) as total_vendido'))
            ->groupBy('products.id')
            ->orderByDesc('total_vendido')
            ->pluck('products.id');

        // Si no hay productos vendidos, retornar vacío
        if ($topProductsIds->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage,
                    'total' => 0,
                    'from' => null,
                    'to' => null,
                    'has_more_pages' => false,
                ],
            ]);
        }

        // Obtener productos con paginación manteniendo el orden de ventas
        $products = Product::with([
            'marca',
            'etiquetas',
            'productVariants.color',
            'productVariants.sizes'
        ])
            ->whereIn('id', $topProductsIds)
            ->has('productVariants') // Solo productos con al menos una variante
            ->orderByRaw('FIELD(id, ' . $topProductsIds->implode(',') . ')')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => ProductListResource::collection($products),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'has_more_pages' => $products->hasMorePages(),
            ],
        ]);
    }

    /**
     * Obtener productos con ofertas activas
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ofertas(Request $request)
    {
        $genero = $request->query('genero');
        $perPage = $request->query('per_page', 12);
        $page = $request->query('page', 1);
        $now = now();

        $products = Product::with([
            'marca',
            'etiquetas',
            'productVariants.color',
            'productVariants.sizes'
        ])
            ->when($genero, function ($query) use ($genero) {
                $query->where('genero', $genero);
            })
            ->has('productVariants') // Solo productos con al menos una variante
            ->whereHas('productVariants', function ($query) use ($now) {
                $query->where('descuento', '>', 0)
                    ->where('descuento_desde', '<=', $now)
                    ->where('descuento_hasta', '>=', $now);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => ProductListResource::collection($products),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'has_more_pages' => $products->hasMorePages(),
            ],
        ]);
    }

    /**
     * Obtener productos nuevos (creados este mes)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function novedades(Request $request)
    {
        $genero = $request->query('genero');
        $perPage = $request->query('per_page', 12);
        $page = $request->query('page', 1);

        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        $products = Product::with([
            'marca',
            'etiquetas',
            'productVariants.color',
            'productVariants.sizes'
        ])
            ->when($genero, function ($query) use ($genero) {
                $query->where('genero', $genero);
            })
            ->has('productVariants') // Solo productos con al menos una variante
            ->whereBetween('created_at', [$inicioMes, $finMes])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => ProductListResource::collection($products),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'has_more_pages' => $products->hasMorePages(),
            ],
        ]);
    }

    /**
     * Listado de productos con paginación para scroll infinito
     * Filtrado por género y/o tipo de producto
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listado(Request $request)
    {
        $genero = $request->query('genero'); // hombre, mujer, unisex
        $tipoProducto = $request->query('tipo_producto'); // urbanas, deportivas, botas, sandalias
        $perPage = $request->query('per_page', 12);
        $page = $request->query('page', 1);

        $query = Product::with([
            'marca',
            'etiquetas',
            'productVariants.color',
            'productVariants.sizes'
        ]);

        // Filtrar por género si se proporciona
        if ($genero) {
            $query->where('genero', $genero);
        }

        // Filtrar por tipo de producto si se proporciona
        if ($tipoProducto) {
            $query->where('tipo', $tipoProducto);
        }

        // Solo productos con al menos una variante
        $query->has('productVariants');

        // Ordenar por más recientes primero
        $query->orderBy('created_at', 'desc');

        // Paginar resultados
        $products = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => ProductListResource::collection($products),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'has_more_pages' => $products->hasMorePages(),
            ],
        ]);
    }
}
