<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'variant_size_id',
        'cantidad',
        'precio_unitario'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function variantSize()
    {
        return $this->belongsTo(VariantSize::class);
    }
}
