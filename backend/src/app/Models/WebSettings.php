<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebSettings extends Model
{
    use HasFactory;

    protected $table = 'web_settings';

    protected $fillable = [
        'valor',
        'nombre',
        'descripcion',
    ];

    public static function getValue(string $clave, $default = null)
    {
        $setting = self::where('clave', $clave)->first();
        return $setting ? $setting->valor : $default;
    }
}
