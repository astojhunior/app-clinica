<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cita extends Model
{
    use HasFactory;

    // 1. Configuración de Tabla
    protected $table = 'citas';
    protected $primaryKey = 'IdCita';
    public $incrementing = false;
    protected $keyType = 'string';

    // ⚠️ IMPORTANTE: Desactivar timestamps automáticos para evitar
    // el error de fecha en SQL Server (usarás DB::raw en el controlador)
    public $timestamps = false;

    protected $fillable = [
        'IdCita',
        'FechaCita',           // Nombre correcto
        'HoraInicio',
        'HoraFin',
        'Estado',
        'MotivoCita',          // Corregido (estaba Observaciones en migraciones viejas, MotivoCita en las nuevas)
        'Modalidad',
        'CostoTotal',          // ✅ Nuevo campo
        'EstadoPago',          // ✅ Nuevo campo
        'Paciente_idPaciente',
        'Doctor_idDoctores',
        'Tipo_Cita_idTipo_Cita',
        'created_at',          // ✅ Necesario para inserción manual
        'updated_at'           // ✅ Necesario para inserción manual
    ];

    // Corregido: Usar los nombres reales de la BD
    protected $casts = [
        'FechaCita' => 'date',
        'HoraInicio' => 'datetime', // O 'string' si solo quieres la hora
        'HoraFin' => 'datetime',
    ];

    // Generar ID automático
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($cita) {
            if (empty($cita->IdCita)) {
                $cita->IdCita = 'CIT-' . strtoupper(Str::random(10));
            }
        });
    }

    // ---------------- RELACIONES ----------------

    public function paciente()
    {
        // Verificado: Paciente_idPaciente (según estándar)
        return $this->belongsTo(Paciente::class, 'Paciente_idPaciente', 'IdPaciente');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'Doctor_idDoctores', 'IdDoctores');
    }

   public function tipoCita()
    {
        return $this->belongsTo(TipoCita::class, 'Tipo_Cita_idTipo_Cita', 'IdTipo_Cita');
    }

    // ✅ Relación Correcta: Una Cita tiene MUCHOS Pagos
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'Cita_idCita', 'IdCita');
    }

    // ---------------- ACCESSORS ----------------

    public function getNombrePacienteAttribute()
    {
        return $this->paciente ?
               $this->paciente->Nombres . ' ' . $this->paciente->Apellidos : 'N/A';
    }

    public function getNombreDoctorAttribute()
    {
        // Accedemos a Doctor -> Empleado -> Nombres
        return $this->doctor && $this->doctor->empleado ?
               $this->doctor->empleado->Nombres . ' ' . $this->doctor->empleado->Apellidos : 'N/A';
    }

    // ---------------- SCOPES (Corregidos) ----------------

    public function scopeEstado($query, $estado)
    {
        return $query->where('Estado', $estado);
    }

    public function scopeFecha($query, $fecha)
    {
        // Corregido: 'Fecha' -> 'FechaCita'
        return $query->whereDate('FechaCita', $fecha);
    }

    public function scopeHoy($query)
    {
        // Corregido: 'Fecha' -> 'FechaCita'
        return $query->whereDate('FechaCita', now()->toDateString());
    }

    public function scopeFuturas($query)
    {
        // Corregido: 'Fecha' -> 'FechaCita'
        return $query->where('FechaCita', '>=', now()->toDateString());
    }
}
