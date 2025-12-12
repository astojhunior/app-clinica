<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCita extends Model
{
    use HasFactory;

    protected $table = 'tipo_citas';
    protected $primaryKey = 'IdTipo_Cita';
    public $incrementing = false; // Porque es varchar (TC-001)
    protected $keyType = 'string';
    public $timestamps = false; // Manejo manual de fechas

    protected $fillable = [
        'IdTipo_Cita',
        'Descripcion',
        'Estado',
        'created_at',
        'updated_at'
    ];

    // Relación: Un Tipo tiene MUCHOS costos (historial)
    public function costos()
    {
        return $this->hasMany(Costo::class, 'Tipo_Cita_idTipo_Cita', 'IdTipo_Cita');
    }

    // Relación: Un Tipo se usa en MUCHAS citas
    public function citas()
    {
        return $this->hasMany(Cita::class, 'Tipo_Cita_idTipo_Cita', 'IdTipo_Cita');
    }
}
