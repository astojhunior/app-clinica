<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    // Laravel, por defecto, busca una clave primaria llamada "id"
    // Pero como tu PK se llama "IdPaciente", se lo indicamos:
    protected $primaryKey = 'IdPaciente';

    // Si la PK NO es autoincremental/int, indícalo
    public $incrementing = false;
    protected $keyType = 'string';

    // Si la tabla se llama "pacientes"
    protected $table = 'pacientes';

    protected $casts = [
            'FechaNacimiento' => 'datetime',
            'Estado' => 'boolean',
    ];
    // Campos que puedes asignar vía create/update
    protected $fillable = [
        'IdPaciente',
        'Nombres',
        'Apellidos',
        'Dni',
        'CorreoElectronico',
        'FechaNacimiento',
        'Genero',
        'Direccion',
        'Telefono',
        'Estado'
    ];
    public function citas()
    {
        return $this->hasMany(Cita::class, 'Paciente_IdPaciente', 'IdPaciente');
    }

    // Si no usas timestamps, puedes desactivarlo
    // public $timestamps = false;
}
