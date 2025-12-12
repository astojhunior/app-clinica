<?php
//app/Models/TipoPago.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPago extends Model
{
    protected $table = 'tipos_pagos';
    protected $primaryKey = 'IDtipos';

    protected $fillable = [
        'TipoDescripcion',
        'TipoEstado',
    ];

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'TipoPago_id', 'IDtipos');
    }
}
