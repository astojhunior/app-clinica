<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tratamiento extends Model
{
    protected $table = 'tratamientos';
    protected $primaryKey = 'IdTratamiento';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'IdTratamiento',
        'Descripcion',
        'Indicaciones',
        'Consulta_idConsulta',
        'Medicina_idMedicina',
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'Consulta_idConsulta', 'IdConsulta');
    }

    public function medicina()
    {
        return $this->belongsTo(Medicinas::class, 'Medicina_idMedicina', 'IdMedicina');
    }

    public function receta()
    {
        return $this->hasOne(Receta::class, 'Tratamiento_idTratamiento', 'IdTratamiento');
    }
}
