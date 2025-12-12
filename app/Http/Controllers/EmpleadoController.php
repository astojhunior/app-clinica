<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Doctor;
use App\Models\Horario;
use App\Models\Cargo;
use App\Models\Especialidad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class EmpleadoController extends Controller
{
    /**
     * Lista empleados que SON doctores
     */
    public function index(Request $request)
    {
    $query = Empleado::with(['doctor', 'doctor.especialidad', 'doctor.horarios', 'cargo']);

    // Aplicar búsqueda si existe
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function($q) use ($search) {
            $q->where('Dni', 'like', "%$search%")
                ->orWhere('Nombres', 'like', "%$search%")
                ->orWhere('Apellidos', 'like', "%$search%");
        });
    }

    // Obtener registros por página (por defecto 10)
    $perPage = $request->input('per_page', 10);

    // Paginar con el valor dinámico
    $empleados = $query->orderBy('created_at', 'desc')->paginate($perPage);

    $cargos = Cargo::all();
    $especialidades = Especialidad::all();

    return view('empleados.index', compact('empleados', 'cargos', 'especialidades'));
    }

    public function crearUsuario($id)
    {
        $empleado = Empleado::with('cargo', 'user')->findOrFail($id);

        if ($empleado->user) {
            return back()->with('error', 'Este empleado ya tiene un usuario asociado.');
        }

        return view('empleados.crear-usuario', compact('empleado'));
    }

    public function storeUsuario(Request $request, $id)
    {
        $empleado = Empleado::with('cargo', 'user')->findOrFail($id);

        if ($empleado->user) {
            return back()->with('error', 'Este empleado ya tiene un usuario asociado.');
        }

        $request->validate([
            'email' => 'required|email|unique:users,email',
        ]);

        // DNI como contraseña inicial
        $passwordPlano = $empleado->Dni;

        $user = User::create([
            'name'               => $empleado->Nombres.' '.$empleado->Apellidos,
            'email'              => $request->email,
            'password'           => Hash::make($passwordPlano),
            'Empleado_idEmpleado'=> $empleado->IdEmpleado,
            'rol' => $empleado->cargo ? $empleado->cargo->DescripcionCargo : null,
        ]);

        // Asignar rol según cargo


        return redirect()->route('empleados.index')
            ->with('success', 'Usuario creado y asociado al empleado correctamente.');
    }



    public function indexDoctores(Request $request)
    {
        $query = Empleado::whereHas('doctor')->with(['doctor.especialidad', 'doctor.horarios', 'cargo']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('Dni', 'like', "%$search%")
                    ->orWhere('Nombres', 'like', "%$search%")
                    ->orWhere('Apellidos', 'like', "%$search%");
            });
        }

        $perPage = $request->input('per_page', 10);
        $doctores = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $cargos = Cargo::all();
        $especialidades = Especialidad::all();
        return view('empleados.index-doctores', compact('doctores', 'cargos', 'especialidades'));
    }

    /**
     * Lista empleados que NO son doctores
     */
    public function indexStaff(Request $request)
    {
        $query = Empleado::doesntHave('doctor')->with(['cargo']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('Dni', 'like', "%$search%")
                    ->orWhere('Nombres', 'like', "%$search%")
                    ->orWhere('Apellidos', 'like', "%$search%");
            });
        }

        $perPage = $request->input('per_page', 10);
        $staff = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $cargos = Cargo::all();
        return view('empleados.index-staff', compact('staff', 'cargos'));
    }



    public function store(Request $request)
    {

        $cargo = Cargo::find($request->input('Cargo_idCargo'));

        if ($cargo && str_contains(strtolower($cargo->DescripcionCargo), 'doctor')) {
            $request->merge(['es_doctor' => 1]);
        } else {
            $request->merge(['es_doctor' => 0]);
        }


        $dataEmpleado = $request->validate([
            'Dni'             => 'required|string|max:45|unique:empleados,Dni|regex:/^[0-9]+$/',
            'Nombres'         => 'required|string|max:45|regex:/^[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+$/u',
            'Apellidos'       => 'required|string|max:45|regex:/^[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+$/u',
            'CorreElec'       => 'nullable|email|max:45',
            'FechaIngreso'    => 'required|date_format:Y-m-d',
            'FechaNacimiento' => 'required|date_format:Y-m-d|before:-18 years',
            'Genero'          => 'required|in:Masculino,Femenino,Otro',
            'Cargo_idCargo'   => 'required|exists:cargos,IdCargo',
            'Estado'          => 'required|boolean',
            'es_doctor'       => 'nullable|in:0,1',
        ]);

        $esDoctor = (int)($request->input('es_doctor', 0));

        // 2) Si es doctor, validación adicional
        $dataDoctor = [];
        if ($esDoctor === 1) {
            $dataDoctor = $request->validate([
                'Numero_Colegiatura'          => 'required|string|max:45',
                'Biografia'                   => 'nullable|string|max:200',
                'Especialidad_idEspecialidad' => 'required|exists:especialidades,IdEspecialidad',
                'horarios'                    => 'required|array|min:1',
                'horarios.*.dias'             => 'required|array|min:1',
                'horarios.*.hora_inicio'      => 'required|date_format:H:i',
                'horarios.*.hora_fin'         => 'required|date_format:H:i|after:horarios.*.hora_inicio',
            ]);
        }

        DB::beginTransaction();
        try {
            // 3) Crear empleado
            $empleado = Empleado::create([
                'IdEmpleado'      => 'EMP-' . strtoupper(Str::random(10)),
                'Dni'             => $dataEmpleado['Dni'],
                'Nombres'         => ucwords(strtolower($dataEmpleado['Nombres'])),
                'Apellidos'       => ucwords(strtolower($dataEmpleado['Apellidos'])),
                'CorreElec'       => $dataEmpleado['CorreElec'] ?? null,
                'FechaIngreso'    => $dataEmpleado['FechaIngreso'],
                'FechaNacimiento' => $dataEmpleado['FechaNacimiento'],
                'Fechasalida'     => null,
                'Genero'          => $dataEmpleado['Genero'],
                'Estado'          => $dataEmpleado['Estado'],
                'Cargo_idCargo'   => $dataEmpleado['Cargo_idCargo'],
                'created_at'      => DB::raw('CONVERT(date, GETDATE())'),
                'updated_at'      => DB::raw('CONVERT(date, GETDATE())'),
            ]);

            // 4) Crear doctor + horarios solo si aplica
            if ($esDoctor === 1) {
                $doctor = Doctor::create([
                    'IdDoctores'              => 'DOC-' . strtoupper(Str::random(10)),
                    'Numero_Colegiatura'      => $dataDoctor['Numero_Colegiatura'],
                    'Biografia'               => $dataDoctor['Biografia'] ?? null,
                    'Especialidad_idEspecialidad' => $dataDoctor['Especialidad_idEspecialidad'],
                    'Empleado_idEmpleado'     => $empleado->IdEmpleado,
                    'created_at'              => DB::raw('CONVERT(date, GETDATE())'),
                    'updated_at'              => DB::raw('CONVERT(date, GETDATE())'),
                ]);

                $idsHorarios = [];
                foreach ($request->horarios as $h) {
                    $diasArray = $h['dias'] ?? [];
                    $diasTexto = implode(', ', $diasArray);

                    $horario = Horario::create([
                        'IdHorario'  => 'HOR-' . strtoupper(Str::random(10)),
                        'DiaSemana'  => $diasTexto,
                        'HoraInicio' => $h['hora_inicio'],
                        'HoraFin'    => $h['hora_fin'],
                        'created_at' => DB::raw('CONVERT(date, GETDATE())'),
                        'updated_at' => DB::raw('CONVERT(date, GETDATE())'),
                    ]);
                    $idsHorarios[] = $horario->IdHorario;
                }
                $doctor->horarios()->attach($idsHorarios);
            }

            DB::commit();
            return redirect()->route('empleados.index')
                ->with('success', 'Empleado registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al registrar el empleado: '.$e->getMessage());
        }
    }




    public function edit($id)
    {
        $empleado = Empleado::with(['doctor', 'doctor.horarios'])->findOrFail($id);
        $cargos = Cargo::all();
        $especialidades = Especialidad::all();
        $horarios = Horario::all();
        return view('empleados.edit', compact('empleado', 'cargos', 'especialidades', 'horarios'));
    }


    public function update(Request $request, $id)
    {
        $empleado = Empleado::findOrFail($id);

        $request->validate([
            'Dni'          => 'required|string|max:45|unique:empleados,Dni,' . $id . ',IdEmpleado|regex:/^[0-9]+$/',
            'Nombres'      => 'required|string|max:45|regex:/^[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+$/u',
            'Apellidos'    => 'required|string|max:45|regex:/^[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+$/u',
            'CorreElec'    => 'nullable|email|max:45',
            'FechaIngreso' => 'required|date',
            'FechaNacimiento' => 'required|date|before:-18 years',
            'Genero'       => 'required|in:Masculino,Femenino,Otro',
            'Cargo_idCargo'=> 'required|exists:cargos,IdCargo',
            'Estado'       => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Datos básicos
            $empleado->update([
                'Dni'             => $request->Dni,
                'Nombres'         => ucwords(strtolower($request->Nombres)),
                'Apellidos'       => ucwords(strtolower($request->Apellidos)),
                'CorreElec'       => $request->CorreElec,
                'FechaIngreso'    => $request->FechaIngreso,
                'FechaNacimiento' => $request->FechaNacimiento,
                'Genero'          => $request->Genero,
                'Estado'          => $request->Estado,
                'Cargo_idCargo'   => $request->Cargo_idCargo,
            ]);

            // Si es doctor
            if ($empleado->doctor && $request->has('Numero_Colegiatura')) {
                $doctor = $empleado->doctor;

                $doctor->update([
                    'Numero_Colegiatura'        => $request->Numero_Colegiatura,
                    'Biografia'                 => $request->Biografia,
                    'Especialidad_idEspecialidad' => $request->Especialidad_idEspecialidad,
                ]);

                // Eliminar horarios (modelo y pivote)
                $idsEliminar = explode(',', $request->input('horarios_eliminados', ''));
                $idsEliminar = array_filter($idsEliminar);
                if (!empty($idsEliminar)) {
                    // quitar de pivote y borrar registros
                    $doctor->horarios()->detach($idsEliminar);
                    Horario::whereIn('IdHorario', $idsEliminar)->delete();
                }

                // Actualizar horarios existentes
                if ($request->has('horarios_existentes')) {
                    foreach ($request->horarios_existentes as $horarioData) {
                        $diasArray = $horarioData['dias'] ?? [];
                        $diasTexto = implode(', ', $diasArray);

                        Horario::where('IdHorario', $horarioData['id'])->update([
                            'DiaSemana'  => $diasTexto,
                            'HoraInicio' => $horarioData['hora_inicio'] ?? null,
                            'HoraFin'    => $horarioData['hora_fin'] ?? null,
                        ]);
                    }
                }

                // Crear y asociar nuevos horarios
                if ($request->has('horarios_nuevos')) {
                    $idsNuevos = [];
                    foreach ($request->horarios_nuevos as $horarioNuevo) {
                        $diasArray = $horarioNuevo['dias'] ?? [];
                        $diasTexto = implode(', ', $diasArray);

                        $nuevo = Horario::create([
                            'IdHorario'  => 'HOR-' . strtoupper(Str::random(10)),
                            'DiaSemana'  => $diasTexto,
                            'HoraInicio' => $horarioNuevo['hora_inicio'] ?? null,
                            'HoraFin'    => $horarioNuevo['hora_fin'] ?? null,
                        ]);
                        $idsNuevos[] = $nuevo->IdHorario;
                    }
                    if (!empty($idsNuevos)) {
                        $doctor->horarios()->attach($idsNuevos);
                    }
                }
            }

            DB::commit();
            return redirect()->route('empleados.index')->with('success', 'Empleado actualizado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar el empleado: ' . $e->getMessage());
        }
    }



    public function destroy($id)
    {
        $empleado = Empleado::findOrFail($id);
        $empleado->update([
            'Estado' => 0,
            'Fechasalida' => now(),
        ]);
        return redirect()->route('empleados.index')->with('success', 'Empleado desactivado exitosamente');
    }
}
