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

    protected static function boot()
    {
        parent::boot();
        static::creating(function (VariantSize $size) {
            if (empty($size->sku)) {
                $size->sku = strtoupper("SKU-{$size->product_variant_id}-{$size->talla_id}");
            }
        });
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function talla()
    {
        return $this->belongsTo(Talla::class);
    }
}
