<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'Empleado_idEmpleado', // importante si vas a asignarlo por código
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relación: este usuario pertenece a un empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'Empleado_idEmpleado', 'IdEmpleado');
    }

    // Relación: este usuario, si es doctor, tiene un registro en doctores
    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'Empleado_idEmpleado', 'Empleado_idEmpleado');
    }

    // Helper de rol admin usando Spatie
    public function isAdmin(): bool
    {
         return $this->cargo === 'admin';
    }

    // Helper opcional: es doctor
    public function isDoctor(): bool
    {
        return $this->cargo === 'doctor';
    }
}
