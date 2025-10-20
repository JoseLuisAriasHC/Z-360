<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductControllerADM extends Controller
{
    /**
     * Listar todos los productos en el ADM
     */
    public function index()
    {
        $productos = Product::with('marca')->get();

        return ProductResource::collection($productos);
    }

    /**
     * Listar todos los productos en la Web
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
     * Guardar un Producto en el ADM
     */
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());

        $product->detail()->create([
            'parte_superior' => null,
            'parte_inferior' => null,
            'suela' => null,
            'cuidados' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto creado correctamente',
            'data' => $product
        ], 201);
    }

    /**
     * Mostrar un producto específica en el ADM
     */
    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Actualizar un producto en el ADM
     */
    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado correctamente',
            'data' => $product
        ]);
    }

    /**
     * Eliminar un producto en el ADM
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente'
        ]);
    }
}
