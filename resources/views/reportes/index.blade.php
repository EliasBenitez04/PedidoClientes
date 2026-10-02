@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="page-heading">
    <div>
        <h1>Reportes de demanda</h1>
        <p class="muted">Analizá qué solicitan los clientes y no está disponible en los locales.</p>
    </div>

    <div class="page-actions">
        <a class="btn btn-primary" href="{{ route('reportes.exportar', request()->query()) }}">
            Exportar CSV
        </a>
    </div>
</div>

<div class="card filter-card">
    <div class="card-header">
        <div>
            <h2>Filtros</h2>
            <div class="card-subtitle">Segmentá la demanda por fecha, local y características del producto.</div>
        </div>
    </div>

    <form method="GET" action="{{ route('reportes.index') }}">
        <div class="grid grid-4">
            <div class="field">
                <label>Desde</label>
                <input type="date" name="desde" value="{{ request('desde') }}">
            </div>

            <div class="field">
                <label>Hasta</label>
                <input type="date" name="hasta" value="{{ request('hasta') }}">
            </div>

            @if(auth()->user()->puedeVerTodo())
                <div class="field">
                    <label>Sucursal</label>
                    <select name="sucursal_id" class="select2" data-placeholder="Todas las sucursales">
                        <option value=""></option>
                        @foreach($sucursales as $s)
                            <option value="{{ $s->id }}" @selected((string)request('sucursal_id') === (string)$s->id)>
                                {{ $s->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>Registrado por</label>
                    <select name="usuario_id" class="select2" data-placeholder="Todos los usuarios">
                        <option value=""></option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->id }}" @selected((string)request('usuario_id') === (string)$u->id)>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="field">
                <label>Grupo</label>
                <select name="grupo" class="select2" data-placeholder="Todos los grupos">
                    <option value=""></option>
                    <option value="__SIN_GRUPO__" @selected(request('grupo') === '__SIN_GRUPO__')>NO CATALOGADO</option>
                    @foreach($grupos as $g)
                        <option value="{{ $g }}" @selected(request('grupo') === $g)>{{ $g }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Color</label>
                <select name="color" class="select2" data-placeholder="Todos los colores">
                    <option value=""></option>
                    @foreach($colores as $c)
                        <option value="{{ $c }}" @selected(request('color') === $c)>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Talle</label>
                <select name="talle" class="select2" data-placeholder="Todos los talles">
                    <option value=""></option>
                    @foreach($talles as $t)
                        <option value="{{ $t }}" @selected(request('talle') === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Estado</label>
                <select name="estado" class="select2-no-search" data-placeholder="Todos los estados">
                    <option value=""></option>
                    @foreach(['REGISTRADO','REVISADO','DESCARTADO'] as $e)
                        <option value="{{ $e }}" @selected(request('estado') === $e)>{{ $e }}</option>
                    @endforeach
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
            <h2>Demanda encontrada</h2>
            <div class="card-subtitle">
                {{ $solicitudes->total() }} consulta{{ $solicitudes->total() === 1 ? '' : 's' }}
            </div>
        </div>
    </div>

    @if($solicitudes->isEmpty())
        <div class="empty">No hay registros para los filtros seleccionados.</div>
    @else
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>ID / Fecha</th>
                        <th>Sucursal</th>
                        <th>Registrado por</th>
                        <th>Grupo</th>
                        <th>Color</th>
                        <th>Talle</th>
                        <th>Observación</th>
                        <th>Estado / Revisión</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($solicitudes as $s)
                    <tr>
                        <td>
                            <span class="cell-title">#{{ $s->id }}</span>
                            <span class="cell-meta">{{ $s->created_at->format('d/m/Y H:i:s') }}</span>
                        </td>

                        <td><span class="cell-title">{{ $s->sucursal->nombre }}</span></td>
                        <td>{{ $s->usuario->name }}</td>
                        <td>{{ $s->grupo?->nombre ?? $s->item?->grupo ?? 'NO CATALOGADO' }}</td>
                        <td>{{ $s->color?->nombre ?? $s->item?->color ?? '—' }}</td>
                        <td><strong>{{ $s->talle?->nombre ?? $s->item?->talle ?? '—' }}</strong></td>
                        <td class="observation-cell">{{ $s->observacion ?: 'Sin observación' }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($s->estado) }}">{{ $s->estado }}</span>

                            @if($s->estado === 'REVISADO')
                                <div class="review-audit">
                                    <strong>{{ $s->revisor?->name ?? 'Usuario no disponible' }}</strong>
                                    <span>{{ $s->revisado_en?->format('d/m/Y H:i') ?? 'Sin fecha registrada' }}</span>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination">
            @if($solicitudes->previousPageUrl())
                <a class="btn btn-sm" href="{{ $solicitudes->previousPageUrl() }}">← Anterior</a>
            @endif

            <span class="page-info">
                Página {{ $solicitudes->currentPage() }} de {{ $solicitudes->lastPage() }}
            </span>

            @if($solicitudes->nextPageUrl())
                <a class="btn btn-sm" href="{{ $solicitudes->nextPageUrl() }}">Siguiente →</a>
            @endif
        </div>
    @endif
</div>
@endsection
