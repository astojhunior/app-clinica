<?php

namespace App\Http\Controllers;

use App\Models\Sintoma;
use Illuminate\Http\Request;

class SintomaController extends Controller
{
    public function index()
    {
        $sintomas = Sintoma::orderBy('Descripcion')->get();

        return view('sintomas.index', compact('sintomas'));
    }

    public function create()
    {
        return view('sintomas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Descripcion' => 'required|string|max:200',
        ]);

        Sintoma::create([
            'IdSintomas'  => (string) \Illuminate\Support\Str::uuid(),
            'Descripcion' => $request->Descripcion,
            'Estado'      => 1,
        ]);

        return redirect()->route('sintomas.index')
            ->with('success', 'Síntoma creado correctamente.');
    }

    public function edit(Sintoma $sintoma)
    {
        return view('sintomas.edit', compact('sintoma'));
    }

    public function update(Request $request, Sintoma $sintoma)
    {
        $request->validate([
            'Descripcion' => 'required|string|max:200',
            'Estado'      => 'required|boolean',
        ]);

        $sintoma->update($request->only('Descripcion', 'Estado'));

        return redirect()->route('sintomas.index')
            ->with('success', 'Síntoma actualizado correctamente.');
    }

    public function destroy(Sintoma $sintoma)
    {
        $sintoma->delete();

        return redirect()->route('sintomas.index')
            ->with('success', 'Síntoma eliminado.');
    }
}
