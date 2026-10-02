<?php

namespace App\Http\Controllers;

use App\Models\CatalogoColor;
use App\Models\CatalogoGrupo;
use App\Models\CatalogoTalle;
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

        $grupos = CatalogoGrupo::where('activo', true)->orderBy('nombre')->pluck('nombre');
        $colores = CatalogoColor::where('activo', true)->orderBy('nombre')->pluck('nombre');
        $talles = CatalogoTalle::where('activo', true)->orderBy('nombre')->pluck('nombre');

        return view('reportes.index', compact(
            'pedidos',
            'sucursales',
            'usuarios',
            'grupos',
            'colores',
            'talles'
        ));
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
                    $pedido->grupo?->nombre ?? $pedido->item?->grupo ?? '',
                    $pedido->color?->nombre ?? $pedido->item?->color ?? '',
                    $pedido->talle?->nombre ?? $pedido->item?->talle ?? '',
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

        $query = Pedido::with(['usuario', 'sucursal', 'grupo', 'color', 'talle', 'item'])->latest();

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

        if ($request->filled('grupo')) {
            $query->whereHas('grupo', fn ($q) => $q->where('nombre', $request->grupo));
        }

        if ($request->filled('color')) {
            $query->whereHas('color', fn ($q) => $q->where('nombre', $request->color));
        }

        if ($request->filled('talle')) {
            $query->whereHas('talle', fn ($q) => $q->where('nombre', $request->talle));
        }

        return $query;
    }
}
