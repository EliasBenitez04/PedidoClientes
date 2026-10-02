@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="page-heading">
    <div>
        <h1>Usuarios y roles</h1>
        <p class="muted">Cada usuario queda vinculado a una sucursal.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h2>Nuevo usuario</h2>
            <div class="card-subtitle">Asigná rol y sucursal desde el inicio.</div>
        </div>
    </div>

    <form action="{{ route('usuarios.store') }}" method="POST">
        @csrf
        <div class="grid grid-3">
            <div class="field"><label>Nombre</label><input name="name" required></div>
            <div class="field"><label>Correo</label><input type="email" name="email" required></div>
            <div class="field"><label>Contraseña</label><input type="password" name="password" minlength="8" required></div>
            <div class="field">
                <label>Rol</label>
                <select name="rol" class="select2-no-search" data-placeholder="Seleccionar rol" required>
                    <option value=""></option>
                    <option value="LOCAL">LOCAL</option>
                    <option value="SUPERVISOR">SUPERVISOR</option>
                    <option value="ADMIN">ADMIN</option>
                </select>
            </div>
            <div class="field">
                <label>Sucursal</label>
                <select name="sucursal_id" class="select2" data-placeholder="Buscar sucursal..." required>
                    <option value=""></option>
                    @foreach($sucursales as $s)<option value="{{ $s->id }}">{{ $s->codigo }} - {{ $s->nombre }}</option>@endforeach
                </select>
            </div>
        </div>
        <button class="btn btn-primary">Crear usuario</button>
    </form>
</div>

<div class="card table-card">
    <div class="table-toolbar">
        <div>
            <h2>Usuarios existentes</h2>
            <div class="card-subtitle">{{ $usuarios->count() }} usuario{{ $usuarios->count() === 1 ? '' : 's' }}</div>
        </div>
    </div>

    <div class="table-scroll">
        <table>
            <thead><tr><th>Usuario</th><th>Rol</th><th>Sucursal</th><th>Estado</th><th>Editar</th></tr></thead>
            <tbody>
            @foreach($usuarios as $u)
                <tr>
                    <td><span class="cell-title">{{ $u->name }}</span><span class="cell-meta">{{ $u->email }}</span></td>
                    <td>{{ $u->rol }}</td>
                    <td>{{ $u->sucursal?->nombre ?? '—' }}</td>
                    <td><span class="badge {{ $u->activo ? 'badge-procesado' : 'badge-cancelado' }}">{{ $u->activo ? 'ACTIVO' : 'INACTIVO' }}</span></td>
                    <td style="min-width:460px">
                        <form action="{{ route('usuarios.update', $u) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="grid grid-3">
                                <input name="name" value="{{ $u->name }}" required>
                                <input type="email" name="email" value="{{ $u->email }}" required>
                                <input type="password" name="password" placeholder="Nueva clave (opcional)">
                                <select name="rol" class="select2-no-search" data-allow-clear="false">
                                    @foreach(['LOCAL','SUPERVISOR','ADMIN'] as $rol)<option value="{{ $rol }}" @selected($u->rol === $rol)>{{ $rol }}</option>@endforeach
                                </select>
                                <select name="sucursal_id" class="select2" data-allow-clear="false">
                                    @foreach($sucursales as $s)<option value="{{ $s->id }}" @selected($u->sucursal_id === $s->id)>{{ $s->nombre }}</option>@endforeach
                                </select>
                                <label style="font-weight:500"><input type="checkbox" name="activo" value="1" style="width:auto;min-height:auto" @checked($u->activo)> Activo</label>
                            </div>
                            <button class="btn btn-sm">Guardar cambios</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
