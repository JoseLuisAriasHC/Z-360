<?php

namespace App\Models;

use App\Enums\TipoCupon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'token',
        'cupon_codigo',
        'estado',
        'subtotal',
        'descuento',
        'costo_envio',
        'total',
        'nombre_cliente',
        'email_cliente',
        'telefono_cliente',
        'direccion_calle',
        'direccion_numero_calle',
        'direccion_piso_info',
        'direccion_ciudad',
        'direccion_cp',
        'metodo_pago',
        'fecha'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applyCupon(?string $codigo): void
    {
        $descuento = 0;

        if ($codigo) {
            $cupon = Cupon::where('codigo', $codigo)->first();

            if ($cupon) {
                $descuento = match ($cupon->tipo) {
                    TipoCupon::PORCENTAJE => round($this->subtotal * ($cupon->descuento / 100), 2),
                    TipoCupon::FIJO       => min($cupon->descuento, $this->subtotal),
                    default               => 0,
                };
            }
        }

        $this->descuento = $descuento;
    }

    public function calculateTotal(): void
    {
        $this->total = round($this->subtotal - $this->descuento + $this->costo_envio, 2);
    }
}
