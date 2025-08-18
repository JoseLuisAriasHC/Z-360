<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        $descuentoActivo = $this->getDescuentoActivo();

        return [
            'id' => $this->id,
            'imagen_principal' => $this->imagen_principal,
            'color' => [
                'id'         => $this->color->id,
                'nombre'     => $this->color->nombre,
                'codigo_hex' => $this->color->codigo_hex,
            ],
            'precio' => $this->precio,
            'stock' => $this->sizes->sum('stock'),
            'descuento' => $this->descuento,
            'descuento_activo' => $descuentoActivo,
        ];
    }

    private function getDescuentoActivo(): bool
    {
        $descuentoActivo = false;
        if ($this->descuento && $this->descuento_desde && $this->descuento_hasta) {
            $now = now();
            $descuentoActivo = $this->descuento_desde <= $now && $this->descuento_hasta >= $now;
        }
        return $descuentoActivo;
    }
}
