<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductVariantRequest;
use App\Http\Resources\ProductVariantResource;
use App\Models\ProductVariant;
use App\Models\VariantSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Storage;
use Str;

class ProductVariantControllerADM extends Controller
{
    private const SIZES = [
        'L'  => 680,
        'M'  => 485,
        'S'  => 164,
        'XS' => 78,
    ];

    /**
     * Mostrar una lista de los productos variables de un producto en el ADM
     */
    public function index(int $productId)
    {
        $variants = ProductVariant::with(['color', 'sizes'])
            ->where('product_id', $productId)
            ->get();

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

            if ($request->hasFile('imagen_principal')) {
                $this->deleteOldImagenPrincipal($productVariant);
                $this->saveImagenPrincipal($manager, $request->file('imagen_principal'), $productVariant);
            }

            $productVariant->update([
                'precio'            => $request->precio ?? $productVariant->precio,
                'descuento'         => $request->descuento ?? $productVariant->descuento,
                'descuento_desde'   => $request->descuento_desde ?? $productVariant->descuento_desde,
                'descuento_hasta'   => $request->descuento_hasta ?? $productVariant->descuento_hasta,
                'imagen_principal'  => $productVariant->imagen_principal,
                'imagen_principal_jpeg' => $productVariant->imagen_principal_jpeg,
            ]);

            if ($request->has('tallas')) {
                $this->updateSizes($productVariant, $request->tallas);
            }

            if ($request->filled('borrar_imagenes')) {
                $this->deleteVariantImages($request->borrar_imagenes, $productVariant);
            }

            if ($request->hasFile('imagenes')) {
                $this->saveVariantImages($manager, $request->file('imagenes'), $productVariant);
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
        $this->deleteOldImagenPrincipal($productVariant);
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
            'precio'   => 'sometimes|numeric|min:0',
        ]);

        $productId = $request->input('product_id');
        $colores   = $request->input('colores');
        $tallas    = $request->input('tallas');
        $precio    = $request->input('precio');

        DB::transaction(function () use ($productId, $colores, $tallas) {
            foreach ($colores as $colorId) {
                $variant = ProductVariant::where('product_id', $productId)
                    ->where('color_id', $colorId)
                    ->first();
                if (!$variant) {
                    $variant = ProductVariant::create([
                        'product_id'        => $productId,
                        'color_id'          => $colorId,
                        'precio'            => $precio ?? 0,
                        'descuento'         => null,
                        'imagen_principal'  => null,
                        'imagen_principal_jpeg' => null,
                    ]);
                }

                foreach ($tallas as $tallaId) {
                    $existVariantSize = VariantSize::where('product_variant_id', $variant->id)
                        ->where('color_id', $colorId)
                        ->first();
                    if (!$existVariantSize) {
                        $variant->sizes()->create([
                            'talla_id'   => $tallaId,
                            'stock'      => 0,
                        ]);
                    }
                }
            }
        });

        $variants = ProductVariant::with(['color', 'sizes.talla'])
            ->paginate(config('web.paginacion_por_pagina'));

        return ProductVariantResource::collection($variants);
    }

    /**
     * Eliminar imágenes antiguas (WebP y JPEG)
     */
    private function deleteOldImagenPrincipal(ProductVariant $productVariant): void
    {
        if (!$productVariant->imagen_principal) return;

        $filenameBase = pathinfo($productVariant->imagen_principal, PATHINFO_FILENAME);

        // Eliminar todas las versiones WebP y JPEG
        foreach (self::SIZES as $sizeName => $size) {
            $webpPath = "product_variants/{$sizeName}_{$filenameBase}.webp";
            $jpegPath = "product_variants/{$sizeName}_{$filenameBase}.jpg";

            Storage::disk('public')->delete($webpPath);
            Storage::disk('public')->delete($jpegPath);
        }
    }

    /**
     * Guardar imagen principal en ambos formatos (WebP + JPEG)
     */
    private function saveImagenPrincipal(ImageManager $manager, $file, ProductVariant $productVariant): void
    {
        $filenameBase = Str::uuid();

        foreach (self::SIZES as $sizeName => $tamano) {
            // Leer y redimensionar imagen
            $img = $manager->read($file->getRealPath());
            $img->resize($tamano, $tamano, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Guardar como WebP (para web)
            $webpPath = "product_variants/{$sizeName}_{$filenameBase}.webp";
            Storage::disk('public')->put($webpPath, (string) $img->toWebp(90));

            // Guardar como JPEG solo para tamaño S (para PDFs)
            if ($sizeName === 'S') {
                $jpegPath = "product_variants/{$sizeName}_{$filenameBase}.jpg";
                Storage::disk('public')->put($jpegPath, (string) $img->toJpeg(90));
            }
        }

        // Guardar nombres base (sin prefijo de tamaño)
        $productVariant->imagen_principal = "{$filenameBase}.webp";
        $productVariant->imagen_principal_jpeg = "{$filenameBase}.jpg";
    }

    /**
     * Guardar imágenes adicionales de variante
     */
    private function saveVariantImages(ImageManager $manager, array $files, ProductVariant $productVariant): void
    {
        foreach ($files as $file) {
            $filenameBase = Str::uuid();
            $img = $manager->read($file->getRealPath());

            $webpPath = "variant_images/{$filenameBase}.webp";
            Storage::disk('public')->put($webpPath, (string) $img->toWebp());

            $productVariant->images()->create(['path' => "{$filenameBase}.webp"]);
        }
    }

    /**
     * Eliminar imágenes adicionales de variante
     */
    private function deleteVariantImages(array $imageIds, ProductVariant $productVariant): void
    {
        foreach ($imageIds as $imgId) {
            $img = $productVariant->images()->find($imgId);
            if ($img) {
                Storage::disk('public')->delete("variant_images/{$img->path}");
                $img->delete();
            }
        }
    }

    /**
     * Actualizar tallas de la variante
     */
    private function updateSizes(ProductVariant $productVariant, array $tallas): void
    {
        foreach ($tallas as $talla) {
            $productVariant->sizes()->updateOrCreate(
                ['talla_id' => $talla['talla_id']],
                ['stock' => $talla['stock']]
            );
        }
    }
}
