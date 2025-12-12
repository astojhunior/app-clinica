<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Costo extends Model
{
    use HasFactory;

    protected $table = 'costos';
    protected $primaryKey = 'idCosto';
    // public $incrementing = true; (Es el default, no hace falta ponerlo)
    // protected $keyType = 'int';  (Es el default)
    public $timestamps = false;

    protected $fillable = [
        'Monto',
        'FechaInicioVigencia',
        'FechaFinVigencia',
        'Tipo_Cita_idTipo_Cita',
        'created_at',
        'updated_at'
    ];

    // Relación: Pertenece a un Tipo de Cita
    public function tipoCita()
    {
        return $this->belongsTo(TipoCita::class, 'Tipo_Cita_idTipo_Cita', 'IdTipo_Cita');
    }
}
