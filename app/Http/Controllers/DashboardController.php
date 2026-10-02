<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $base = Pedido::query();

        if (!$user->puedeVerTodo()) {
            $base->where('sucursal_id', $user->sucursal_id);
        }

        $hoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();

        $kpis = [
            'hoy' => (clone $base)->whereDate('created_at', $hoy)->count(),
            'mes' => (clone $base)->where('created_at', '>=', $inicioMes)->count(),
            'pendientes' => (clone $base)->where('estado', 'PENDIENTE')->count(),
            'procesados' => (clone $base)->where('estado', 'PROCESADO')->count(),
        ];

        $ultimos = (clone $base)
            ->with(['usuario', 'sucursal', 'item'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard', compact('kpis', 'ultimos'));
    }
}
