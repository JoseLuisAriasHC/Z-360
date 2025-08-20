<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductVariantRequest;
use App\Http\Resources\ProductVariantResource;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(int $productId)
    {
        $variants = ProductVariant::with(['color', 'sizes'])
            ->where('product_id', $productId)
            ->paginate(config('web.paginacion_por_pagina'));

        return ProductVariantResource::collection($variants);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductVariant $productVariant)
    {
        $productVariant->load(['color', 'sizes', 'images']);

        return response()->json([
            'success' => true,
            'data' => $productVariant,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductVariantRequest $request, ProductVariant $productVariant)
    {
        DB::transaction(function () use ($request, $productVariant) {

            $request->validated();
            if ($request->hasFile('imagen_principal') && $productVariant->imagen_principal && Storage::exists($productVariant->imagen_principal))
                Storage::delete($productVariant->imagen_principal);

            $productVariant->update([
                'precio'           => $request->precio ?? $productVariant->precio,
                'descuento'        => $request->descuento ?? $productVariant->descuento,
                'descuento_desde'  => $request->descuento_desde ?? $productVariant->descuento_desde,
                'descuento_hasta'  => $request->descuento_hasta ?? $productVariant->descuento_hasta,
                'imagen_principal' => $request->imagen_principal ? $request->file('imagen_principal')->store('product_variants') : $productVariant->imagen_principal,
            ]);

            if ($request->has('tallas')) {
                foreach ($request->tallas as $talla) {
                    $productVariant->sizes()->updateOrCreate(
                        ['talla_id' => $talla['talla_id']],
                        ['stock' => $talla['stock'], 'sku' => strtoupper("SKU-{$productVariant->id}-{$talla['talla_id']}")]
                    );
                }
            }

            if ($request->filled('borrar_imagenes')) {
                foreach ($request->borrar_imagenes as $imgId) {
                    $img = $productVariant->images()->find($imgId);
                    if ($img) {
                        Storage::delete($img->path);
                        $img->delete();
                    }
                }
            }

            if ($request->has('imagenes')) {
                foreach ($request->file('imagenes') as $img) {
                    $productVariant->images()->create([
                        'path' => $img->store('variant_images'),
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'data' => $productVariant->load(['sizes', 'images', 'color']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductVariant $productVariant)
    {
        $productVariant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Variante de producto eliminada correctamente',
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'colores'    => 'required|array|min:1',
            'colores.*'  => 'exists:colores,id',
            'tallas'     => 'required|array|min:1',
            'tallas.*'   => 'exists:tallas,id',
        ]);

        $productId = $request->input('product_id');
        $colores   = $request->input('colores');
        $tallas    = $request->input('tallas');

        DB::transaction(function () use ($productId, $colores, $tallas) {
            foreach ($colores as $colorId) {
                $existVariant = ProductVariant::where('product_id', $productId)
                    ->where('color_id', $colorId)
                    ->first();
                if ($existVariant) {
                    continue;
                }

                $variant = ProductVariant::create([
                    'product_id'        => $productId,
                    'color_id'          => $colorId,
                    'precio'            => 0,
                    'descuento'         => null,
                    'imagen_principal'  => null,
                ]);

                foreach ($tallas as $tallaId) {
                    $variant->sizes()->create([
                        'talla_id'   => $tallaId,
                        'stock'      => 0,
                        'sku'        => strtoupper("SKU-{$variant->id}-{$tallaId}"),
                    ]);
                }
            }
        });

        $variants = ProductVariant::with(['color', 'sizes.talla'])
            ->paginate(config('web.paginacion_por_pagina'));

        return ProductVariantResource::collection($variants);
    }
}
