<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    protected $table = 'recetas';
    protected $primaryKey = 'IdReceta';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'IdReceta',
        'FechaReceta',
        'Instrucciones',
        'Tratamiento_idTratamiento',
    ];

    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class, 'Tratamiento_idTratamiento', 'IdTratamiento');
    }
}
