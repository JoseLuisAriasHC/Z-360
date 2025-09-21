<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductVariantRequest;
use App\Http\Resources\ProductVariantResource;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Storage;
use Str;

class ProductVariantController extends Controller
{
    /**
     * Mostrar una lista de los productos variables de un producto en el ADM
     */
    public function index(int $productId)
    {
        $variants = ProductVariant::with(['color', 'sizes'])
            ->where('product_id', $productId)
            ->paginate(config('web.paginacion_por_pagina'));

        return ProductVariantResource::collection($variants);
    }

    /**
     * Mostrar un producto variable en el ADM
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
     * Actualizar un producto variable con sus tallas e imagenes 
     */
    public function update(ProductVariantRequest $request, ProductVariant $productVariant)
    {
        DB::transaction(function () use ($request, $productVariant) {

            $request->validated();
            $manager = new ImageManager(new Driver());
            $sizes = [
                'L'  => 680,
                'M'  => 485,
                'S'  => 164,
                'XS' => 78,
            ];

            // --- Manejo de imagen principal ---
            if ($request->hasFile('imagen_principal')) {
                // Si ya existe una imagen principal, eliminarla junto con sus versiones
                if ($productVariant->imagen_principal && Storage::disk('public')->exists($productVariant->imagen_principal)) {
                    $filenameBase = pathinfo($productVariant->imagen_principal, PATHINFO_FILENAME);

                    // Borrar también las versiones generadas
                    foreach ($sizes as $sizeName) {
                        $generatedPath = "product_variants/{$sizeName}_{$filenameBase}.webp";
                        if (Storage::disk('public')->exists($generatedPath)) {
                            Storage::disk('public')->delete($generatedPath);
                        }
                    }
                }
                $filenameBase = Str::uuid();

                // Generar las versiones redimensionadas en WebP
                foreach ($sizes as $sizeName => $tamano) {
                    $img = $manager->read($request->file('imagen_principal')->getRealPath());

                    $img->resize($tamano, $tamano, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                    $generatedPath = "product_variants/{$sizeName}_{$filenameBase}.webp";
                    Storage::disk('public')->put($generatedPath, (string) $img->toWebp(90));
                }

                $productVariant->imagen_principal = "product_variants/{$filenameBase}.webp";
            }

            $productVariant->update([
                'precio'           => $request->precio ?? $productVariant->precio,
                'descuento'        => $request->descuento ?? $productVariant->descuento,
                'descuento_desde'  => $request->descuento_desde ?? $productVariant->descuento_desde,
                'descuento_hasta'  => $request->descuento_hasta ?? $productVariant->descuento_hasta,
                'imagen_principal' => $productVariant->imagen_principal,
            ]);

            // --- Manejo de tallas ---
            if ($request->has('tallas')) {
                foreach ($request->tallas as $talla) {
                    $productVariant->sizes()->updateOrCreate(
                        ['talla_id' => $talla['talla_id']],
                        ['stock' => $talla['stock'], 'sku' => strtoupper("SKU-{$productVariant->id}-{$talla['talla_id']}")]
                    );
                }
            }

            // --- Borrado de imágenes adicionales ---
            if ($request->filled('borrar_imagenes') && is_array($request->borrar_imagenes)) {
                foreach ($request->borrar_imagenes as $imgId) {
                    $img = $productVariant->images()->find($imgId);
                    if ($img) {
                        Storage::disk('public')->delete($img->path);
                        $img->delete();
                    }
                }
            }

            if ($request->has('imagenes') && is_array($request->imagenes)) {
                foreach ($request->file('imagenes') as $img) {
                    $filenameBase = Str::uuid();

                    $resized = $manager->read($img->getRealPath());
                    $webpPath = "variant_images/{$filenameBase}.webp";
                    Storage::disk('public')->put($webpPath, (string) $resized->toWebp(90));

                    $productVariant->images()->create([
                        'path' => $webpPath,
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
     * Eliminar un producto variable
     */
    public function destroy(ProductVariant $productVariant)
    {
        $productVariant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Variante de producto eliminada correctamente',
        ]);
    }

    /**
     * Generar los productos variables de un producto
     */
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
