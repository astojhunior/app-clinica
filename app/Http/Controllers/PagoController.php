<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\DetallePago;
use App\Models\Cita;
use App\Models\TipoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PagoController extends Controller
{
    // Listado de pagos
    public function index(Request $request)
    {
        $query = Pago::with('cita.paciente', 'cita.doctor', 'detalles', 'tipoPago');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('IdPago', 'like', "%$search%")
                  ->orWhereHas('tipoPago', function ($qt) use ($search) {
                      $qt->where('TipoDescripcion', 'like', "%$search%");
                  })
                  ->orWhereHas('cita', function ($qc) use ($search) {
                      $qc->where('FechaCita', 'like', "%$search%");
                  });
            });
        }

        $perPage = $request->input('per_page', 10);
        $pagos = $query->paginate($perPage);

        return view('pagos.index', compact('pagos'));
    }

    // Vista para registrar un pago
    public function create(Request $request)
    {
        $query = Cita::with([
                'paciente',
                'doctor',
                'pagos' => function ($q) {
                    // solo pagos NO anulados (EstadoPago != 2)
                    $q->where('EstadoPago', '!=', 2);
                }
            ])
            // SOLO citas pendientes, que son las que se pueden seguir pagando
            ->where('EstadoPago', 'Pendiente');

        // Filtro por ID de Cita (para el buscador incremental)
        if ($request->filled('cita_id')) {
            $query->where('IdCita', 'like', '%' . $request->cita_id . '%');
        }

        // Filtro por fecha de la cita (formato Y-m-d)
        if ($request->filled('fecha')) {
            $query->whereDate('FechaCita', $request->fecha);
        }

        // Paginación
        $perPage = $request->input('per_page', 10);
        $citas   = $query->orderBy('FechaCita', 'asc')->paginate($perPage);

        $tiposPagos = TipoPago::where('TipoEstado', true)->get();

        return view('pagos.create', compact('citas', 'tiposPagos'))
            ->with([
                'cita_id' => $request->cita_id,
                'fecha'   => $request->fecha,
                'per_page'=> $perPage,
            ]);
    }


    // Guardar pago nuevo
    public function store(Request $request)
    {
        $request->validate([
            'CitaidCita'   => 'required|exists:citas,IdCita',
            'MontoPago'    => 'required|numeric|min:0.01',
            'TipoPago_id'  => 'required|exists:tipos_pagos,IDtipos',
            'EstadoPago'   => 'required|string',
        ]);

        $cita = Cita::findOrFail($request->CitaidCita);

        // Validar contra saldo
        $pagadoAcumulado = $cita->pagos()->sum('MontoTotal');
        $montoPendiente  = $cita->CostoTotal - $pagadoAcumulado;

        if ($montoPendiente <= 0) {
            return back()->withErrors(['MontoPago' => 'Esta cita ya está completamente pagada.'])->withInput();
        }
        if ($request->MontoPago > $montoPendiente) {
            return back()->withErrors(['MontoPago' => 'El monto a pagar excede el saldo pendiente para la cita.'])->withInput();
        }

        // -----------------------------------------------------------------
        // Cálculo de IGV para este pago
        // -----------------------------------------------------------------
        $montoPago = $request->MontoPago;     // total que el usuario está abonando
        $tasaIgv   = 0.18;                    // cámbialo si usas otra tasa

        // SubTotal = base imponible, MontoIGV = impuesto
        $subTotal = $montoPago / (1 + $tasaIgv);      // base del pago
        $montoIGV = $montoPago - $subTotal;          // impuesto del pago

        $subTotal = round($subTotal, 2);
        $montoIGV = round($montoIGV, 2);

        $idPago    = 'PAG-' . strtoupper(Str::random(12));
        $fechaPago = now()->format('Y-m-d\TH:i:s');

        $pago = Pago::create([
            'IdPago'      => $idPago,
            'FechaPago'   => $fechaPago,
            'EstadoPago'  => $request->EstadoPago == 'Pagado' ? 1 : 0,
            'SubTotal'    => $subTotal,
            'MontoIGV'    => $montoIGV,
            'MontoTotal'  => $montoPago,
            'TipoPago_id' => $request->TipoPago_id,
            'Cita_idCita' => $request->CitaidCita,
        ]);
        // -----------------------------------------------------------------

        // Registrar detalle(s)
        if ($request->has('detalles') && is_array($request->detalles) && count($request->detalles)) {
            foreach ($request->detalles as $detalle) {
                DetallePago::create([
                    'Pago_idPago' => $pago->IdPago,
                    'Descripcion' => $detalle['Descripcion'],
                    'Monto'       => $detalle['Monto'],
                    'Cantidad'    => $detalle['Cantidad'],
                ]);
            }
        } else {
            DetallePago::create([
                'Pago_idPago' => $pago->IdPago,
                'Descripcion' => 'Abono para cita ' . $cita->IdCita,
                'Monto'       => $montoPago,
                'Cantidad'    => 1,
            ]);
        }

        // Actualizar estado de la cita según suma de pagos
        $pagadoAcumulado = $cita->pagos()->sum('MontoTotal');
        if (abs($pagadoAcumulado - $cita->CostoTotal) < 0.01) {
            $cita->EstadoPago = 'Pagado';
        } else {
            $cita->EstadoPago = 'Pendiente';
        }
        $cita->save();

        return redirect()->route('pagos.index')->with('success', 'Pago registrado correctamente');
    }


    // Vista para editar pago
    public function edit($id)
    {
        $pago = Pago::findOrFail($id);
        $citas = Cita::with('paciente')->get();
        $tiposPagos = TipoPago::where('TipoEstado', true)->get();

        return view('pagos.edit', compact('pago', 'citas', 'tiposPagos'));
    }

    // Actualizar pago existente
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'EstadoPago'  => 'required|integer', // 1=Pagado, 0=Pendiente
            'MontoTotal'  => 'required|numeric',
            'TipoPago_id' => 'required|exists:tipos_pagos,IDtipos',
            'Cita_idCita' => 'required|string|exists:citas,IdCita',
        ]);

        $pago = Pago::findOrFail($id);

        $tasaIgv   = 0.18;
        $total     = $validated['MontoTotal'];
        $subTotal  = $total / (1 + $tasaIgv);
        $montoIGV  = $total - $subTotal;

        $subTotal  = round($subTotal, 2);
        $montoIGV  = round($montoIGV, 2);

        $pago->update([
            'EstadoPago'  => $validated['EstadoPago'],
            'SubTotal'    => $subTotal,
            'MontoIGV'    => $montoIGV,
            'MontoTotal'  => $total,
            'TipoPago_id' => $validated['TipoPago_id'],
            'Cita_idCita' => $validated['Cita_idCita'],
        ]);

        // Actualizar estado de la cita según pagos totales
        $cita = Cita::findOrFail($validated['Cita_idCita']);
        $pagadoAcumulado = $cita->pagos()->sum('MontoTotal');
        $montoRequerido  = $cita->CostoTotal;

        $cita->EstadoPago = (abs($pagadoAcumulado - $montoRequerido) < 0.01) ? 'Pagado' : 'Pendiente';
        $cita->save();

        return redirect()->route('pagos.index')->with('success', 'Pago actualizado correctamente');
    }


    // Eliminar un pago
    public function destroy($id)
    {
        $pago   = Pago::findOrFail($id);
        $citaId = $pago->Cita_idCita;

        // Eliminar detalles asociados
        $pago->detalles()->delete();
        $pago->delete();

        // Recalcular estado de la cita
        $cita = Cita::find($citaId);
        if ($cita) {
            $pagadoAcumulado = $cita->pagos()->sum('MontoTotal');
            $montoRequerido  = $cita->CostoTotal;

            $cita->EstadoPago = (abs($pagadoAcumulado - $montoRequerido) < 0.01) ? 'Pagado' : 'Pendiente';
            $cita->save();
        }

        return redirect()->route('pagos.index')->with('success', 'Pago eliminado correctamente');
    }

    // Mostrar pago individual
    public function show($id)
    {
        $pago = Pago::with([
            'cita.paciente',
            'cita.doctor.empleado',
            'tipoPago',
            'detalles',
        ])->findOrFail($id);

        return view('pagos.show', compact('pago'));
    }

    // Pagos filtrados por estado
    public function pagosPorEstado($estado)
    {
        $pagos = Pago::where('EstadoPago', $estado)->with('cita')->get();
        return response()->json($pagos);
    }

    // Pagos filtrados por fecha
    public function pagosPorFecha($fecha)
    {
        $pagos = Pago::whereDate('FechaPago', $fecha)->with('cita')->get();
        return response()->json($pagos);
    }


    public function anular($id)
    {
        $pago = Pago::with('cita')->findOrFail($id);

        // 1 = Pagado, 0 = Pendiente, 2 = Anulado  (definimos esta convención)
        $pago->EstadoPago = 2;
        $pago->save();

        // Opcional: agrega una marca en los detalles
        foreach ($pago->detalles as $detalle) {
            $detalle->Descripcion = '[ANULADO] ' . $detalle->Descripcion;
            $detalle->save();
        }

        // Recalcular estado de la cita SOLO con pagos no anulados (EstadoPago != 2)
        if ($pago->cita) {
            $cita = $pago->cita;

            $pagadoAcumulado = $cita->pagos()
                ->where('EstadoPago', '!=', 2)
                ->sum('MontoTotal');

            if (abs($pagadoAcumulado - $cita->CostoTotal) < 0.01) {
                $cita->EstadoPago = 'Pagado';
            } else {
                $cita->EstadoPago = 'Pendiente';
            }
            $cita->save();
        }

        return redirect()->route('pagos.index')
            ->with('success', 'Pago anulado correctamente');
    }

}
