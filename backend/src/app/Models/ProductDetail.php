<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    use HasFactory;

    protected $table = 'product_details';

    protected $fillable = [
        'product_id',
        'parte_superior',
        'parte_inferior',
        'suela',
        'cuidados',
    ];

    protected $casts = [
        'parte_superior' => 'string',
        'parte_inferior' => 'string',
        'suela' => 'string',
        'cuidados' => 'string',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
