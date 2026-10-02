@extends('layouts.app')

@section('title', 'Pedidos')

@section('content')
<div class="top">
    <div><h1>Pedidos</h1><div class="muted">Historial de solicitudes registradas.</div></div>
    <a class="btn btn-primary" href="{{ route('pedidos.create') }}">+ Nuevo pedido</a>
</div>

<div class="card">
@if($pedidos->isEmpty())
    <div class="empty">No hay pedidos.</div>
@else
<table>
    <thead><tr><th>ID / Fecha</th><th>Local</th><th>Usuario</th><th>Grupo</th><th>Color</th><th>Talle</th><th>Observación</th><th>Estado</th></tr></thead>
    <tbody>
    @foreach($pedidos as $p)
        <tr>
            <td><strong>#{{ $p->id }}</strong><br><span class="muted">{{ $p->created_at->format('d/m/Y H:i') }}</span></td>
            <td>{{ $p->sucursal->nombre }}</td>
            <td>{{ $p->usuario->name }}</td>
            <td>{{ $p->item->grupo }}</td>
            <td>{{ $p->item->color }}</td>
            <td><strong>{{ $p->item->talle }}</strong></td>
            <td>{{ $p->observacion ?: '—' }}</td>
            <td>
                <span class="badge badge-{{ strtolower($p->estado) }}">{{ $p->estado }}</span>
                @if(in_array(auth()->user()->rol, ['ADMIN','SUPERVISOR']))
                    <form action="{{ route('pedidos.estado', $p) }}" method="POST" style="margin-top:7px">
                        @csrf @method('PATCH')
                        <select name="estado" onchange="this.form.submit()" style="padding:6px;width:auto">
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
<div class="pagination">
    @if($pedidos->previousPageUrl())<a class="btn" href="{{ $pedidos->previousPageUrl() }}">← Anterior</a>@endif
    <span class="btn">Página {{ $pedidos->currentPage() }} de {{ $pedidos->lastPage() }}</span>
    @if($pedidos->nextPageUrl())<a class="btn" href="{{ $pedidos->nextPageUrl() }}">Siguiente →</a>@endif
</div>
@endif
</div>
@endsection
