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
                        <th>ID / Fecha</th>
                        <th>Local</th>
                        <th>Registrado por</th>
                        <th>Grupo</th>
                        <th>Color</th>
                        <th>Talle</th>
                        <th>Observación</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($solicitudes as $s)
                    <tr>
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

                        <td>
                            <span class="badge badge-{{ strtolower($s->estado) }}">{{ $s->estado }}</span>

                            @if(in_array(auth()->user()->rol, ['ADMIN','SUPERVISOR']))
                                <form action="{{ route('solicitudes.estado', $s) }}" method="POST" style="margin-top:7px">
                                    @csrf
                                    @method('PATCH')

                                    <select
                                        name="estado"
                                        class="state-select js-state-select"
                                        data-current="{{ $s->estado }}"
                                    >
                                        @foreach(['REGISTRADO','REVISADO','DESCARTADO'] as $estado)
                                            <option value="{{ $estado }}" @selected($s->estado === $estado)>
                                                {{ $estado }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
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
