<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    protected $table = 'colores';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'codigo_hex',
    ];

    protected $casts = [
        'id' => 'integer',
        'nombre' => 'string',
        'codigo_hex' => 'string',
    ];

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function setCodigoHexAttribute(?string $value): void
    {
        if ($value && !str_starts_with($value, '#')) {
            $value = '#' . ltrim($value, '#');
        }
        $this->attributes['codigo_hex'] = strtoupper($value);
    }
}
