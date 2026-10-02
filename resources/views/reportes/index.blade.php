@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="top">
    <div><h1>Reportes</h1><div class="muted">Consulta quién pidió, desde qué local y qué variante solicitó el cliente.</div></div>
    <a class="btn btn-primary" href="{{ route('reportes.exportar', request()->query()) }}">Exportar CSV</a>
</div>

<div class="card">
    <form method="GET" action="{{ route('reportes.index') }}">
        <div class="grid grid-4">
            <div class="field"><label>Desde</label><input type="date" name="desde" value="{{ request('desde') }}"></div>
            <div class="field"><label>Hasta</label><input type="date" name="hasta" value="{{ request('hasta') }}"></div>
            @if(auth()->user()->puedeVerTodo())
                <div class="field"><label>Sucursal</label><select name="sucursal_id"><option value="">Todas</option>@foreach($sucursales as $s)<option value="{{ $s->id }}" @selected((string)request('sucursal_id') === (string)$s->id)>{{ $s->nombre }}</option>@endforeach</select></div>
                <div class="field"><label>Usuario</label><select name="usuario_id"><option value="">Todos</option>@foreach($usuarios as $u)<option value="{{ $u->id }}" @selected((string)request('usuario_id') === (string)$u->id)>{{ $u->name }}</option>@endforeach</select></div>
            @endif
            <div class="field"><label>Grupo</label><select name="grupo"><option value="">Todos</option>@foreach($grupos as $g)<option value="{{ $g }}" @selected(request('grupo') === $g)>{{ $g }}</option>@endforeach</select></div>
            <div class="field"><label>Color</label><input name="color" value="{{ request('color') }}" placeholder="Ej.: NEGRO"></div>
            <div class="field"><label>Talle</label><input name="talle" value="{{ request('talle') }}" placeholder="Ej.: M"></div>
            <div class="field"><label>Estado</label><select name="estado"><option value="">Todos</option>@foreach(['PENDIENTE','PROCESADO','CANCELADO'] as $e)<option value="{{ $e }}" @selected(request('estado') === $e)>{{ $e }}</option>@endforeach</select></div>
        </div>
        <div class="actions"><button class="btn btn-primary">Filtrar</button><a class="btn" href="{{ route('reportes.index') }}">Limpiar</a></div>
    </form>
</div>

<div class="card">
@if($pedidos->isEmpty())
    <div class="empty">No hay resultados para los filtros seleccionados.</div>
@else
<table>
    <thead><tr><th>ID / Fecha</th><th>Sucursal</th><th>Usuario</th><th>Grupo</th><th>Color</th><th>Talle</th><th>Observación</th><th>Estado</th></tr></thead>
    <tbody>
    @foreach($pedidos as $p)
        <tr>
            <td><strong>#{{ $p->id }}</strong><br><span class="muted">{{ $p->created_at->format('d/m/Y H:i:s') }}</span></td>
            <td>{{ $p->sucursal->nombre }}</td>
            <td>{{ $p->usuario->name }}</td>
            <td>{{ $p->item->grupo }}</td>
            <td>{{ $p->item->color }}</td>
            <td><strong>{{ $p->item->talle }}</strong></td>
            <td>{{ $p->observacion ?: '—' }}</td>
            <td><span class="badge badge-{{ strtolower($p->estado) }}">{{ $p->estado }}</span></td>
        </tr>
    @endforeach
    </tbody>
</table>
<div class="pagination">
    @if($pedidos->previousPageUrl())<a class="btn" href="{{ $pedidos->previousPageUrl() }}">← Anterior</a>@endif
    <span class="btn">Página {{ $pedidos->currentPage() }} de {{ $pedidos->lastPage() }}</span>
    @if($pedidos->nextPageUrl())<a class="btn" href="{{ $pedidos->nextPageUrl() }}">Siguiente →</a>@endif
</div>
@endif
</div>
@endsection
