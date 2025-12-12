<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    public $timestamps = false;
    protected $table = 'doctores';
    protected $primaryKey = 'IdDoctores';  // ⭐ Es PLURAL (como en tu BD)
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'IdDoctores',  // ⭐ Plural
        'Numero_Colegiatura',
        'Biografia',
        'Especialidad_idEspecialidad',
        'Empleado_idEmpleado',
        'created_at',
        'updated_at',
    ];

    /**
     * Relación: Un doctor pertenece a un empleado
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'Empleado_idEmpleado', 'IdEmpleado');
    }

    /**
     * Relación: Un doctor pertenece a una especialidad
     */
    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'Especialidad_idEspecialidad', 'IdEspecialidad');
    }

    /**
     * Relación: Un doctor tiene muchos horarios (many-to-many)
     * ⭐ CORREGIDO según tu estructura real
     */
    public function horarios()
    {
        return $this->belongsToMany(
            Horario::class,
            'doctor_horario',           // Tabla pivote
            'Doctores_idDoctores',      // ⭐ FK en tabla pivote (PLURAL)
            'Horario_IdHorario',        // FK en tabla pivote para horario
            'IdDoctores',               // ⭐ PK de doctores (PLURAL)
            'IdHorario'                 // PK de horarios
        );
    }

    /**
     * Relación: Un doctor tiene muchas citas
     */
    public function citas()
    {
        return $this->hasMany(Cita::class, 'Doctor_IdDoctor', 'IdDoctores');
    }
}
