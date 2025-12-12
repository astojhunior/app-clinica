<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Doctor;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use App\Models\TipoCita;
use App\Models\Costo; // Import Costo model for price calculation
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str; // Import Str for ID generation


class CitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cita::with(['paciente', 'doctor.empleado', 'tipoCita']); // Updated relation path

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('paciente', function($subQ) use ($search) {
                    $subQ->where('Dni', 'like', "%$search%")
                        ->orWhere('Nombres', 'like', "%$search%")
                        ->orWhere('Apellidos', 'like', "%$search%");
                })
                ->orWhereHas('doctor.empleado', function($subQ) use ($search) { // Updated path
                    $subQ->where('Nombres', 'like', "%$search%")
                        ->orWhere('Apellidos', 'like', "%$search%");
                })
                ->orWhere('MotivoCita', 'like', "%$search%"); // Changed from Observaciones to MotivoCita if schema changed
            });
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('Estado', $request->input('estado'));
        }

        // Filtro por fecha
        if ($request->filled('fecha')) {
            $query->whereDate('FechaCita', $request->input('fecha'));
        }

        // Filtro por empleado/doctor
        if ($request->filled('empleado')) {
            $query->where('Doctor_idDoctores', $request->input('empleado'));
        }

        $perPage = $request->input('per_page', 10);
        $citas = $query->orderBy('FechaCita', 'desc')
                    ->orderBy('HoraInicio', 'desc') // Changed HoraCita to HoraInicio
                    ->paginate($perPage);

        // Obtener doctores para el filtro
        $doctores = Doctor::with(['empleado', 'especialidad'])
                        ->whereHas('empleado', function($q) {
                            $q->where('Estado', 1);
                        })
                        ->get();

        // Obtener todos los pacientes activos para el formulario (optional here if index doesn't need it)
        $pacientes = Paciente::where('Estado', 1)
                            ->orderBy('Nombres')
                            ->get();

        // Obtener tipos de cita disponibles
        $tiposCita = TipoCita::where('Estado', 1)->get();

        return view('citas.index', compact('citas', 'doctores', 'pacientes', 'tiposCita'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pacientes = Paciente::where('Estado', 1)
                             ->orderBy('Nombres')
                             ->get();

        $doctores = Doctor::with(['empleado', 'especialidad', 'horarios'])
                          ->whereHas('empleado', function($q) {
                              $q->where('Estado', 1);
                          })
                          ->get();

        $tiposCita = TipoCita::where('Estado', 1)->get();

        return view('citas.create', compact('pacientes', 'doctores', 'tiposCita'));
    }


    private function verificarConflictoHorario($doctorId, $fecha, $horaInicio, $horaFin, $citaIdExcluir = null)
    {

        // Normalizar fecha a Y-m-d
        if ($fecha instanceof \DateTimeInterface) {
            $fechaStr = Carbon::instance($fecha)->format('Y-m-d');
        } else {
            $fechaStr = Carbon::parse($fecha)->format('Y-m-d');
        }

        // Normalizar horas a H:i (evita dobles fechas / segundos)
        $normalizarHora = function ($valor) {
            if ($valor instanceof \DateTimeInterface) {
                return Carbon::instance($valor)->format('H:i');
            }
            $str = (string) $valor;
            return substr($str, 0, 5); // "09:00" de "09:00:00"
        };

        $horaInicioStr = $normalizarHora($horaInicio);
        $horaFinStr    = $normalizarHora($horaFin);

        $inicio = Carbon::parse("$fechaStr $horaInicioStr");
        $fin    = Carbon::parse("$fechaStr $horaFinStr");

        $query = Cita::where('Doctor_idDoctores', $doctorId)
            ->whereDate('FechaCita', $fechaStr)
            ->where('Estado', '!=', 'Cancelada');

        if ($citaIdExcluir) {
            $query->where('IdCita', '!=', $citaIdExcluir);
        }

        $citas = $query->get();

        foreach ($citas as $cita) {
            // normalizar también lo que viene de BD
            $citaFechaStr   = Carbon::parse($cita->FechaCita)->format('Y-m-d');
            $citaHoraIniStr = $normalizarHora($cita->HoraInicio);
            $citaHoraFinStr = $normalizarHora($cita->HoraFin ?? $cita->HoraInicio);

            $citaInicio = Carbon::parse("$citaFechaStr $citaHoraIniStr");
            $citaFin    = Carbon::parse("$citaFechaStr $citaHoraFinStr");

            // hay solape si inicio < fin existente Y fin > inicio existente
            if ($inicio->lt($citaFin) && $fin->gt($citaInicio)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        $request->validate([
            'Paciente_idPaciente'   => 'required|exists:pacientes,IdPaciente',
            'Doctor_idDoctores'     => 'required|exists:doctores,IdDoctores',
            'Tipo_Cita_idTipo_Cita' => 'required|exists:tipo_citas,IdTipo_Cita',
            'FechaCita'             => 'required|date|after_or_equal:today',
            'HoraInicio'            => 'required',
            'Modalidad'             => 'required|in:Presencial,Virtual',
            'MotivoCita'            => 'nullable|string|max:200',
        ], [
            'Paciente_idPaciente.required'   => 'Debe seleccionar un paciente.',
            'Doctor_idDoctores.required'     => 'Debe seleccionar un doctor.',
            'Tipo_Cita_idTipo_Cita.required' => 'Debe seleccionar un tipo de cita.',
            'FechaCita.required'             => 'La fecha es obligatoria.',
            'HoraInicio.required'            => 'La hora de inicio es obligatoria.',
        ]);

        //Obtener paciente y doctor
        $paciente = Paciente::findOrFail($request->Paciente_idPaciente);
        $doctor   = Doctor::with('especialidad')->findOrFail($request->Doctor_idDoctores);

        $especialidad = $doctor->especialidad ? $doctor->especialidad->DescripcionEspe : null;
        $sexoPaciente = $paciente->Genero ?? null;
        $fechaNac     = $paciente->FechaNacimiento ?? null;

        $edad = null;
        if ($fechaNac) {
            $edad = \Carbon\Carbon::parse($fechaNac)->age;
        }

        $esp = $especialidad ? mb_strtolower($especialidad) : '';

        // Normalizar género a M / F
        if ($sexoPaciente) {
            $valor = mb_strtolower(trim($sexoPaciente)); // "masculino" / "femenino"
            if (str_starts_with($valor, 'masc')) {
                $sexoPaciente = 'M';
            } elseif (str_starts_with($valor, 'fem')) {
                $sexoPaciente = 'F';
            }
        }

        // ==========================================
        // 2) Restricciones por especialidad/paciente
        // ==========================================

        // Regla global: menores de edad solo en Pediatría
        if (!is_null($edad) && $edad < 18 && !str_contains($esp, 'pediatr')) {
            return back()
                ->withErrors(['Paciente_idPaciente' => 'Los pacientes menores de 18 años solo pueden ser atendidos en Pediatría.'])
                ->withInput();
        }

        // Pediatría: (opcional) reforzar que solo menores de 18
        if (str_contains($esp, 'pediatr')) {
            if (is_null($edad)) {
                return back()
                    ->withErrors(['Paciente_idPaciente' => 'El paciente no tiene fecha de nacimiento registrada, no se puede agendar en Pediatría.'])
                    ->withInput();
            }
            if ($edad >= 18) {
                return back()
                    ->withErrors(['Paciente_idPaciente' => 'Pediatría está reservada para pacientes menores de 18 años.'])
                    ->withInput();
            }
        }

        // Ginecología / Obstetricia: solo F
        if (str_contains($esp, 'ginecolog') || str_contains($esp, 'obstetr')) {
            if (!$sexoPaciente) {
                return back()
                    ->withErrors(['Paciente_idPaciente' => 'El paciente no tiene sexo registrado, no se puede agendar en Ginecología/Obstetricia.'])
                    ->withInput();
            }
            if ($sexoPaciente !== 'F') {
                return back()
                    ->withErrors(['Paciente_idPaciente' => 'Ginecología y Obstetricia están reservadas para pacientes de sexo femenino.'])
                    ->withInput();
            }
        }

        // Andrología: solo M
        if (str_contains($esp, 'androlog')) {
            if (!$sexoPaciente) {
                return back()
                    ->withErrors(['Paciente_idPaciente' => 'El paciente no tiene sexo registrado, no se puede agendar en Andrología.'])
                    ->withInput();
            }
            if ($sexoPaciente !== 'M') {
                return back()
                    ->withErrors(['Paciente_idPaciente' => 'Andrología está reservada para pacientes de sexo masculino.'])
                    ->withInput();
            }
        }

        // costo vigente...
        $costoVigente = Costo::where('Tipo_Cita_idTipo_Cita', $request->Tipo_Cita_idTipo_Cita)
            ->where('FechaInicioVigencia', '<=', Carbon::now()->format('Y-m-d'))
            ->where(function ($q) {
                $q->whereNull('FechaFinVigencia')
                ->orWhere('FechaFinVigencia', '>=', Carbon::now()->format('Y-m-d'));
            })
            ->orderBy('FechaInicioVigencia', 'desc')
            ->first();

        $montoCongelado = $costoVigente ? $costoVigente->Monto : 0.00;

        // asumimos siempre 30 minutos
        $horaFinTemporal = Carbon::parse($request->HoraInicio)->addMinutes(30)->format('H:i');

        // 1) validar que la cita cae dentro de un horario del doctor
        if (!$this->citaDentroDeHorarioDoctor(
            $request->Doctor_idDoctores,
            $request->FechaCita,
            $request->HoraInicio,
            $horaFinTemporal
        )) {
            return back()->withErrors([
                'HoraInicio' => 'La hora seleccionada está fuera del horario de atención del doctor para ese día.'
            ])->withInput();
        }

        // 2) validar que no se solapa con otra cita
        $conflicto = $this->verificarConflictoHorario(
            $request->Doctor_idDoctores,
            $request->FechaCita,
            $request->HoraInicio,
            $horaFinTemporal
        );

        if ($conflicto) {
            return back()->withErrors([
                'HoraInicio' => 'El doctor ya tiene una cita en ese horario.'
            ])->withInput();
        }

        // 3) crear cita
        $idCita = 'CIT-' . strtoupper(Str::random(10));

        DB::beginTransaction();
        try {
            Cita::create([
                'IdCita'              => $idCita,
                'FechaCita'           => $request->FechaCita,
                'HoraInicio'          => $request->HoraInicio,
                'HoraFin'             => null,
                'Estado'              => 'Programada',
                'MotivoCita'          => $request->MotivoCita,
                'Modalidad'           => $request->Modalidad,
                'CostoTotal'          => $montoCongelado,
                'EstadoPago'          => 'Pendiente',
                'Paciente_idPaciente' => $request->Paciente_idPaciente,
                'Doctor_idDoctores'   => $request->Doctor_idDoctores,
                'Tipo_Cita_idTipo_Cita' => $request->Tipo_Cita_idTipo_Cita,
                'created_at'          => DB::raw('CONVERT(date, GETDATE())'),
                'updated_at'          => DB::raw('CONVERT(date, GETDATE())'),
            ]);

            DB::commit();
            return redirect()->route('citas.index')
                ->with('success', 'Cita registrada exitosamente. Costo: ' . $montoCongelado);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar la cita: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cita = Cita::with(['paciente', 'doctor.empleado', 'doctor.especialidad', 'tipoCita', 'pagos'])
            ->findOrFail($id);
        return view('citas.show', compact('cita'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cita = Cita::findOrFail($id);

        $pacientes = Paciente::where('Estado', 1)
                             ->orderBy('Nombres')
                             ->get();

        $doctores = Doctor::with(['empleado', 'especialidad'])
                          ->whereHas('empleado', function($q) {
                              $q->where('Estado', 1);
                          })
                          ->get();

        $tiposCita = TipoCita::where('Estado', 1)->get();

        return view('citas.edit', compact('cita', 'pacientes', 'doctores', 'tiposCita'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cita = Cita::findOrFail($id);

        $request->validate([
            'Paciente_idPaciente'   => 'required|exists:pacientes,IdPaciente',
            'Doctor_idDoctores'     => 'required|exists:doctores,IdDoctores',
            'Tipo_Cita_idTipo_Cita' => 'required|exists:tipo_citas,IdTipo_Cita',
            'HoraInicio'            => 'required',
            'HoraFin'               => 'nullable|date_format:H:i|after:HoraInicio',
            'Modalidad'             => 'required|in:Presencial,Virtual',
            'MotivoCita'            => 'nullable|string|max:200',
            'Estado'                => 'required',
        ]);

        // ========== REGLAS POR ESPECIALIDAD / PACIENTE ==========
        $paciente = Paciente::findOrFail($request->Paciente_idPaciente);
        $doctor   = Doctor::with('especialidad')->findOrFail($request->Doctor_idDoctores);

        $especialidad = $doctor->especialidad ? $doctor->especialidad->DescripcionEspe : null;
        $sexoPaciente = $paciente->Genero ?? null;
        $fechaNac     = $paciente->FechaNacimiento ?? null;

        $edad = null;
        if ($fechaNac) {
            $edad = \Carbon\Carbon::parse($fechaNac)->age;
        }

        $esp = $especialidad ? mb_strtolower($especialidad) : '';

        // Normalizar género a M / F
        if ($sexoPaciente) {
            $valor = mb_strtolower(trim($sexoPaciente)); // "masculino" / "femenino"
            if (str_starts_with($valor, 'masc')) {
                $sexoPaciente = 'M';
            } elseif (str_starts_with($valor, 'fem')) {
                $sexoPaciente = 'F';
            }
        }

        // 2) Restricciones por especialidad/paciente

        // Regla global: menores de edad solo en Pediatría
        if (!is_null($edad) && $edad < 18 && !str_contains($esp, 'pediatr')) {
            return back()
                ->withErrors(['Paciente_idPaciente' => 'Los pacientes menores de 18 años solo pueden ser atendidos en Pediatría.'])
                ->withInput();
        }

        // Pediatría: (opcional) reforzar que solo menores de 18
        if (str_contains($esp, 'pediatr')) {
            if (is_null($edad)) {
                return back()
                    ->withErrors(['Paciente_idPaciente' => 'El paciente no tiene fecha de nacimiento registrada, no se puede agendar en Pediatría.'])
                    ->withInput();
            }
            if ($edad >= 18) {
                return back()
                    ->withErrors(['Paciente_idPaciente' => 'Pediatría está reservada para pacientes menores de 18 años.'])
                    ->withInput();
            }
        }

        // Ginecología / Obstetricia: solo F
        if (str_contains($esp, 'ginecolog') || str_contains($esp, 'obstetr')) {
            if (!$sexoPaciente) {
                return back()
                    ->withErrors(['Paciente_idPaciente' => 'El paciente no tiene sexo registrado, no se puede agendar en Ginecología/Obstetricia.'])
                    ->withInput();
            }
            if ($sexoPaciente !== 'F') {
                return back()
                    ->withErrors(['Paciente_idPaciente' => 'Ginecología y Obstetricia están reservadas para pacientes de sexo femenino.'])
                    ->withInput();
            }
        }

        // Andrología: solo M
        if (str_contains($esp, 'androlog')) {
            if (!$sexoPaciente) {
                return back()
                    ->withErrors(['Paciente_idPaciente' => 'El paciente no tiene sexo registrado, no se puede agendar en Andrología.'])
                    ->withInput();
            }
            if ($sexoPaciente !== 'M') {
                return back()
                    ->withErrors(['Paciente_idPaciente' => 'Andrología está reservada para pacientes de sexo masculino.'])
                    ->withInput();
            }
        }

        // si no envías HoraFin, asumimos 30 minutos
        $horaFinParaValidar = $request->HoraFin
            ? $request->HoraFin
            : Carbon::parse($request->HoraInicio)->addMinutes(30)->format('H:i');

        // 1) validar rango contra horario del doctor
        if (!$this->citaDentroDeHorarioDoctor(
            $request->Doctor_idDoctores,
            $request->FechaCita,
            $request->HoraInicio,
            $horaFinParaValidar
        )) {
            return back()->withErrors([
                'HoraInicio' => 'La hora seleccionada está fuera del horario de atención del doctor para ese día.'
            ])->withInput();
        }

        // 2) validar conflicto (excluyendo esta misma cita)
        $conflicto = $this->verificarConflictoHorario(
            $request->Doctor_idDoctores,
            $request->FechaCita,
            $request->HoraInicio,
            $horaFinParaValidar,
            $cita->IdCita
        );

        if ($conflicto) {
            return back()->withErrors([
                'HoraInicio' => 'El doctor ya tiene una cita en ese horario.'
            ])->withInput();
        }

        // 3) actualizar
        $cita->update([
            'FechaCita'             => $request->FechaCita,
            'HoraInicio'            => $request->HoraInicio,
            'HoraFin'               => $request->HoraFin,
            'Estado'                => $request->Estado,
            'MotivoCita'            => $request->MotivoCita,
            'Modalidad'             => $request->Modalidad,
            'Paciente_idPaciente'   => $request->Paciente_idPaciente,
            'Doctor_idDoctores'     => $request->Doctor_idDoctores,
            'Tipo_Cita_idTipo_Cita' => $request->Tipo_Cita_idTipo_Cita,
            'updated_at'            => DB::raw('CONVERT(date, GETDATE())'),
        ]);

        return redirect()->route('citas.index')
            ->with('success', 'Cita actualizada exitosamente');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cita = Cita::findOrFail($id);
        $cita->delete();

        return redirect()->route('citas.index')
                         ->with('success', 'Cita eliminada exitosamente');
    }

    /**
     * Cancelar una cita
     */
    public function cancelar(string $id)
    {
        $cita = Cita::findOrFail($id);
        $cita->update(['Estado' => 'Cancelada', 'updated_at' => DB::raw('CONVERT(date, GETDATE())')]);

        return redirect()->route('citas.index')
                         ->with('success', 'Cita cancelada exitosamente');
    }

    /**
     * Confirmar una cita
     */
    public function confirmar(string $id)
    {
        $cita = Cita::findOrFail($id);
        $cita->update(['Estado' => 'Confirmada', 'updated_at' => DB::raw('CONVERT(date, GETDATE())')]); // Or whatever state value you use
        return redirect()->route('citas.index')
                         ->with('success', 'Cita confirmada exitosamente');
    }

    /**
     * Obtener horarios disponibles de un doctor en una fecha
     */
    public function horariosDisponibles(Request $request)
    {
        // ... (Logic to fetch available slots based on Doctor's schedule and existing appointments)
        // Use Doctor model relation to horarios via pivot table
        // Ensure you use the correct column names from your new schema
         $request->validate([
            'doctor_id' => 'required|exists:doctores,IdDoctores',
            'fecha' => 'required|date',
        ]);

        $doctor = Doctor::with('horarios')->findOrFail($request->doctor_id);
        $fecha = Carbon::parse($request->fecha);
        // Map Carbon dayOfWeek (0=Sunday) to your DB day names
        $diasMap = [0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'];
        $diaSemana = $diasMap[$fecha->dayOfWeek];

        // Filter doctor's schedules for this day
        $horariosDia = $doctor->horarios->filter(function($horario) use ($diaSemana) {
             return stripos($horario->DiaSemana, $diaSemana) !== false;
        });

        if ($horariosDia->isEmpty()) {
             return response()->json(['success' => false, 'message' => 'El doctor no atiende este día.']);
        }

        // Fetch existing appointments to exclude occupied slots
        $citasExistentes = Cita::where('Doctor_idDoctores', $doctor->IdDoctores)
                               ->whereDate('FechaCita', $fecha)
                               ->where('Estado', '!=', 'Cancelada')
                               ->get();

        $slots = [];
        foreach ($horariosDia as $horario) {
            $inicio = Carbon::parse($horario->HoraInicio);
            $fin = Carbon::parse($horario->HoraFin);

            while ($inicio->lt($fin)) {
                $slotFin = $inicio->copy()->addMinutes(30); // 30 min slots
                if ($slotFin->gt($fin)) break;

                $ocupado = false;
                foreach ($citasExistentes as $cita) {
                    $citaInicio = Carbon::parse($cita->HoraInicio);
                    $citaFin = Carbon::parse($cita->HoraFin);

                    // Check overlap
                    if ($inicio->lt($citaFin) && $slotFin->gt($citaInicio)) {
                        $ocupado = true;
                        break;
                    }
                }

                if (!$ocupado) {
                    $slots[] = [
                        'hora_inicio' => $inicio->format('H:i'),
                        'hora_fin' => $slotFin->format('H:i'),
                        'label' => $inicio->format('H:i') . ' - ' . $slotFin->format('H:i')
                    ];
                }
                $inicio->addMinutes(30);
            }
        }

        return response()->json(['success' => true, 'horarios' => $slots]);
    }

    /**
     * Filtrar doctores por especialidad y día (UPDATED METHOD)
     */
    public function filtrarDoctores(Request $request)
    {
        $query = Doctor::with(['empleado', 'especialidad', 'horarios'])
                       ->whereHas('empleado', function($q) {
                           $q->where('Estado', 1);
                       });

        // Filtrar por especialidad
        if ($request->filled('especialidad')) {
            $query->where('Especialidad_idEspecialidad', $request->especialidad);
        }

        // Filtrar por día de atención
        if ($request->filled('dia')) {
            $dia = $request->dia;
            $query->whereHas('horarios', function($q) use ($dia) {
                // SQL Server usa CHARINDEX
                $q->whereRaw("CHARINDEX(?, DiaSemana) > 0", [$dia]);
            });
        }

        $doctores = $query->get();

        // Formatear la respuesta con HTML de las tarjetas
        $html = '';

        foreach ($doctores as $doctor) {
            // 1. Obtener Iniciales
            $iniciales = 'DR';
            if ($doctor->empleado) {
                $nombres = explode(' ', $doctor->empleado->Nombres);
                $apellidos = explode(' ', $doctor->empleado->Apellidos);
                $iniciales = substr($nombres[0] ?? '', 0, 1) . substr($apellidos[0] ?? '', 0, 1);
            }

            // 2. Obtener Días Únicos
            $diasUnicos = $doctor->horarios
                ->pluck('DiaSemana')
                ->map(function($dias) {
                    return array_map('trim', explode(',', $dias));
                })
                ->flatten()
                ->unique()
                ->filter()
                ->values()
                ->all();

            // 3. Obtener Rangos de Horarios
            $horariosTexto = $doctor->horarios->map(function($horario) {
                return date('h:i A', strtotime($horario->HoraInicio)) . ' - ' . date('h:i A', strtotime($horario->HoraFin));
            })->implode(', ');

            // 4. Preparar datos para el onclick (Escapados)
            $idDoctor = $doctor->IdDoctores;
            $nombreDoctor = e('Dr. ' . ($doctor->empleado->Nombres ?? '') . ' ' . ($doctor->empleado->Apellidos ?? ''));
            $nombreEspecialidad = e($doctor->especialidad->DescripcionEspe ?? 'Sin especialidad'); // Updated to DescripcionEspe

            // 5. Construir HTML
            $html .= '
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-all cursor-pointer doctor-card hover:bg-blue-50"
                 data-doctor-id="' . $idDoctor . '"
                 onclick=\'seleccionarDoctor("' . $idDoctor . '", "' . $nombreDoctor . '", "' . $nombreEspecialidad . '")\'>

                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-indigo-600 font-semibold text-lg">' . $iniciales . '</span>
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-900 truncate">
                                ' . $nombreDoctor . '
                            </h4>
                            <svg class="w-5 h-5 text-green-500 hidden check-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>

                        <span class="inline-block px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full mt-1">
                            ' . $nombreEspecialidad . '
                        </span>

                        <p class="text-xs text-gray-500 mt-1">CMP: ' . e($doctor->Numero_Colegiatura ?? 'N/A') . '</p>
                        ';

            if (!empty($diasUnicos)) {
                $html .= '<div class="mt-2 flex flex-wrap gap-1">';
                foreach ($diasUnicos as $dia) {
                    $html .= '<span class="inline-block px-2 py-0.5 text-[10px] font-medium text-white bg-blue-500 rounded">' . e($dia) . '</span>';
                }
                $html .= '</div>';
            }

            $html .= '
                        <p class="text-xs text-gray-500 mt-2 truncate" title="' . e($horariosTexto) . '">
                            <span class="font-medium">Horario:</span> ' . e($horariosTexto) . '
                        </p>
                    </div>
                </div>
            </div>';
        }

        if ($doctores->isEmpty()) {
            $html = '<div class="col-span-full text-center py-8 text-gray-500">
                        <p>No se encontraron doctores con los criterios seleccionados.</p>
                     </div>';
        }

        return response()->json([
            'success' => true,
            'count' => $doctores->count(),
            'html' => $html
        ]);
    }



    private function citaDentroDeHorarioDoctor($doctorId, $fecha, $horaInicio, $horaFin)
    {
        $doctor = Doctor::with('horarios')->find($doctorId);
        if (!$doctor) return false;

        // Map usando códigos cortos (dom, lun, mar, mie, jue, vie, sab)
        $fechaCarbon = Carbon::parse($fecha);
        $map = [
            0 => 'dom',
            1 => 'lun',
            2 => 'mar',
            3 => 'mie',
            4 => 'jue',
            5 => 'vie',
            6 => 'sab',
        ];
        $diaCodigo = $map[$fechaCarbon->dayOfWeek];

        $horariosDia = $doctor->horarios->filter(function ($h) use ($diaCodigo) {
            return stripos($h->DiaSemana, $diaCodigo) !== false;
        });

        if ($horariosDia->isEmpty()) {
            return false;
        }

        // Función helper para limpiar a H:i
        $normalizar = function ($valor) {
            if ($valor instanceof \DateTimeInterface) {
                return Carbon::instance($valor)->format('H:i');
            }
            // si viene como "10:00:00" o "10:00:00.000000"
            $str = (string)$valor;
            // nos quedamos con los primeros 5 que parezcan HH:MM
            return substr($str, 0, 5);
        };

        $inicioCita = Carbon::createFromFormat('H:i', $normalizar($horaInicio));
        $finCita    = Carbon::createFromFormat('H:i', $normalizar($horaFin));

        foreach ($horariosDia as $h) {
            $inicioHorario = Carbon::createFromFormat('H:i', $normalizar($h->HoraInicio));
            $finHorario    = Carbon::createFromFormat('H:i', $normalizar($h->HoraFin));

            if ($inicioCita->gte($inicioHorario) && $finCita->lte($finHorario)) {
                return true;
            }
        }

        return false;
    }


/**
 * Enviar email de confirmación de cita
 */
    public function enviarEmail(string $id)
    {
        try {
            // Obtener la cita con todas las relaciones
            $cita = Cita::with([
                'paciente',
                'doctor.empleado',
                'doctor.especialidad',
                'tipoCita'
            ])->findOrFail($id);

            // Validar que el paciente tenga email
            if (!$cita->paciente->Email) {
                return back()->with('error', 'El paciente no tiene email registrado.');
            }

            // Calcular edad del paciente
            $edadPaciente = $cita->paciente->FechaNacimiento
                ? \Carbon\Carbon::parse($cita->paciente->FechaNacimiento)->age
                : 'N/A';

            // Preparar datos para el email
            $datos = [
                'cita' => $cita,
                'paciente' => $cita->paciente,
                'doctor' => $cita->doctor,
                'edadPaciente' => $edadPaciente,
            ];

            // Enviar email usando MailerSend
            Mail::send([], [], function (Message $message) use ($cita, $datos, $edadPaciente) {
                $message->to($cita->paciente->Email, $cita->paciente->Nombres . ' ' . $cita->paciente->Apellidos)
                    ->subject('Confirmación de Cita Médica - Clínica Calidad')
                    ->html($this->construirEmailHTML($cita, $edadPaciente));
            });

            return back()->with('success', 'Email enviado exitosamente a ' . $cita->paciente->Email);

        } catch (\Exception $e) {
            Log::error('Error al enviar email: ' . $e->getMessage());
            return back()->with('error', 'Error al enviar el email: ' . $e->getMessage());
        }
    }

/**
 * Construir HTML del email
 */
    private function construirEmailHTML($cita, $edadPaciente)
    {
        $fechaCita = \Carbon\Carbon::parse($cita->FechaCita)->format('d/m/Y');
        $horaInicio = \Carbon\Carbon::parse($cita->HoraInicio)->format('h:i A');
        $horaFin = \Carbon\Carbon::parse($cita->HoraFin)->format('h:i A');

        $nombreDoctor = 'Dr. ' . $cita->doctor->empleado->Nombres . ' ' . $cita->doctor->empleado->Apellidos;
        $especialidad = $cita->doctor->especialidad->DescripcionEspe ?? 'Sin especialidad';

        $tipoCita = $cita->tipoCita->Descripcion ?? 'Consulta General';
        $modalidad = $cita->Modalidad ?? 'Presencial';
        $costoTotal = number_format($cita->CostoTotal, 2);
        $estadoPago = $cita->EstadoPago ?? 'Pendiente';
        $motivoCita = $cita->MotivoCita ?? 'No especificado';

        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Confirmación de Cita</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 30px;
                    text-align: center;
                    border-radius: 10px 10px 0 0;
                }
                .content {
                    background: #f9f9f9;
                    padding: 30px;
                    border: 1px solid #ddd;
                }
                .section {
                    background: white;
                    margin: 20px 0;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
                .section h3 {
                    color: #667eea;
                    margin-top: 0;
                    border-bottom: 2px solid #667eea;
                    padding-bottom: 10px;
                }
                .info-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 8px 0;
                    border-bottom: 1px solid #eee;
                }
                .info-label {
                    font-weight: bold;
                    color: #555;
                }
                .info-value {
                    color: #333;
                }
                .highlight {
                    background: #fff3cd;
                    padding: 15px;
                    border-left: 4px solid #ffc107;
                    margin: 15px 0;
                }
                .footer {
                    text-align: center;
                    padding: 20px;
                    color: #666;
                    font-size: 12px;
                }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>🏥 Clínica Calidad</h1>
                <p>Confirmación de Cita Médica</p>
            </div>

            <div class='content'>
                <!-- Información del Paciente -->
                <div class='section'>
                    <h3>👤 Información del Paciente</h3>
                    <div class='info-row'>
                        <span class='info-label'>Nombre:</span>
                        <span class='info-value'>{$cita->paciente->Nombres} {$cita->paciente->Apellidos}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>DNI:</span>
                        <span class='info-value'>{$cita->paciente->Dni}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Edad:</span>
                        <span class='info-value'>{$edadPaciente} años</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Email:</span>
                        <span class='info-value'>{$cita->paciente->Email}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Teléfono:</span>
                        <span class='info-value'>{$cita->paciente->Telefono}</span>
                    </div>
                </div>

                <!-- Información del Doctor -->
                <div class='section'>
                    <h3>👨‍⚕️ Información del Doctor</h3>
                    <div class='info-row'>
                        <span class='info-label'>Doctor:</span>
                        <span class='info-value'>{$nombreDoctor}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Especialidad:</span>
                        <span class='info-value'>{$especialidad}</span>
                    </div>
                </div>

                <!-- Detalles de la Cita -->
                <div class='section'>
                    <h3>📅 Detalles de la Cita</h3>
                    <div class='info-row'>
                        <span class='info-label'>Fecha:</span>
                        <span class='info-value'>{$fechaCita}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Hora:</span>
                        <span class='info-value'>{$horaInicio} - {$horaFin}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Tipo de Cita:</span>
                        <span class='info-value'>{$tipoCita}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Modalidad:</span>
                        <span class='info-value'>{$modalidad}</span>
                    </div>
                </div>

                <!-- Información de Pago -->
                <div class='section'>
                    <h3>💰 Información de Pago</h3>
                    <div class='info-row'>
                        <span class='info-label'>Costo Total:</span>
                        <span class='info-value'>S/ {$costoTotal}</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Estado de Pago:</span>
                        <span class='info-value'>{$estadoPago}</span>
                    </div>
                </div>

                <!-- Motivo de la Cita -->
                <div class='highlight'>
                    <strong>📋 Motivo de la Cita:</strong><br>
                    {$motivoCita}
                </div>

                <p style='text-align: center; margin-top: 30px; color: #666;'>
                    <strong>Nota:</strong> Por favor llegue 10 minutos antes de su cita.
                </p>
            </div>

            <div class='footer'>
                <p>Este es un correo automático, por favor no responder.</p>
                <p>© 2025 Clínica Calidad - Todos los derechos reservados</p>
            </div>
        </body>
        </html>
        ";
    }


}
