<?php

namespace App\Support;

class CargoRoleMapper
{
    public static function roleForCargo(string $descripcionCargo): ?string
    {
        $texto = strtolower($descripcionCargo);

        // C001 - Doctor
        if (str_contains($texto, 'doctor')) {
            return 'doctor';
        }

        // C002 - Recepcionista
        if (str_contains($texto, 'recepcionista')) {
            return 'recepcionista';
        }

        // C003 - Administrador TI
        if (str_contains($texto, 'administrador ti')) {
            return 'admin';
        }

        // C004 - Personal de Facturación y Cobranza
        if (str_contains($texto, 'facturacion') || str_contains($texto, 'facturación')) {
            return 'facturacion';
        }

        // C005 - Personal de Mantenimiento
        if (str_contains($texto, 'mantenimiento')) {
            return 'mantenimiento';
        }

        // Si el cargo no coincide con ninguno, no se asigna rol
        return null;
    }
}
