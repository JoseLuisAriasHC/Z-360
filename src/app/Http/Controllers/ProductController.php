<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Listar todos los productos
     */
    public function index()
    {
        $productos = Product::with('marca')->paginate(config('web.paginacion_por_pagina'));

        return ProductResource::collection($productos);
    }

    /**
     * Listar todos los productos
     */
    public function search(Request $request)
    {
        $query = Product::query()->with('marca');

        if ($request->filled('genero')) {
            $query->where('genero', $request->input('genero'));
        }

        if ($request->filled('marca')) {
            $query->where('marca_id', $request->input('marca'));
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                    ->orWhere('tipo', 'LIKE', "%{$search}%")
                    ->orWhere('genero', 'LIKE', "%{$search}%")
                    ->orWhereHas('marca', fn($m) => $m->where('nombre', 'LIKE', "%{$search}%"));
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(config('web.paginacion_por_pagina'));

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Guardar un Producto
     */
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Producto creado correctamente',
            'data' => $product
        ], 201);
    }


    /**
     * Mostrar un producto específica.
     */
    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Actualizar un producto.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Marca actualizada correctamente',
            'data' => $product
        ]);
    }

    /**
     * Eliminar un producto.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Marca eliminada correctamente'
        ]);
    }
}
