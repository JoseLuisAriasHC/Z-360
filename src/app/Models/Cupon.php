<?php

namespace App\Models;

use App\Enums\TipoCupon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
    use HasFactory;

    protected $table = 'cupones';

    protected $fillable = [
        'codigo',
        'descuento',
        'tipo',
        'fecha_expiracion',
        'uso_maximo',
    ];

    protected $casts = [
        'tipo' => TipoCupon::class,
        'fecha_expiracion' => 'datetime',
        'descuento' => 'float',
        'uso_maximo' => 'integer',
    ];
}
