<?php

namespace App\Http\Controllers;

use App\Models\Medicinas;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MedicinaController extends Controller
{
    public function index()
    {
        $medicinas = Medicinas::orderBy('Nombre')->get();

        return view('medicinas.index', compact('medicinas'));
    }

    public function create()
    {
        return view('medicinas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nombre' => 'required|string|max:200',
        ]);

        Medicinas::create([
            'IdMedicina' => (string) Str::uuid(),
            'Nombre'     => $request->Nombre,
            'Estado'     => 1,
        ]);

        return redirect()->route('medicinas.index')
            ->with('success', 'Medicina creada correctamente.');
    }

    public function edit(Medicinas $medicina)
    {
        return view('medicinas.edit', compact('medicina'));
    }

    public function update(Request $request, Medicinas $medicina)
    {
        $request->validate([
            'Nombre' => 'required|string|max:200',
            'Estado' => 'required|boolean',
        ]);

        $medicina->update($request->only('Nombre', 'Estado'));

        return redirect()->route('medicinas.index')
            ->with('success', 'Medicina actualizada correctamente.');
    }

    public function destroy(Medicinas $medicina)
    {
        $medicina->delete();

        return redirect()->route('medicinas.index')
            ->with('success', 'Medicina eliminada.');
    }
}
