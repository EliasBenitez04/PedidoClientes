@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-heading">
    <div>
        <h1>Demanda no cubierta</h1>
        <p class="muted">Qué están buscando los clientes y el local no está pudiendo ofrecer.</p>
    </div>
</div>

<div class="hero-strip">
    <div>
        <h2>Convertí cada faltante en información comercial</h2>
        <p>Cuando un cliente solicita algo que no está disponible, registralo para detectar oportunidades de stock y surtido.</p>
    </div>

    <a class="btn btn-white" href="{{ route('solicitudes.create') }}">+ Registrar demanda</a>
</div>

<div class="grid grid-4">
    <div class="kpi-card kpi-primary">
        <div class="kpi-top">
            <span class="kpi-label">Consultas hoy</span>
            <span class="kpi-icon">H</span>
        </div>
        <div class="kpi-value">{{ $kpis['hoy'] }}</div>
    </div>

    <div class="kpi-card kpi-info">
        <div class="kpi-top">
            <span class="kpi-label">Consultas este mes</span>
            <span class="kpi-icon">M</span>
        </div>
        <div class="kpi-value">{{ $kpis['mes'] }}</div>
    </div>

    <div class="kpi-card kpi-warning">
        <div class="kpi-top">
            <span class="kpi-label">Grupos solicitados</span>
            <span class="kpi-icon">G</span>
        </div>
        <div class="kpi-value">{{ $kpis['grupos_mes'] }}</div>
    </div>

    <div class="kpi-card kpi-success">
        <div class="kpi-top">
            <span class="kpi-label">Registros revisados</span>
            <span class="kpi-icon">✓</span>
        </div>
        <div class="kpi-value">{{ $kpis['revisados'] }}</div>
    </div>
</div>

<div class="grid grid-2" style="margin-top:20px">
    <div class="card table-card">
        <div class="table-toolbar">
            <div>
                <h2>Combinaciones más solicitadas</h2>
                <div class="card-subtitle">Top del mes actual</div>
            </div>
        </div>

        @if($topCombinaciones->isEmpty())
            <div class="empty">Todavía no hay suficientes registros este mes.</div>
        @else
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr><th>Grupo</th><th>Color</th><th>Talle</th><th>Consultas</th></tr>
                    </thead>
                    <tbody>
                    @foreach($topCombinaciones as $item)
                        <tr>
                            <td><span class="cell-title">{{ $item->grupo }}</span></td>
                            <td>{{ $item->color }}</td>
                            <td><strong>{{ $item->talle }}</strong></td>
                            <td><span class="badge badge-registrado">{{ $item->cantidad }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if(auth()->user()->puedeVerTodo())
        <div class="card table-card">
            <div class="table-toolbar">
                <div>
                    <h2>Demanda por sucursal</h2>
                    <div class="card-subtitle">Locales con más consultas no cubiertas este mes</div>
                </div>
            </div>

            @if($topSucursales->isEmpty())
                <div class="empty">Todavía no hay registros este mes.</div>
            @else
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr><th>Sucursal</th><th>Consultas</th></tr>
                        </thead>
                        <tbody>
                        @foreach($topSucursales as $local)
                            <tr>
                                <td><span class="cell-title">{{ $local->nombre }}</span></td>
                                <td><strong>{{ $local->cantidad }}</strong></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <div>
                    <h2>Cómo registrar correctamente</h2>
                    <div class="card-subtitle">Para que el dato sea útil</div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-row">
                    <div class="info-dot">1</div>
                    <div><strong>Solo faltantes reales</strong><span>El cliente realmente quiso el producto y no estaba disponible.</span></div>
                </div>
                <div class="info-row">
                    <div class="info-dot">2</div>
                    <div><strong>Un cliente = un registro</strong><span>Si tres clientes preguntan lo mismo, registralo tres veces.</span></div>
                </div>
                <div class="info-row">
                    <div class="info-dot">3</div>
                    <div><strong>Elegí la variante correcta</strong><span>Grupo, color y talle deben reflejar lo que pidió.</span></div>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="card table-card" style="margin-top:20px">
    <div class="table-toolbar">
        <div>
            <h2>Últimos registros</h2>
            <div class="card-subtitle">Consultas no cubiertas registradas recientemente</div>
        </div>

        <a href="{{ route('solicitudes.index') }}" class="btn btn-soft btn-sm">Ver historial</a>
    </div>

    @if($ultimos->isEmpty())
        <div class="empty">Todavía no hay demanda registrada.</div>
    @else
        <div class="table-scroll">
            <table>
                <thead>
                    <tr><th>Fecha</th><th>Local</th><th>Grupo</th><th>Color</th><th>Talle</th><th>Estado</th></tr>
                </thead>
                <tbody>
                @foreach($ultimos as $s)
                    <tr>
                        <td>
                            <span class="cell-title">{{ $s->created_at->format('d/m/Y') }}</span>
                            <span class="cell-meta">{{ $s->created_at->format('H:i') }}</span>
                        </td>
                        <td>{{ $s->sucursal->nombre }}</td>
                        <td><span class="cell-title">{{ $s->grupo?->nombre ?? $s->item?->grupo ?? '—' }}</span></td>
                        <td>{{ $s->color?->nombre ?? $s->item?->color ?? '—' }}</td>
                        <td><strong>{{ $s->talle?->nombre ?? $s->item?->talle ?? '—' }}</strong></td>
                        <td><span class="badge badge-{{ strtolower($s->estado) }}">{{ $s->estado }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
