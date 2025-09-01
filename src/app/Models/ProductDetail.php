<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'parte_superior',
        'parte_inferior',
        'suela',
        'cuidados',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
