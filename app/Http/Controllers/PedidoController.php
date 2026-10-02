<?php

namespace App\Http\Controllers;

use App\Models\CatalogoItem;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $query = Pedido::with(['usuario', 'sucursal', 'item'])->latest();

        if (!$user->puedeVerTodo()) {
            $query->where('sucursal_id', $user->sucursal_id);
        }

        $pedidos = $query->paginate(30);

        return view('pedidos.index', compact('pedidos'));
    }

    public function create()
    {
        $grupos = CatalogoItem::where('activo', true)
            ->select('grupo')->distinct()->orderBy('grupo')->pluck('grupo');

        return view('pedidos.create', compact('grupos'));
    }

    public function colores(Request $request)
    {
        $request->validate(['grupo' => ['required', 'string', 'max:120']]);

        return response()->json(
            CatalogoItem::where('activo', true)
                ->where('grupo', $request->grupo)
                ->select('color')->distinct()->orderBy('color')->pluck('color')
        );
    }

    public function talles(Request $request)
    {
        $request->validate([
            'grupo' => ['required', 'string', 'max:120'],
            'color' => ['required', 'string', 'max:120'],
        ]);

        return response()->json(
            CatalogoItem::where('activo', true)
                ->where('grupo', $request->grupo)
                ->where('color', $request->color)
                ->select('talle')->distinct()->orderBy('talle')->pluck('talle')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'grupo' => ['required', 'string', 'max:120'],
            'color' => ['required', 'string', 'max:120'],
            'talle' => ['required', 'string', 'max:50'],
            'observacion' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = auth()->user();

        if (!$user->sucursal_id) {
            return back()->withInput()->withErrors([
                'sucursal' => 'Tu usuario no tiene una sucursal asignada. Solicitá al administrador que la configure.',
            ]);
        }

        $item = CatalogoItem::where('activo', true)
            ->where('grupo', $data['grupo'])
            ->where('color', $data['color'])
            ->where('talle', $data['talle'])
            ->first();

        if (!$item) {
            return back()->withInput()->withErrors([
                'talle' => 'La combinación grupo/color/talle ya no está disponible.',
            ]);
        }

        $pedido = Pedido::create([
            'user_id' => $user->id,
            'sucursal_id' => $user->sucursal_id,
            'catalogo_item_id' => $item->id,
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
