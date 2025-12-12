<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Sintoma;
use App\Models\Medicinas;
use App\Models\Tratamiento;
use App\Models\Diagnostico;
use App\Models\Receta;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConsultasController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Cita::with(['paciente', 'doctor.empleado', 'tipoCita'])
            ->whereDate('FechaCita', '>=', now()->toDateString())
            // incluir también completadas si quieres ver el historial
            ->whereIn('Estado', ['Programada', 'Confirmada', 'En Progreso', 'Completada']);

        // Filtros opcionales solo para admin
        if (false && $user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            if ($request->filled('fecha')) {
                $query->whereDate('FechaCita', $request->fecha);
            }

            if ($request->filled('estado')) {
                $query->where('Estado', $request->estado);
            }

            if ($request->filled('doctor')) {
                $query->where('Doctor_idDoctores', $request->doctor);
            }
        }

        $citas = $query
            ->orderBy('FechaCita', 'asc')
            ->orderBy('HoraInicio', 'asc')
            ->get();

        // aquí después puedes cargar doctores reales para los filtros
        $doctores = collect();

        return view('consultas.index', compact('citas', 'doctores', 'user'));
    }

    /** Pantalla para atender una cita */
    public function create($idCita)
    {
        $cita = Cita::with(['paciente', 'doctor.empleado', 'tipoCita'])->findOrFail($idCita);
        $sintomas  = Sintoma::where('Estado', 1)->get();
        $medicinas = Medicinas::where('Estado', 1)->get();

        return view('consultas.create', compact('cita', 'sintomas', 'medicinas'));
    }

    /** Guarda consulta + síntomas + diagnóstico + tratamiento + receta */
    public function store(Request $request, $idCita)
    {
        $cita = Cita::findOrFail($idCita);

        $request->validate([
            'FechaConsulta'           => 'required|date',
            'HoraFin'                 => 'nullable|date_format:H:i',
            'ExploracionFisica'       => 'nullable|string',
            'Sintomas'                => 'nullable|array',
            'diagnostico'             => 'required|string',
            'tipo_diagnostico'        => 'required|string',
            'medicina_id'             => 'nullable|string',
            'tratamiento_descripcion' => 'nullable|string',
            'tratamiento_indicaciones'=> 'nullable|string',
            'FechaReceta'             => 'nullable|date',
            'Instrucciones'           => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // 1) crear consulta
            $consulta = Consulta::create([
                'IdConsulta'        => (string) Str::uuid(),
                'FechaConsulta'     => $request->FechaConsulta,
                'Sintomas'          => null,
                'ExploracionFisica' => $request->ExploracionFisica,
                'Cita_idCita'       => $cita->IdCita,
            ]);

            // 2) pivot consulta_sintoma
            if ($request->filled('Sintomas')) {
                foreach ($request->Sintomas as $idSintoma) {
                    DB::table('consulta_sintoma')->insert([
                        'Consulta_idConsulta' => $consulta->IdConsulta,
                        'Sintoma_idSintomas'  => $idSintoma,
                        'created_at'          => now(),
                        'updated_at'          => now(),
                    ]);
                }
            }

            // 3) diagnóstico
            Diagnostico::create([
                'IdDiagnostics'       => (string) Str::uuid(),
                'Descripcion'         => $request->diagnostico,
                'Tipo'                => $request->tipo_diagnostico,
                'Consulta_idConsulta' => $consulta->IdConsulta,
            ]);

            // 4) tratamiento (opcional) + receta
            if ($request->filled('medicina_id') || $request->filled('tratamiento_descripcion')) {
                $tratamiento = Tratamiento::create([
                    'IdTratamiento'       => (string) Str::uuid(),
                    'Descripcion'         => $request->tratamiento_descripcion,
                    'Indicaciones'        => $request->tratamiento_indicaciones,
                    'Consulta_idConsulta' => $consulta->IdConsulta,
                    'Medicina_idMedicina' => $request->medicina_id,
                ]);

                if ($request->filled('FechaReceta') || $request->filled('Instrucciones')) {
                    Receta::create([
                        'IdReceta'               => (string) Str::uuid(),
                        'FechaReceta'            => $request->FechaReceta ?? now(),
                        'Instrucciones'          => $request->Instrucciones,
                        'Tratamiento_idTratamiento' => $tratamiento->IdTratamiento,
                    ]);
                }
            }

            // 5) actualizar cita
            $cita->update([
                'Estado'  => 'Completada',
                'HoraFin' => $request->HoraFin ?? $cita->HoraFin,
            ]);

            DB::commit();

            return redirect()->route('consultas.index')
                ->with('success', 'Consulta registrada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Error al guardar la consulta: '.$e->getMessage())
                ->withInput();
        }
    }

    /** Cambia la cita a "En Progreso" y redirige a la pantalla clínica */
    public function iniciarConsulta(Request $request, $id)
    {
        $user = Auth::user();
        $cita = Cita::with('doctor.empleado')->findOrFail($id);

        if (!in_array($cita->Estado, ['Programada', 'Confirmada'])) {
            return back()->with('error', 'Esta cita no se puede iniciar.');
        }

        $cita->update([
            'Estado'     => 'En Progreso',
            'updated_at' => now(),
        ]);

        return redirect()->route('consultas.create', $cita->IdCita);
    }

    /** Solo cambia estado/hora fin sin registrar datos clínicos (si lo quieres mantener) */
   public function finalizarConsulta(Request $request, $id)
    {
        $user = Auth::user();
        $cita = Cita::with('doctor.empleado')->findOrFail($id);

        // Validación de hora fin
        $request->validate([
            'HoraFin' => 'required|date_format:H:i|after:'.$cita->HoraInicio,
        ]);

        DB::beginTransaction();

        try {
            // Actualizar solo esta cita
            $cita->update([
                'HoraFin'    => $request->HoraFin,
                'Estado'     => 'Completada',
                'updated_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Consulta finalizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error al finalizar la consulta: '.$e->getMessage());
        }
    }


}
