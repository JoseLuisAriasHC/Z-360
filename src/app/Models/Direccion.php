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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
