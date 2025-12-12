<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Empleado extends Model
{
    // ¡¡¡CLAVE!!! Le decimos a Laravel que NO maneje
    // created_at/updated_at automáticamente.
    public $timestamps = false;

    protected $table = 'empleados';
    protected $primaryKey = 'IdEmpleado';
    public $incrementing = false;
    protected $keyType = 'string';

    // ¡SIN CASTS DE FECHA!
    protected $casts = [
        'Estado' => 'boolean',
    ];

    // ¡Asegúrate de que created_at/updated_at ESTÉN en el fillable!
    protected $fillable = [
        'IdEmpleado', 'Nombres', 'Apellidos', 'Dni', 'CorreElec',
        'FechaIngreso', 'Fechasalida', 'Estado', 'Genero',
        'FechaNacimiento', 'Cargo_idCargo',
        'created_at', // ⬅️ Necesario
        'updated_at', // ⬅️ Necesario
    ];


    public function user()
    {
        return $this->hasOne(User::class, 'Empleado_idEmpleado', 'IdEmpleado');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'Cargo_idCargo', 'IdCargo');
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'Empleado_idEmpleado', 'IdEmpleado');
    }
}
