<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    protected $table = 'marcas';

    protected $fillable = [
        'nombre',
        'logo',
        'talla_offset',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function userBrandSizes()
    {
        return $this->hasMany(UserBrandSize::class);
    }
}
