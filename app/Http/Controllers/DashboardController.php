<?php

namespace App\Http\Controllers;

use App\Models\SolicitudCliente;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $inicioMes = Carbon::now()->startOfMonth();

        $base = SolicitudCliente::query();

        if (!$user->puedeVerTodo()) {
            $base->where('sucursal_id', $user->sucursal_id);
        }

        $kpis = [
            'hoy' => (clone $base)->whereDate('created_at', Carbon::today())->count(),
            'mes' => (clone $base)->where('created_at', '>=', $inicioMes)->count(),
            'grupos_mes' => (clone $base)
                ->where('created_at', '>=', $inicioMes)
                ->whereNotNull('grupo_id')
                ->distinct()
                ->count('grupo_id'),
            'revisados' => (clone $base)
                ->where('created_at', '>=', $inicioMes)
                ->where('estado', 'REVISADO')
                ->count(),
        ];

        $ultimos = (clone $base)
            ->with(['usuario', 'sucursal', 'grupo', 'color', 'talle', 'item'])
            ->latest()
            ->limit(10)
            ->get();

        $topCombinaciones = SolicitudCliente::query()
            ->from('solicitudes_clientes as s')
            ->join('grupo as g', 'g.id', '=', 's.grupo_id')
            ->join('color as c', 'c.id', '=', 's.color_id')
            ->join('talle as t', 't.id', '=', 's.talle_id')
            ->when(!$user->puedeVerTodo(), fn ($q) => $q->where('s.sucursal_id', $user->sucursal_id))
            ->where('s.created_at', '>=', $inicioMes)
            ->select([
                'g.nombre as grupo',
                'c.nombre as color',
                't.nombre as talle',
                DB::raw('COUNT(*) as cantidad'),
            ])
            ->groupBy('g.nombre', 'c.nombre', 't.nombre')
            ->orderByDesc('cantidad')
            ->limit(8)
            ->get();

        $topSucursales = collect();

        if ($user->puedeVerTodo()) {
            $topSucursales = SolicitudCliente::query()
                ->from('solicitudes_clientes as s')
                ->join('sucursales as su', 'su.id', '=', 's.sucursal_id')
                ->where('s.created_at', '>=', $inicioMes)
                ->select('su.nombre', DB::raw('COUNT(*) as cantidad'))
                ->groupBy('su.nombre')
                ->orderByDesc('cantidad')
                ->limit(8)
                ->get();
        }

        return view('dashboard', compact(
            'kpis',
            'ultimos',
            'topCombinaciones',
            'topSucursales'
        ));
    }
}
