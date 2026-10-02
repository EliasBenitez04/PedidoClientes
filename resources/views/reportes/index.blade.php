@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="page-heading">
    <div>
        <h1>Reportes</h1>
        <p class="muted">Filtrá y auditá quién pidió, desde qué local y qué variante fue solicitada.</p>
    </div>
    <div class="page-actions">
        <a class="btn btn-primary" href="{{ route('reportes.exportar', request()->query()) }}">Exportar CSV</a>
    </div>
</div>

<div class="card filter-card">
    <div class="card-header">
        <div>
            <h2>Filtros del reporte</h2>
            <div class="card-subtitle">Combiná uno o varios criterios.</div>
        </div>
    </div>

    <form method="GET" action="{{ route('reportes.index') }}">
        <div class="grid grid-4">
            <div class="field"><label>Desde</label><input type="date" name="desde" value="{{ request('desde') }}"></div>
            <div class="field"><label>Hasta</label><input type="date" name="hasta" value="{{ request('hasta') }}"></div>

            @if(auth()->user()->puedeVerTodo())
                <div class="field">
                    <label>Sucursal</label>
                    <select name="sucursal_id">
                        <option value="">Todas las sucursales</option>
                        @foreach($sucursales as $s)<option value="{{ $s->id }}" @selected((string)request('sucursal_id') === (string)$s->id)>{{ $s->nombre }}</option>@endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Usuario</label>
                    <select name="usuario_id">
                        <option value="">Todos los usuarios</option>
                        @foreach($usuarios as $u)<option value="{{ $u->id }}" @selected((string)request('usuario_id') === (string)$u->id)>{{ $u->name }}</option>@endforeach
                    </select>
                </div>
            @endif

            <div class="field">
                <label>Grupo</label>
                <select name="grupo">
                    <option value="">Todos los grupos</option>
                    @foreach($grupos as $g)<option value="{{ $g }}" @selected(request('grupo') === $g)>{{ $g }}</option>@endforeach
                </select>
            </div>
            <div class="field"><label>Color</label><input name="color" value="{{ request('color') }}" placeholder="Ej.: NEGRO"></div>
            <div class="field"><label>Talle</label><input name="talle" value="{{ request('talle') }}" placeholder="Ej.: M"></div>
            <div class="field">
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos los estados</option>
                    @foreach(['PENDIENTE','PROCESADO','CANCELADO'] as $e)<option value="{{ $e }}" @selected(request('estado') === $e)>{{ $e }}</option>@endforeach
                </select>
            </div>
        </div>

        <div class="filter-actions">
            <a class="btn" href="{{ route('reportes.index') }}">Limpiar</a>
            <button class="btn btn-primary">Aplicar filtros</button>
        </div>
    </form>
</div>

<div class="card table-card">
    <div class="table-toolbar">
        <div>
            <h2>Resultados</h2>
            <div class="card-subtitle">{{ $pedidos->total() }} pedido{{ $pedidos->total() === 1 ? '' : 's' }} encontrado{{ $pedidos->total() === 1 ? '' : 's' }}</div>
        </div>
    </div>

    @if($pedidos->isEmpty())
        <div class="empty">No hay resultados para los filtros seleccionados.</div>
    @else
        <div class="table-scroll">
            <table>
                <thead>
                    <tr><th>ID / Fecha</th><th>Sucursal</th><th>Usuario</th><th>Grupo</th><th>Color</th><th>Talle</th><th>Observación</th><th>Estado</th></tr>
                </thead>
                <tbody>
                @foreach($pedidos as $p)
                    <tr>
                        <td><span class="cell-title">#{{ $p->id }}</span><span class="cell-meta">{{ $p->created_at->format('d/m/Y H:i:s') }}</span></td>
                        <td><span class="cell-title">{{ $p->sucursal->nombre }}</span></td>
                        <td>{{ $p->usuario->name }}</td>
                        <td>{{ $p->item->grupo }}</td>
                        <td>{{ $p->item->color }}</td>
                        <td><strong>{{ $p->item->talle }}</strong></td>
                        <td class="observation-cell">{{ $p->observacion ?: 'Sin observación' }}</td>
                        <td><span class="badge badge-{{ strtolower($p->estado) }}">{{ $p->estado }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination">
            @if($pedidos->previousPageUrl())<a class="btn btn-sm" href="{{ $pedidos->previousPageUrl() }}">← Anterior</a>@endif
            <span class="page-info">Página {{ $pedidos->currentPage() }} de {{ $pedidos->lastPage() }}</span>
            @if($pedidos->nextPageUrl())<a class="btn btn-sm" href="{{ $pedidos->nextPageUrl() }}">Siguiente →</a>@endif
        </div>
    @endif
</div>
@endsection
