<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBrandSize extends Model
{
    use HasFactory;

    protected $table = 'user_brand_sizes';

    protected $fillable = [
        'user_id',
        'marca_id',
        'talla_id',
        'talla_personalizada',
    ];

    protected $casts = [
        'talla_personalizada' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function talla()
    {
        return $this->belongsTo(Talla::class);
    }
}
