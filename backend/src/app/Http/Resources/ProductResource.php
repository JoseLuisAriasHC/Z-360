<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'nombre'    => $this->nombre,
            'marca'     => [
                'nombre'    => $this->marca->nombre,
                'logo'      => $this->marca->logo,
            ],
            'tipo'      => $this->tipo,
            'genero'    => $this->genero,
            'cierre'    => $this->cierre,
            'stock'     => $this->productVariants()
                            ->withSum('sizes', 'stock')
                            ->get()
                            ->sum('sizes_sum_stock'),
        ];
    }
}
