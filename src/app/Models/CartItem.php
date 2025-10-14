<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $table = 'cart_items';

    protected $fillable = [
        'shopping_cart_id',
        'variant_size_id',
        'cantidad'
    ];

    protected $casts = [
        'shopping_cart_id' => 'integer',
        'variant_size_id' => 'integer',
        'cantidad' => 'integer',
    ];

    public function cart()
    {
        return $this->belongsTo(ShoppingCart::class, 'shopping_cart_id');
    }

    public function variantSize()
    {
        return $this->belongsTo(VariantSize::class);
    }
}
