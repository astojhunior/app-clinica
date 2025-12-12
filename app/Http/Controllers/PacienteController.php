<?php
namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PacienteController extends Controller
{
    // Listar y buscar pacientes
    public function index(Request $request)
    {
        $query = Paciente::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('Dni', 'like', "%$search%")
                  ->orWhere('Nombres', 'like', "%$search%")
                  ->orWhere('Apellidos', 'like', "%$search%");
        }

        $perPage = $request->input('per_page', 10); // Default 10 registros
        $pacientes = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('pacientes.index', compact('pacientes'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('pacientes.index');
    }

    // Guardar nuevo paciente
    public function store(Request $request)
    {
            $request->validate([
            // DNI: requerido, único, solo números, entre 8-20 caracteres
            'Dni' => [
                'required',
                'string',
                'max:45',
                'unique:pacientes,Dni',
                'regex:/^[0-9]+$/' // Solo números
            ],

            // Nombres: requerido, solo letras y espacios
            'Nombres' => [
                'required',
                'string',
                'max:45',
                'regex:/^[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+$/u' // Solo letras y espacios
            ],

            // Apellidos: requerido, solo letras y espacios
            'Apellidos' => [
                'required',
                'string',
                'max:45',
                'regex:/^[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+$/u'
            ],

            // Correo: opcional, formato email válido, único
            'CorreoElectronico' => [
                'nullable',
                'email',
                'max:45',
                'unique:pacientes,CorreoElectronico'
            ],

            // Teléfono: opcional, solo números y guiones
            'Telefono' => [
                'nullable',
                'string',
                'max:45',
                'regex:/^[0-9\-\+\(\)\s]+$/' // Números, guiones, paréntesis, espacios
            ],


            // Género: requerido, valores específicos
            'Genero' => [
                'required',
                'in:Masculino,Femenino,Otro'
            ],

            // Dirección: opcional, longitud máxima
            'Direccion' => [
                'nullable',
                'string',
                'max:200'
            ],

            // Estado: requerido, booleano
            'Estado' => [
                'required',
                'boolean'
            ],
        ], [
            // Mensajes personalizados en español
            'Dni.required' => 'El DNI es obligatorio.',
            'Dni.unique' => 'Este DNI ya está registrado.',
            'Dni.regex' => 'El DNI debe contener solo números.',
            'Dni.max' => 'El DNI no puede tener más de 45 caracteres.',

            'Nombres.required' => 'El nombre es obligatorio.',
            'Nombres.regex' => 'El nombre solo puede contener letras y espacios.',
            'Nombres.max' => 'El nombre no puede tener más de 45 caracteres.',

            'Apellidos.required' => 'Los apellidos son obligatorios.',
            'Apellidos.regex' => 'Los apellidos solo pueden contener letras y espacios.',
            'Apellidos.max' => 'Los apellidos no pueden tener más de 45 caracteres.',

            'CorreoElectronico.email' => 'Debe ingresar un correo electrónico válido.',
            'CorreoElectronico.unique' => 'Este correo electrónico ya está registrado.',
            'CorreoElectronico.max' => 'El correo no puede tener más de 45 caracteres.',

            'Telefono.regex' => 'El teléfono solo puede contener números, guiones y paréntesis.',
            'Telefono.max' => 'El teléfono no puede tener más de 45 caracteres.',

            'FechaNacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'FechaNacimiento.date' => 'Debe ingresar una fecha válida.',
            'FechaNacimiento.after' => 'La fecha de nacimiento no es válida.',

            'Genero.required' => 'Debe seleccionar un género.',
            'Genero.in' => 'El género seleccionado no es válido.',

            'Direccion.max' => 'La dirección no puede tener más de 200 caracteres.',

            'Estado.required' => 'Debe seleccionar un estado.',
            'Estado.boolean' => 'El estado debe ser activo o inactivo.',
        ]);

        // Generar ID único para el paciente
        Paciente::create([
            'IdPaciente' => 'PAC-' . strtoupper(Str::random(10)),
            'Dni' => $request->Dni,
            'Nombres' => ucwords(strtolower($request->Nombres)), // Primera letra mayúscula
            'Apellidos' => ucwords(strtolower($request->Apellidos)),
            'CorreoElectronico' => $request->CorreoElectronico,
            'FechaNacimiento' => $request->FechaNacimiento,
            'Genero' => $request->Genero,
            'Direccion' => $request->Direccion,
            'Telefono' => $request->Telefono,
            'Estado' => $request->Estado,
        ]);

        return redirect()->route('pacientes.index')->with('success', 'Paciente registrado exitosamente');
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $paciente = Paciente::findOrFail($id);
        return view('pacientes.edit', compact('paciente'));
    }

    // Actualizar paciente
        public function update(Request $request, $id)
        {
            $paciente = Paciente::findOrFail($id);

            $request->validate([
                'Dni' => [
                    'required',
                    'string',
                    'max:45',
                    'unique:pacientes,Dni,' . $id . ',IdPaciente', // Excluir el registro actual
                    'regex:/^[0-9]+$/'
                ],
                'Nombres' => [
                    'required',
                    'string',
                    'max:45',
                    'regex:/^[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+$/u'
                ],
                'Apellidos' => [
                    'required',
                    'string',
                    'max:45',
                    'regex:/^[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+$/u'
                ],
                'CorreoElectronico' => [
                    'nullable',
                    'email',
                    'max:45',
                    'unique:pacientes,CorreoElectronico,' . $id . ',IdPaciente'
                ],
                'Telefono' => [
                    'nullable',
                    'string',
                    'max:45',
                    'regex:/^[0-9\-\+\(\)\s]+$/'
                ],

                'Genero' => [
                    'required',
                    'in:Masculino,Femenino,Otro'
                ],
                'Direccion' => [
                    'nullable',
                    'string',
                    'max:200'
                ],
                'Estado' => [
                    'required',
                    'boolean'
                ],
            ], [
                // Los mismos mensajes personalizados
                'Dni.required' => 'El DNI es obligatorio.',
                'Dni.unique' => 'Este DNI ya está registrado.',
                'Dni.regex' => 'El DNI debe contener solo números.',
                'Nombres.required' => 'El nombre es obligatorio.',
                'Nombres.regex' => 'El nombre solo puede contener letras y espacios.',
                'Apellidos.required' => 'Los apellidos son obligatorios.',
                'Apellidos.regex' => 'Los apellidos solo pueden contener letras y espacios.',
                'CorreoElectronico.email' => 'Debe ingresar un correo electrónico válido.',
                'CorreoElectronico.unique' => 'Este correo electrónico ya está registrado.',
                'Telefono.regex' => 'El teléfono solo puede contener números, guiones y paréntesis.',
                'FechaNacimiento.required' => 'La fecha de nacimiento es obligatoria.',
                'FechaNacimiento.date' => 'Debe ingresar una fecha válida.',
                'FechaNacimiento.after' => 'La fecha de nacimiento no es válida.',
                'Genero.required' => 'Debe seleccionar un género.',
                'Genero.in' => 'El género seleccionado no es válido.',
            ]);

            $paciente->update([
                'Dni' => $request->Dni,
                'Nombres' => ucwords(strtolower($request->Nombres)),
                'Apellidos' => ucwords(strtolower($request->Apellidos)),
                'CorreoElectronico' => $request->CorreoElectronico,
                'FechaNacimiento' => $request->FechaNacimiento,
                'Genero' => $request->Genero,
                'Direccion' => $request->Direccion,
                'Telefono' => $request->Telefono,
                'Estado' => $request->Estado,
            ]);

            return redirect()->route('pacientes.index')->with('success', 'Paciente actualizado exitosamente');
        }


    // Eliminar paciente (cambio de estado a inactivo)
    public function destroy($id)
    {
        $paciente = Paciente::findOrFail($id);

        // Opción 1: Cambiar estado a inactivo en lugar de eliminar (recomendado)
        $paciente->update(['Estado' => 0]);
        return redirect()->route('pacientes.index')->with('success', 'Paciente desactivado exitosamente');

        // Opción 2: Eliminar permanentemente (descomentar si prefieres esto)
        // $paciente->delete();
        // return redirect()->route('pacientes.index')->with('success', 'Paciente eliminado exitosamente');
    }

    // Método adicional: Activar paciente
    public function activate($id)
    {
        $paciente = Paciente::findOrFail($id);
        $paciente->update(['Estado' => 1]);
        return redirect()->route('pacientes.index')->with('success', 'Paciente activado exitosamente');
    }
}
