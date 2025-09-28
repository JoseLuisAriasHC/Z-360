<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $table = 'product_variants';

    protected $fillable = [
        'product_id',
        'color_id',
        'precio',
        'precio_sin_iva',
        'iva',
        'imagen_principal',
        'descuento',
        'descuento_desde',
        'descuento_hasta',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function sizes()
    {
        return $this->hasMany(VariantSize::class);
    }

    public function images()
    {
        return $this->hasMany(VariantImage::class);
    }

    public function setPrecioAttribute($value)
    {
        $ivaPorcentaje = (float) WebSettings::getValue('iva', 21);
        $iva = $ivaPorcentaje / 100;

        $this->attributes['precio'] = $value;
        $this->attributes['iva'] = round($value * $iva, 2);
        $this->attributes['precio_sin_iva'] = round($value - ($value * $iva), 2);
    }
}
