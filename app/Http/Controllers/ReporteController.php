<?php

namespace App\Http\Controllers;

use App\Models\CatalogoItem;
use App\Models\Pedido;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->consulta($request);
        $pedidos = $query->paginate(50)->withQueryString();

        $sucursales = auth()->user()->puedeVerTodo()
            ? Sucursal::where('activo', true)->orderBy('nombre')->get()
            : collect();

        $usuarios = auth()->user()->puedeVerTodo()
            ? User::where('activo', true)->orderBy('name')->get()
            : collect();

        $grupos = CatalogoItem::select('grupo')->distinct()->orderBy('grupo')->pluck('grupo');

        return view('reportes.index', compact('pedidos', 'sucursales', 'usuarios', 'grupos'));
    }

    public function exportar(Request $request)
    {
        $pedidos = $this->consulta($request)->get();
        $filename = 'reporte_pedidos_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($pedidos) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'Fecha', 'Sucursal', 'Usuario', 'Grupo', 'Color', 'Talle', 'Observación', 'Estado'], ';');

            foreach ($pedidos as $pedido) {
                fputcsv($out, [
                    $pedido->id,
                    $pedido->created_at->format('d/m/Y H:i:s'),
                    $pedido->sucursal->nombre,
                    $pedido->usuario->name,
                    $pedido->item->grupo,
                    $pedido->item->color,
                    $pedido->item->talle,
                    $pedido->observacion,
                    $pedido->estado,
                ], ';');
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function consulta(Request $request)
    {
        $user = auth()->user();

        $query = Pedido::with(['usuario', 'sucursal', 'item'])->latest();

        if (!$user->puedeVerTodo()) {
            $query->where('sucursal_id', $user->sucursal_id);
        } elseif ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->integer('sucursal_id'));
        }

        if ($request->filled('usuario_id') && $user->puedeVerTodo()) {
            $query->where('user_id', $request->integer('usuario_id'));
        }

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('grupo') || $request->filled('color') || $request->filled('talle')) {
            $query->whereHas('item', function ($q) use ($request) {
                if ($request->filled('grupo')) $q->where('grupo', $request->grupo);
                if ($request->filled('color')) $q->where('color', $request->color);
                if ($request->filled('talle')) $q->where('talle', $request->talle);
            });
        }

        return $query;
    }
}
