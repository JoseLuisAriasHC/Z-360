<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUsage extends Model
{
    use HasFactory;

    protected $table = 'product_usages';

    protected $fillable = ['nombre'];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withTimestamps();
    }
}
