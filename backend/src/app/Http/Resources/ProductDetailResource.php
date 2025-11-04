<?php

namespace App\Http\Resources;

use App\Models\WebSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Obtener la variante seleccionada desde el contexto
        $selectedVariant = $this->additional['selected_variant'] ?? null;

        // Calcular descuento activo de la variante seleccionada
        $descuentoActivo = false;
        $descuento = 0;
        $precioConDescuento = (float) $selectedVariant->precio;

        if ($selectedVariant && $selectedVariant->descuento > 0 && 
            $selectedVariant->descuento_desde && 
            $selectedVariant->descuento_hasta) {
            $now = now();
            if ($now->between($selectedVariant->descuento_desde, $selectedVariant->descuento_hasta)) {
                $descuentoActivo = true;
                $descuento = $selectedVariant->descuento;
                $precioConDescuento = round($selectedVariant->precio - ($selectedVariant->precio * ($descuento / 100)), 2);
            }
        }

        // Obtener costos de envío desde configuración
        $costoEnvio = (float) WebSettings::getValue('coste_envio', 5);
        $envioGratisDesde = (float) WebSettings::getValue('free_coste_envio_from', 100);

        return [
            // Información del producto base
            'producto' => [
                'id' => $this->id,
                'nombre' => $this->nombre,
                'marca' => [
                    'id' => $this->marca->id ?? null,
                    'nombre' => $this->marca->nombre ?? null,
                ],
                'tipo' => $this->tipo->value ?? null,
                'genero' => $this->genero->value ?? null,
                'descripcion' => $this->descripcion,
                'cierre' => $this->cierre->value ?? null,
                'altura_suela' => $this->altura_suela->value ?? null,
                'plantilla' => $this->plantilla,
            ],

            // Detalles del producto
            'detalles' => [
                'parte_superior' => $this->detail->parte_superior ?? null,
                'parte_inferior' => $this->detail->parte_inferior ?? null,
                'suela' => $this->detail->suela ?? null,
                'cuidados' => $this->detail->cuidados ?? null,
            ],

            // Etiquetas del producto
            'etiquetas' => $this->etiquetas->map(function ($etiqueta) {
                return [
                    'id' => $etiqueta->id,
                    'nombre' => $etiqueta->nombre,
                ];
            }),

            // Variante seleccionada con toda su información
            'variante_seleccionada' => [
                'id' => $selectedVariant->id,
                'color' => [
                    'id' => $selectedVariant->color->id ?? null,
                    'nombre' => $selectedVariant->color->nombre ?? null,
                    'codigo_hex' => $selectedVariant->color->codigo_hex ?? null,
                ],
                'precio' => (float) $selectedVariant->precio,
                'precio_sin_iva' => (float) $selectedVariant->precio_sin_iva,
                'iva' => (float) $selectedVariant->iva,
                'descuento' => $descuento,
                'descuento_activo' => $descuentoActivo,
                'precio_con_descuento' => $precioConDescuento,
                'imagen_principal' => $selectedVariant->imagen_principal 
                    ? asset('storage/product_variants/L_' . $selectedVariant->imagen_principal) 
                    : null,
                // Imágenes adicionales de la variante
                'imagenes_adicionales' => $selectedVariant->images->map(function ($image) {
                    return asset('storage/' . $image->path);
                }),
            ],

            // Todas las variantes del producto (para selector de color)
            'variantes' => $this->productVariants->map(function ($variant) use ($selectedVariant) {
                return [
                    'id' => $variant->id,
                    'color' => [
                        'id' => $variant->color->id ?? null,
                        'nombre' => $variant->color->nombre ?? null,
                        'codigo_hex' => $variant->color->codigo_hex ?? null,
                    ],
                    'imagen_principal' => $variant->imagen_principal 
                        ? asset('storage/product_variants/XS_' . $variant->imagen_principal) 
                        : null,
                    'seleccionada' => $variant->id === $selectedVariant->id,
                ];
            }),

            // Tallas disponibles para la variante seleccionada
            'tallas' => $selectedVariant->sizes->map(function ($size) {
                return [
                    'id' => $size->id,
                    'talla' => [
                        'id' => $size->talla->id ?? null,
                        'numero' => $size->talla->numero ?? null,
                    ],
                    'stock' => $size->stock,
                    'disponible' => $size->stock > 0,
                    'sku' => $size->sku,
                ];
            })->sortBy('talla.numero')->values(),

            // Información de envío
            'envio' => [
                'costo_envio' => $costoEnvio,
                'envio_gratis_desde' => $envioGratisDesde,
                'mensaje' => $precioConDescuento >= $envioGratisDesde 
                    ? 'Envío gratis' 
                    : "Envío gratis en compras superiores a €{$envioGratisDesde}",
            ],
        ];
    }
}