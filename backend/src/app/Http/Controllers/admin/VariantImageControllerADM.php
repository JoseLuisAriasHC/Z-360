<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\VariantImage;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Storage;
use Str;

class VariantImageControllerADM extends Controller
{
    /**
     * Obtener las imágenes adicionales de variante
     */
    public function getVariantImages(ProductVariant $productVariant)
    {
        return response()->json([
            'success' => true,
            'data' => $productVariant->images,
        ]);
    }

    public function uploadImages(Request $request, ProductVariant $productVariant)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimes:avif,jpeg,jpg,png,webp|max:10000',
        ]);
        $manager = new ImageManager(new Driver());

        $newImages = [];
        foreach ($request->file('files') as $file) {
            $filenameBase = Str::uuid();
            $img = $manager->read($file->getRealPath());

            $webpPath = "variant_images/{$filenameBase}.webp";
            Storage::disk('public')->put($webpPath, (string) $img->toWebp());

            // Guardar en DB
            $variantImage = $productVariant->images()->create([
                'path' => $webpPath
            ]);

            $newImages[] = $variantImage;
        }

        return response()->json([
            'success' => true,
            'message' => 'Imágenes subidas correctamente.',
            'data' => $newImages
        ]);
    }

    public function deleteImage(VariantImage $variantImage)
    {
        $relativePath = str_replace(Storage::url(''), '', $variantImage->path);
        Storage::disk('public')->delete($relativePath);
        $variantImage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Imagen eliminada correctamente.'
        ]);
    }
}
