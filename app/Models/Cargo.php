<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Cargo extends Model
{
    protected $table = 'cargos';
    protected $primaryKey = 'IdCargo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'IdCargo',
        'DescripcionCargo'
    ];

    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'Cargo_idCargo', 'IdCargo');
    }
}
