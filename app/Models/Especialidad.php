<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Especialidad extends Model
{
    protected $table = 'especialidades';
    protected $primaryKey = 'IdEspecialidad';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'IdEspecialidad',
        'DescripcionEspe'
    ];

    public function doctores()
    {
        return $this->hasMany(Doctor::class, 'Especialidad_idEspecialidad', 'IdEspecialidad');
    }
}
