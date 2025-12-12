<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicinas extends Model
{
    protected $table = 'medicinas';
    protected $primaryKey = 'IdMedicina';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'Nombre',
        'Estado',
    ];

    public function tratamientos()
    {
        return $this->hasMany(Tratamiento::class, 'Medicinas_idMedicina', 'IdMedicina');
        // ajusta el nombre de la FK: en tu diagrama pone Medicina_idMedicina
    }
}
