<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    protected $table = 'direcciones';

    protected $fillable = [
        'user_id',
        'titulo',
        'nombre',
        'calle',
        'numero_calle',
        'piso_info',
        'ciudad',
        'cp',
        'telefono',
        'predeterminada',
    ];

    protected $casts = [
        'predeterminada' => 'boolean',
        'user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Garantizar una sola dirección predeterminada por usuario
    protected static function booted()
    {
        static::saving(function ($direccion) {
            if ($direccion->predeterminada) {
                static::where('user_id', $direccion->user_id)
                    ->where('id', '!=', $direccion->id)
                    ->update(['predeterminada' => false]);
            }
        });
    }
}
