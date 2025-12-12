<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    public $timestamps = false;
    protected $table = 'horarios';
    protected $primaryKey = 'IdHorario';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'IdHorario',
        'DiaSemana',      // ⭐ Tu campo real es DiaSemana, no "Dias"
        'HoraInicio',
        'HoraFin',
    ];

    protected $casts = [
        'HoraInicio' => 'datetime',
        'HoraFin' => 'datetime',
    ];

    /**
     * Relación: Un horario pertenece a muchos doctores (many-to-many)
     */
    public function doctores()
    {
        return $this->belongsToMany(
            Doctor::class,
            'doctor_horario',
            'Horario_IdHorario',
            'Doctores_idDoctores',  // ⭐ Plural
            'IdHorario',
            'IdDoctores'            // ⭐ Plural
        );
    }
}
