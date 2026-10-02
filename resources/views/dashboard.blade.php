@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-heading">
    <div>
        <h1>Dashboard</h1>
        <p class="muted">Vista general del movimiento de pedidos.</p>
    </div>
</div>

<div class="hero-strip">
    <div>
        <h2>Registrá una solicitud en pocos pasos</h2>
        <p>Elegí grupo, color y talle. El sistema identifica automáticamente el usuario y la sucursal.</p>
    </div>
    <a class="btn btn-white" href="{{ route('pedidos.create') }}">+ Registrar nuevo pedido</a>
</div>

<div class="grid grid-4">
    <div class="kpi-card kpi-primary">
        <div class="kpi-top"><span class="kpi-label">Pedidos hoy</span><span class="kpi-icon">H</span></div>
        <div class="kpi-value">{{ $kpis['hoy'] }}</div>
    </div>
    <div class="kpi-card kpi-info">
        <div class="kpi-top"><span class="kpi-label">Este mes</span><span class="kpi-icon">M</span></div>
        <div class="kpi-value">{{ $kpis['mes'] }}</div>
    </div>
    <div class="kpi-card kpi-warning">
        <div class="kpi-top"><span class="kpi-label">Pendientes</span><span class="kpi-icon">P</span></div>
        <div class="kpi-value">{{ $kpis['pendientes'] }}</div>
    </div>
    <div class="kpi-card kpi-success">
        <div class="kpi-top"><span class="kpi-label">Procesados</span><span class="kpi-icon">✓</span></div>
        <div class="kpi-value">{{ $kpis['procesados'] }}</div>
    </div>
</div>

<div class="card table-card" style="margin-top:20px">
    <div class="table-toolbar">
        <div>
            <h2>Últimos registros</h2>
            <div class="card-subtitle">Actividad reciente del sistema</div>
        </div>
        <a href="{{ route('pedidos.index') }}" class="btn btn-soft btn-sm">Ver todos</a>
    </div>

    @if($ultimos->isEmpty())
        <div class="empty">Todavía no hay pedidos registrados.</div>
    @else
        <div class="table-scroll">
            <table>
                <thead>
                    <tr><th>Fecha</th><th>Local</th><th>Usuario</th><th>Grupo</th><th>Color</th><th>Talle</th><th>Estado</th></tr>
                </thead>
                <tbody>
                @foreach($ultimos as $p)
                    <tr>
                        <td><span class="cell-title">{{ $p->created_at->format('d/m/Y') }}</span><span class="cell-meta">{{ $p->created_at->format('H:i') }}</span></td>
                        <td>{{ $p->sucursal->nombre }}</td>
                        <td>{{ $p->usuario->name }}</td>
                        <td><span class="cell-title">{{ $p->item->grupo }}</span></td>
                        <td>{{ $p->item->color }}</td>
                        <td><strong>{{ $p->item->talle }}</strong></td>
                        <td><span class="badge badge-{{ strtolower($p->estado) }}">{{ $p->estado }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
