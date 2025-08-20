<?php

namespace App\Models;

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

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
