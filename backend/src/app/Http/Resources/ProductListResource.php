<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'genero' => $this->genero->value ?? null,
            'tipo' => $this->tipo->value ?? null,
            'marca' => [
                'id' => $this->marca->id ?? null,
                'nombre' => $this->marca->nombre ?? null,
            ],
            'etiquetas' => $this->etiquetas->map(function ($etiqueta) {
                return [
                    'id' => $etiqueta->id,
                    'nombre' => $etiqueta->nombre,
                ];
            }),
            'variants' => $this->productVariants->map(function ($variant) {
                $descuentoActivo = false;
                $descuento = 0;
                
                if ($variant->descuento > 0 && 
                    $variant->descuento_desde && 
                    $variant->descuento_hasta) {
                    $now = now();
                    if ($now->between($variant->descuento_desde, $variant->descuento_hasta)) {
                        $descuentoActivo = true;
                        $descuento = $variant->descuento;
                    }
                }

                return [
                    'id' => $variant->id,
                    'color' => [
                        'id' => $variant->color->id ?? null,
                        'nombre' => $variant->color->nombre ?? null,
                        'codigo_hex' => $variant->color->codigo_hex ?? null,
                    ],
                    'precio' => (float) $variant->precio,
                    'descuento' => $descuento,
                    'descuento_activo' => $descuentoActivo,
                    'precio_con_descuento' => $descuentoActivo 
                        ? round($variant->precio - $descuento, 2)
                        : (float) $variant->precio,
                    'imagen_principal' => $variant->imagen_principal 
                        ? asset('storage/product_variants/M_' . $variant->imagen_principal) 
                        : null,
                    'tiene_stock' => $variant->sizes->sum('stock') > 0,
                ];
            }),
        ];
    }
}