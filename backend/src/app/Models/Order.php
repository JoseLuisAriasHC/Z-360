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
        'subtotal_sin_iva',
        'iva',
        'descuento',
        'total',

        // Datos de ENVÍO
        'envio_nombre',
        'envio_email',
        'envio_telefono',
        'envio_direccion_calle',
        'envio_direccion_numero_calle',
        'envio_direccion_piso_info',
        'envio_direccion_ciudad',
        'envio_direccion_cp',

        // Datos de FACTURACIÓN
        'facturacion_nombre',
        'facturacion_email',
        'facturacion_telefono',
        'facturacion_direccion_calle',
        'facturacion_direccion_numero_calle',
        'facturacion_direccion_piso_info',
        'facturacion_direccion_ciudad',
        'facturacion_direccion_cp',

        // Stripe
        'pago_id',
        'pago_estado',
        'pago_fecha',
        'fecha_reembolso',

        'usar_misma_direccion_facturacion',
        'metodo_pago',
        'fecha_pedido',
    ];

    protected $casts = [
        'fecha_pedido' => 'datetime',
        'pago_fecha' => 'datetime',
        'fecha_reembolso' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
        'costo_envio' => 'decimal:2',
        'usar_misma_direccion_facturacion' => 'boolean',
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
        $this->descuento = 0;

        if (!$codigo) {
            return;
        }

        $cupon = Cupon::where('codigo', $codigo)->first();

        if (!$cupon) {
            return;
        }

        if ($cupon->fecha_expiracion && $cupon->fecha_expiracion->isPast()) {
            return;
        }

        if (!is_null($cupon->uso_maximo) && $cupon->uso_maximo <= 0) {
            return;
        }

        // Calcular descuento
        $descuento = match ($cupon->tipo) {
            TipoCupon::PORCENTAJE => round($this->subtotal * ($cupon->descuento / 100), 2),
            TipoCupon::FIJO       => min($cupon->descuento, $this->subtotal),
            default               => 0,
        };

        $this->descuento = $descuento;

        if (!is_null($cupon->uso_maximo) && $cupon->uso_maximo > 0) {
            $cupon->decrement('uso_maximo');
        }
    }


    private function calcularCosteEnvio(): void
    {
        $subtotal = $this->subtotal;

        $coste_envio = (float) WebSettings::getValue('coste_envio', 5);
        $free_from  = (float) WebSettings::getValue('free_coste_envio_from', 100);

        if ($subtotal >= $free_from) {
            $coste_envio = 0;
        }

        $this->costo_envio = round($coste_envio, 2);
    }

    public function calculateTotal(): void
    {
        $this->calcularCosteEnvio();
        $this->total = round($this->subtotal - $this->descuento + $this->costo_envio, 2);
    }

    public function setSubtotalAttribute($value)
    {
        $ivaPorcentaje = (float) WebSettings::getValue('iva', 21);
        $iva = $ivaPorcentaje / 100;

        $this->attributes['subtotal'] = $value;
        $this->attributes['iva'] = round($value * $iva, 2);
        $this->attributes['subtotal_sin_iva'] = round($value - ($value * $iva), 2);
    }
}
