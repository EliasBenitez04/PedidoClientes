@extends('layouts.app')

@section('title', 'Pedidos')

@section('content')
<div class="page-heading">
    <div>
        <h1>Pedidos</h1>
        <p class="muted">Historial completo de solicitudes registradas.</p>
    </div>
    <div class="page-actions">
        <a class="btn btn-primary" href="{{ route('pedidos.create') }}">+ Nuevo pedido</a>
    </div>
</div>

<div class="card table-card">
    <div class="table-toolbar">
        <div>
            <h2>Registro de pedidos</h2>
            <div class="card-subtitle">{{ $pedidos->total() }} resultado{{ $pedidos->total() === 1 ? '' : 's' }}</div>
        </div>
    </div>

    @if($pedidos->isEmpty())
        <div class="empty">No hay pedidos registrados.</div>
    @else
        <div class="table-scroll">
            <table>
                <thead>
                    <tr><th>ID / Fecha</th><th>Local</th><th>Usuario</th><th>Grupo</th><th>Color</th><th>Talle</th><th>Observación</th><th>Estado</th></tr>
                </thead>
                <tbody>
                @foreach($pedidos as $p)
                    <tr>
                        <td><span class="cell-title">#{{ $p->id }}</span><span class="cell-meta">{{ $p->created_at->format('d/m/Y H:i') }}</span></td>
                        <td><span class="cell-title">{{ $p->sucursal->nombre }}</span></td>
                        <td>{{ $p->usuario->name }}</td>
                        <td><span class="cell-title">{{ $p->item->grupo }}</span></td>
                        <td>{{ $p->item->color }}</td>
                        <td><strong>{{ $p->item->talle }}</strong></td>
                        <td class="observation-cell">{{ $p->observacion ?: 'Sin observación' }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($p->estado) }}">{{ $p->estado }}</span>
                            @if(in_array(auth()->user()->rol, ['ADMIN','SUPERVISOR']))
                                <form action="{{ route('pedidos.estado', $p) }}" method="POST" style="margin-top:7px">
                                    @csrf @method('PATCH')
                                    <select name="estado" onchange="this.form.submit()" class="state-select">
                                        @foreach(['PENDIENTE','PROCESADO','CANCELADO'] as $estado)
                                            <option value="{{ $estado }}" @selected($p->estado === $estado)>{{ $estado }}</option>
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
            @if($pedidos->previousPageUrl())<a class="btn btn-sm" href="{{ $pedidos->previousPageUrl() }}">← Anterior</a>@endif
            <span class="page-info">Página {{ $pedidos->currentPage() }} de {{ $pedidos->lastPage() }}</span>
            @if($pedidos->nextPageUrl())<a class="btn btn-sm" href="{{ $pedidos->nextPageUrl() }}">Siguiente →</a>@endif
        </div>
    @endif
</div>
@endsection
