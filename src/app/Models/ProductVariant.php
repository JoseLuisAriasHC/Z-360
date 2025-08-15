<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'color_id',
        'precio',
        'imagen_principal',
        'descuento',
        'descuento_desde',
        'descuento_hasta',
    ];

    protected $dates = ['descuento_desde', 'descuento_hasta'];

    protected $appends = ['descuento_activo', 'precio_final'];

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

    // saber si su descuento esta activo
    // saber el precio con y sin descuento
}
