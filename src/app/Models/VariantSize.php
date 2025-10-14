<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantSize extends Model
{
    use HasFactory;

    protected $table = 'variant_sizes';

    protected $fillable = [
        'product_variant_id',
        'talla_id',
        'stock',
        'sku',
    ];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function talla()
    {
        return $this->belongsTo(Talla::class);
    }

    public function setSkuAttribute($value)
    {
        if (!$value && $this->product_variant_id && $this->talla_id) {
            $this->attributes['sku'] = strtoupper("SKU-{$this->product_variant_id}-{$this->talla_id}");
        } else {
            $this->attributes['sku'] = $value;
        }
    }
}
