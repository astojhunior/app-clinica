<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Doctor;
use App\Models\Cita;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Filtro para el gráfico de pagos: dia | mes | anio
        $rango = $request->get('rango', 'mes');

        // -------- TARJETAS PRINCIPALES --------
        $totalPacientes = Paciente::count();
        $totalDoctores  = Doctor::count();
        $citasHoy       = Cita::hoy()->count();        // scopeHoy en tu modelo Cita
        $citasFuturas   = Cita::futuras()->count();    // scopeFuturas en tu modelo Cita

        // Total pagado hoy (para la tarjeta)
       $pagosHoy = Pago::whereDate('FechaPago', now()->toDateString())
        ->sum('MontoTotal');


        // -------- DATOS PARA GRÁFICO DE PAGOS --------
        $query = Pago::select(
            DB::raw('CONVERT(date, FechaPago) as fecha'),
            DB::raw('SUM(MontoTotal) as total')
        );

        if ($rango === 'dia') {
            $query->whereDate('FechaPago', now()->toDateString());
        } elseif ($rango === 'mes') {
            $query->whereMonth('FechaPago', now()->month)
                  ->whereYear('FechaPago', now()->year);
        } else { // 'anio'
            $query->whereYear('FechaPago', now()->year);
        }

        $pagosPorFecha = $query
            ->groupBy(DB::raw('CONVERT(date, FechaPago)'))
            ->orderBy(DB::raw('CONVERT(date, FechaPago)'))
            ->get();

        $labelsPagos = $pagosPorFecha->pluck('fecha')->map(function ($f) {
            return \Carbon\Carbon::parse($f)->format('d/m');
        })->toArray();

        $dataPagos = $pagosPorFecha->pluck('total')->toArray();

        // -------- PACIENTES ACTIVOS / INACTIVOS --------
        $activos   = Paciente::where('Estado', 1)->count();
        $inactivos = Paciente::where('Estado', 0)->count();

        // -------- PRÓXIMAS CITAS --------
        $proximasCitas = Cita::futuras()
            ->with(['paciente', 'doctor.empleado'])
            ->orderBy('FechaCita')
            ->orderBy('HoraInicio')
            ->limit(10)
            ->get();

        // -------- RETORNAR VISTA --------
        return view('dashboard', [
            'totalPacientes' => $totalPacientes,
            'totalDoctores'  => $totalDoctores,
            'citasHoy'       => $citasHoy,
            'citasFuturas'   => $citasFuturas,
            'pagosHoy'       => $pagosHoy,
            'labelsPagos'    => $labelsPagos,
            'dataPagos'      => $dataPagos,
            'activos'        => $activos,
            'inactivos'      => $inactivos,
            'proximasCitas'  => $proximasCitas,
            'rango'          => $rango,
        ]);
    }
}
