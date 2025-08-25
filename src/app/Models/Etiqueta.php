<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etiqueta extends Model
{
    use HasFactory;

    protected $table = 'etiquetas';

    protected $fillable = ['nombre'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_etiquetas');
    }
}
