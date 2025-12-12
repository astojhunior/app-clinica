<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    protected $table = 'consultas';
    protected $primaryKey = 'IdConsulta';
    public $incrementing = false;
    protected $keyType = 'string';

   protected $fillable = [
        'IdConsulta',
        'FechaConsulta',
        'Sintomas',
        'ExploracionFisica',
        'Cita_idCita',
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'Cita_idCita', 'IdCita');
    }

    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class, 'Consulta_idConsulta', 'IdConsulta');
    }

    public function tratamientos()
    {
        return $this->hasMany(Tratamiento::class, 'Consulta_idConsulta', 'IdConsulta');
    }

    public function sintomas()
    {
        // tabla intermedia consulta_sintoma
        return $this->belongsToMany(Sintoma::class, 'consulta_sintoma', 'Consulta_idConsulta', 'Sintoma_idSintomas');
    }
}
