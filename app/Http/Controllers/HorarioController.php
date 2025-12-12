<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Horario;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index()
    {
        // 1) Especialidades
        $especialidades = Especialidad::orderBy('DescripcionEspe')->get();

        // 2) Horarios con doctores + empleado + especialidad del doctor
        $horarios = Horario::with([
            'doctores.empleado',
            'doctores.especialidad',
        ])->get();

        // 3) Días que quieres mostrar en el horario
        $dias = ['Lunes','Martes','Miércoles','Jueves','Viernes'];

        // 4) Franjas horarias a partir de los registros
        $horas = $horarios->map(function ($h) {
            return $h->HoraInicio->format('H:i') . '-' . $h->HoraFin->format('H:i');
        })->unique()->values()->all();

        // 5) Inicializar matriz vacía
        $horario = [];
        foreach ($especialidades as $esp) {
            foreach ($dias as $dia) {
                foreach ($horas as $franja) {
                    $horario[$esp->IdEspecialidad][$dia][$franja] = collect();
                }
            }
        }

        // 6) Rellenar matriz con cada registro de horarios
        foreach ($horarios as $h) {

            // Normalizar días desde DiaSemana (ej: "lun, mar, mie")
            $diasHorario = collect(explode(',', $h->DiaSemana))
                ->map(fn ($d) => trim($d))
                ->map(function ($d) {
                    $map = [
                        'lun'        => 'Lunes',
                        'lunes'      => 'Lunes',
                        'mar'        => 'Martes',
                        'martes'     => 'Martes',
                        'mie'        => 'Miércoles',
                        'miercoles'  => 'Miércoles',
                        'miércoles'  => 'Miércoles',
                        'jue'        => 'Jueves',
                        'jueves'     => 'Jueves',
                        'vie'        => 'Viernes',
                        'viernes'    => 'Viernes',
                    ];
                    $key = strtolower($d);
                    return $map[$key] ?? null;
                })
                ->filter()
                ->unique()
                ->all();

            $franja = $h->HoraInicio->format('H:i') . '-' . $h->HoraFin->format('H:i');

            if (!in_array($franja, $horas)) {
                continue;
            }

            foreach ($diasHorario as $diaNorm) {
                if (!in_array($diaNorm, $dias)) {
                    continue;
                }

                foreach ($h->doctores as $doc) {
                    $idEsp = $doc->Especialidad_idEspecialidad;

                    if (isset($horario[$idEsp][$diaNorm][$franja])) {
                        $horario[$idEsp][$diaNorm][$franja]->push($doc);
                    }
                }
            }
        }

        // 7) Quitar duplicados por doctor en cada celda
        foreach ($horario as $idEsp => $diasArr) {
            foreach ($diasArr as $dia => $franjasArr) {
                foreach ($franjasArr as $franja => $coleccion) {
                    $horario[$idEsp][$dia][$franja] = $coleccion->unique('IdDoctores');
                }
            }
        }

        return view('horarios.index', [
            'especialidades' => $especialidades,
            'horas'          => $horas,
            'dias'           => $dias,
            'horario'        => $horario,
        ]);
    }
}
