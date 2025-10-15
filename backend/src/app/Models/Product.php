<?php

namespace App\Models;

use App\Enums\AlturaSuelaProduct;
use App\Enums\CierreProducto;
use App\Enums\Genero;
use App\Enums\TipoProducto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'nombre',
        'marca_id',
        'tipo',
        'genero',
        'descripcion',
        'cierre',
        'altura_suela',
        'plantilla',
    ];

    protected $casts = [
        'tipo' => TipoProducto::class,
        'genero' => Genero::class,
        'cierre' => CierreProducto::class,
        'altura_suela' => AlturaSuelaProduct::class,
    ];

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function etiquetas()
    {
        return $this->belongsToMany(Etiqueta::class, 'product_etiquetas');
    }

    public function detail()
    {
        return $this->hasOne(ProductDetail::class);
    }

    public function usages()
    {
        return $this->belongsToMany(ProductUsage::class)->withTimestamps();
    }
}
