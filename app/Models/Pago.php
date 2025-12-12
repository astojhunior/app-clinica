<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';
    protected $primaryKey = 'IdPago';
    public $incrementing = false; // PK es String (PAG-XXXX)
    protected $keyType = 'string';

    // Desactivamos timestamps automáticos para evitar errores con SQL Server
    // (Ya que los estás insertando manualmente con DB::raw)
    public $timestamps = false;

    protected $fillable = [
        'IdPago',
        'FechaPago',
        'EstadoPago',
        'SubTotal',  // Base imponible
        'MontoIGV',  // Impuesto
        'MontoTotal',
        'TipoPago_id', // Asegúrate que este nombre coincida con tu columna en BD
        'Cita_idCita',
        'created_at',
        'updated_at'
    ];

    /**
     * Casts: Convierte los datos de la BD a tipos nativos de PHP.
     * Esto ayuda a que 'MontoTotal' sea un número (float) y no un string.
     */
    protected $casts = [
        'FechaPago' => 'datetime',
        'SubTotal' => 'decimal:2',
        'MontoIGV' => 'decimal:2',
        'MontoTotal' => 'decimal:2',
        'EstadoPago' => 'integer',
    ];

    // Relación: Pertenece a una Cita
    public function cita()
    {
        return $this->belongsTo(Cita::class, 'Cita_idCita', 'IdCita');
    }

    // Relación: Un Pago tiene MUCHOS Detalles (desglose)
    public function detalles()
    {
        return $this->hasMany(DetallePago::class, 'Pago_idPago', 'IdPago');
    }

    // app/Models/Pago.php
    public function tipoPago()
    {
        return $this->belongsTo(TipoPago::class, 'TipoPago_id', 'IDtipos');
    }


}
