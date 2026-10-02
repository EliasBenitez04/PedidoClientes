@extends('layouts.app')

@section('title', 'Historial de demanda')

@section('content')
<div class="page-heading">
    <div>
        <h1>Demanda registrada</h1>
        <p class="muted">Consultas de clientes que no pudieron cubrirse con el stock disponible del local.</p>
    </div>

    <div class="page-actions">
        <a class="btn btn-primary" href="{{ route('solicitudes.create') }}">+ Registrar demanda</a>
    </div>
</div>

@if(in_array(auth()->user()->rol, ['ADMIN','SUPERVISOR']) && !$solicitudes->isEmpty())
    <div class="review-toolbar">
        <div>
            <strong>Revisión rápida</strong>
            <span>Seleccioná varios registros y revisalos en una sola acción.</span>
        </div>

        <div class="review-toolbar-actions">
            <span id="bulkSelectedCount" class="review-selected-count"></span>

            <form
                id="bulkReviewForm"
                action="{{ route('solicitudes.revisar-masivo') }}"
                method="POST"
                class="js-bulk-review-form"
            >
                @csrf
                @method('PATCH')

                <button id="bulkReviewButton" class="btn btn-success" type="submit" disabled>
                    ✓ Marcar seleccionados como revisados
                </button>
            </form>
        </div>
    </div>
@endif

<div class="card table-card">
    <div class="table-toolbar">
        <div>
            <h2>Historial de consultas</h2>
            <div class="card-subtitle">
                {{ $solicitudes->total() }} registro{{ $solicitudes->total() === 1 ? '' : 's' }}
            </div>
        </div>
    </div>

    @if($solicitudes->isEmpty())
        <div class="empty">Todavía no hay demanda no cubierta registrada.</div>
    @else
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        @if(in_array(auth()->user()->rol, ['ADMIN','SUPERVISOR']))
                            <th class="checkbox-cell">
                                <input type="checkbox" id="selectAllRows" class="table-checkbox" title="Seleccionar todos los registrados">
                            </th>
                        @endif

                        <th>ID / Fecha</th>
                        <th>Local</th>
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
                    <tr class="{{ $s->estado === 'REVISADO' ? 'row-reviewed' : '' }}">
                        @if(in_array(auth()->user()->rol, ['ADMIN','SUPERVISOR']))
                            <td class="checkbox-cell">
                                @if($s->estado === 'REGISTRADO')
                                    <input
                                        type="checkbox"
                                        name="ids[]"
                                        value="{{ $s->id }}"
                                        class="table-checkbox js-row-check"
                                        form="bulkReviewForm"
                                        aria-label="Seleccionar registro {{ $s->id }}"
                                    >
                                @endif
                            </td>
                        @endif

                        <td>
                            <span class="cell-title">#{{ $s->id }}</span>
                            <span class="cell-meta">{{ $s->created_at->format('d/m/Y H:i') }}</span>
                        </td>

                        <td><span class="cell-title">{{ $s->sucursal->nombre }}</span></td>
                        <td>{{ $s->usuario->name }}</td>
                        <td><span class="cell-title">{{ $s->grupo?->nombre ?? $s->item?->grupo ?? '—' }}</span></td>
                        <td>{{ $s->color?->nombre ?? $s->item?->color ?? '—' }}</td>
                        <td><strong>{{ $s->talle?->nombre ?? $s->item?->talle ?? '—' }}</strong></td>
                        <td class="observation-cell">{{ $s->observacion ?: 'Sin observación' }}</td>

                        <td class="review-cell">
                            <span class="badge badge-{{ strtolower($s->estado) }}">{{ $s->estado }}</span>

                            @if($s->estado === 'REVISADO')
                                <div class="review-audit">
                                    <strong>{{ $s->revisor?->name ?? 'Usuario no disponible' }}</strong>
                                    <span>{{ $s->revisado_en?->format('d/m/Y H:i') ?? 'Sin fecha registrada' }}</span>
                                </div>
                            @endif

                            @if(in_array(auth()->user()->rol, ['ADMIN','SUPERVISOR']))
                                <div class="review-actions">
                                    @if($s->estado === 'REGISTRADO')
                                        <form action="{{ route('solicitudes.revisar', $s) }}" method="POST">
                                            @csrf
                                            @method('PATCH')

                                            <button class="btn btn-success btn-sm" type="submit">
                                                ✓ Revisar
                                            </button>
                                        </form>

                                        <form action="{{ route('solicitudes.estado', $s) }}" method="POST" class="js-discard-form">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="estado" value="DESCARTADO">

                                            <button class="btn btn-danger btn-sm" type="submit">
                                                Descartar
                                            </button>
                                        </form>
                                    @elseif($s->estado === 'REVISADO')
                                        <form action="{{ route('solicitudes.estado', $s) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="estado" value="REGISTRADO">

                                            <button class="btn btn-sm" type="submit">
                                                Reabrir
                                            </button>
                                        </form>
                                    @elseif($s->estado === 'DESCARTADO')
                                        <form action="{{ route('solicitudes.estado', $s) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="estado" value="REGISTRADO">

                                            <button class="btn btn-sm" type="submit">
                                                Recuperar
                                            </button>
                                        </form>
                                    @endif
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
