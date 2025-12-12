<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePago extends Model
{
    use HasFactory;

    protected $table = 'detalle_pagos';
    protected $primaryKey = 'IdDetallePago'; // Ojo con la mayúscula/minúscula de tu migración
    public $timestamps = false;

    protected $fillable = [
        'Descripcion',
        'Monto',
        'Cantidad',
        'Pago_idPago',
    ];

    // Relación: Pertenece a un Pago
    public function pago()
    {
        return $this->belongsTo(Pago::class, 'Pago_idPago', 'IdPago');
    }
}
