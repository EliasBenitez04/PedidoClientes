<?php

namespace App\Http\Controllers;

use App\Models\CatalogoColor;
use App\Models\CatalogoGrupo;
use App\Models\CatalogoTalle;
use App\Models\SolicitudCliente;
use Illuminate\Http\Request;

class SolicitudClienteController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $query = SolicitudCliente::with([
                'usuario',
                'sucursal',
                'grupo',
                'color',
                'talle',
                'item',
                'revisor',
            ])
            ->latest();

        if (!$user->puedeVerTodo()) {
            $query->where('sucursal_id', $user->sucursal_id);
        }

        $solicitudes = $query->paginate(30);

        return view('solicitudes.index', compact('solicitudes'));
    }

    public function create()
    {
        $grupos = CatalogoGrupo::where('activo', true)->orderBy('nombre')->get();
        $colores = CatalogoColor::where('activo', true)->orderBy('nombre')->get();
        $talles = CatalogoTalle::where('activo', true)->orderBy('nombre')->get();

        return view('solicitudes.create', compact('grupos', 'colores', 'talles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'grupo_id' => ['required', 'integer', 'exists:grupo,id'],
            'color_id' => ['required', 'integer', 'exists:color,id'],
            'talle_id' => ['required', 'integer', 'exists:talle,id'],
            'observacion' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = auth()->user();

        if (!$user->sucursal_id) {
            return back()->withInput()->withErrors([
                'sucursal' => 'Tu usuario no tiene una sucursal asignada.',
            ]);
        }

        $grupo = CatalogoGrupo::whereKey($data['grupo_id'])->where('activo', true)->first();
        $color = CatalogoColor::whereKey($data['color_id'])->where('activo', true)->first();
        $talle = CatalogoTalle::whereKey($data['talle_id'])->where('activo', true)->first();

        if (!$grupo || !$color || !$talle) {
            return back()->withInput()->withErrors([
                'catalogo' => 'Uno de los valores seleccionados ya no está disponible.',
            ]);
        }

        $solicitud = SolicitudCliente::create([
            'user_id' => $user->id,
            'sucursal_id' => $user->sucursal_id,
            'grupo_id' => $grupo->id,
            'color_id' => $color->id,
            'talle_id' => $talle->id,
            'catalogo_item_id' => null,
            'observacion' => $data['observacion'] ?? null,
            'estado' => 'REGISTRADO',
        ]);

        return redirect()
            ->route('solicitudes.create')
            ->with('success', 'Demanda registrada #'.$solicitud->id.'.');
    }

    public function revisar(SolicitudCliente $solicitud)
    {
        if ($solicitud->estado === 'REVISADO') {
            return back()->with('success', 'El registro ya estaba revisado.');
        }

        $solicitud->update([
            'estado' => 'REVISADO',
            'revisado_por_id' => auth()->id(),
            'revisado_en' => now(),
        ]);

        return back()->with('success', 'Registro #'.$solicitud->id.' marcado como revisado.');
    }

    public function revisarMasivo(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'distinct', 'exists:solicitudes_clientes,id'],
        ]);

        $cantidad = SolicitudCliente::whereIn('id', $data['ids'])
            ->where('estado', 'REGISTRADO')
            ->update([
                'estado' => 'REVISADO',
                'revisado_por_id' => auth()->id(),
                'revisado_en' => now(),
                'updated_at' => now(),
            ]);

        return back()->with(
            'success',
            $cantidad === 1
                ? '1 registro marcado como revisado.'
                : $cantidad.' registros marcados como revisados.'
        );
    }

    public function updateEstado(Request $request, SolicitudCliente $solicitud)
    {
        $data = $request->validate([
            'estado' => ['required', 'in:REGISTRADO,REVISADO,DESCARTADO'],
        ]);

        $payload = ['estado' => $data['estado']];

        if ($data['estado'] === 'REVISADO') {
            $payload['revisado_por_id'] = auth()->id();
            $payload['revisado_en'] = now();
        } elseif ($data['estado'] === 'REGISTRADO') {
            $payload['revisado_por_id'] = null;
            $payload['revisado_en'] = null;
        }

        $solicitud->update($payload);

        return back()->with('success', 'Estado actualizado.');
    }
}
