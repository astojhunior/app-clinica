<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sintoma extends Model
{
    protected $table = 'sintomas';
    protected $primaryKey = 'IdSintomas';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'Descripcion',
        'Estado',
    ];

    public function consultas()
    {
        return $this->belongsToMany(Consulta::class, 'consulta_sintoma', 'Sintoma_idSintomas', 'Consulta_idConsulta');
    }
}
