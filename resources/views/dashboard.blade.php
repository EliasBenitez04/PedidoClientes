@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="top">
    <div>
        <h1>Dashboard</h1>
        <div class="muted">Resumen de pedidos registrados.</div>
    </div>
    <a class="btn btn-primary" href="{{ route('pedidos.create') }}">+ Nuevo pedido</a>
</div>

<div class="grid grid-4">
    <div class="card kpi"><div class="label">Pedidos hoy</div><div class="value">{{ $kpis['hoy'] }}</div></div>
    <div class="card kpi"><div class="label">Pedidos este mes</div><div class="value">{{ $kpis['mes'] }}</div></div>
    <div class="card kpi"><div class="label">Pendientes</div><div class="value">{{ $kpis['pendientes'] }}</div></div>
    <div class="card kpi"><div class="label">Procesados</div><div class="value">{{ $kpis['procesados'] }}</div></div>
</div>

<div class="card">
    <h2>Últimos registros</h2>
    @if($ultimos->isEmpty())
        <div class="empty">Todavía no hay pedidos registrados.</div>
    @else
        <table>
            <thead><tr><th>Fecha</th><th>Local</th><th>Usuario</th><th>Grupo</th><th>Color</th><th>Talle</th><th>Estado</th></tr></thead>
            <tbody>
            @foreach($ultimos as $p)
                <tr>
                    <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $p->sucursal->nombre }}</td>
                    <td>{{ $p->usuario->name }}</td>
                    <td>{{ $p->item->grupo }}</td>
                    <td>{{ $p->item->color }}</td>
                    <td><strong>{{ $p->item->talle }}</strong></td>
                    <td><span class="badge badge-{{ strtolower($p->estado) }}">{{ $p->estado }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
