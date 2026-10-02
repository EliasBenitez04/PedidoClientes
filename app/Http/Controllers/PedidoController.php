<?php

namespace App\Http\Controllers;

use App\Models\CatalogoColor;
use App\Models\CatalogoGrupo;
use App\Models\CatalogoTalle;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $query = Pedido::with(['usuario', 'sucursal', 'grupo', 'color', 'talle', 'item'])->latest();

        if (!$user->puedeVerTodo()) {
            $query->where('sucursal_id', $user->sucursal_id);
        }

        $pedidos = $query->paginate(30);

        return view('pedidos.index', compact('pedidos'));
    }

    public function create()
    {
        $grupos = CatalogoGrupo::where('activo', true)->orderBy('nombre')->get();
        $colores = CatalogoColor::where('activo', true)->orderBy('nombre')->get();
        $talles = CatalogoTalle::where('activo', true)->orderBy('nombre')->get();

        return view('pedidos.create', compact('grupos', 'colores', 'talles'));
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
                'sucursal' => 'Tu usuario no tiene una sucursal asignada. Solicitá al administrador que la configure.',
            ]);
        }

        $grupo = CatalogoGrupo::whereKey($data['grupo_id'])->where('activo', true)->first();
        $color = CatalogoColor::whereKey($data['color_id'])->where('activo', true)->first();
        $talle = CatalogoTalle::whereKey($data['talle_id'])->where('activo', true)->first();

        if (!$grupo || !$color || !$talle) {
            return back()->withInput()->withErrors([
                'catalogo' => 'Uno de los valores seleccionados ya no está activo. Actualizá la pantalla y volvé a intentar.',
            ]);
        }

        $pedido = Pedido::create([
            'user_id' => $user->id,
            'sucursal_id' => $user->sucursal_id,
            'grupo_id' => $grupo->id,
            'color_id' => $color->id,
            'talle_id' => $talle->id,
            'catalogo_item_id' => null,
            'observacion' => $data['observacion'] ?? null,
            'estado' => 'PENDIENTE',
        ]);

        return redirect()->route('pedidos.create')
            ->with('success', 'Pedido #'.$pedido->id.' registrado correctamente.');
    }

    public function updateEstado(Request $request, Pedido $pedido)
    {
        $data = $request->validate([
            'estado' => ['required', 'in:PENDIENTE,PROCESADO,CANCELADO'],
        ]);

        $pedido->update(['estado' => $data['estado']]);

        return back()->with('success', 'Estado actualizado.');
    }
}
