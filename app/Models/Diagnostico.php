<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
   protected $table = 'diagnosticos';
    protected $primaryKey = 'IdDiagnostics';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'IdDiagnostics',
        'Descripcion',
        'Tipo',
        'Consulta_idConsulta',
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'Consulta_idConsulta', 'IdConsulta');
    }
}
