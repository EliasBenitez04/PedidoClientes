@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="top"><div><h1>Usuarios y roles</h1><div class="muted">Cada usuario queda vinculado a una sucursal.</div></div></div>

<div class="card">
    <h2>Nuevo usuario</h2>
    <form action="{{ route('usuarios.store') }}" method="POST">
        @csrf
        <div class="grid grid-3">
            <div class="field"><label>Nombre</label><input name="name" required></div>
            <div class="field"><label>Correo</label><input type="email" name="email" required></div>
            <div class="field"><label>Contraseña</label><input type="password" name="password" minlength="8" required></div>
            <div class="field">
                <label>Rol</label>
                <select name="rol" required>
                    <option value="LOCAL">LOCAL</option>
                    <option value="SUPERVISOR">SUPERVISOR</option>
                    <option value="ADMIN">ADMIN</option>
                </select>
            </div>
            <div class="field">
                <label>Sucursal</label>
                <select name="sucursal_id" required>
                    <option value="">Seleccionar...</option>
                    @foreach($sucursales as $s)<option value="{{ $s->id }}">{{ $s->codigo }} - {{ $s->nombre }}</option>@endforeach
                </select>
            </div>
        </div>
        <button class="btn btn-primary">Crear usuario</button>
    </form>
</div>

<div class="card">
    <h2>Usuarios existentes</h2>
    <table>
        <thead><tr><th>Usuario</th><th>Rol</th><th>Sucursal</th><th>Estado</th><th>Editar</th></tr></thead>
        <tbody>
        @foreach($usuarios as $u)
            <tr>
                <td><strong>{{ $u->name }}</strong><br><span class="muted">{{ $u->email }}</span></td>
                <td>{{ $u->rol }}</td>
                <td>{{ $u->sucursal?->nombre ?? '—' }}</td>
                <td>{{ $u->activo ? 'ACTIVO' : 'INACTIVO' }}</td>
                <td style="min-width:420px">
                    <form action="{{ route('usuarios.update', $u) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="grid grid-3">
                            <input name="name" value="{{ $u->name }}" required>
                            <input type="email" name="email" value="{{ $u->email }}" required>
                            <input type="password" name="password" placeholder="Nueva clave (opcional)">
                            <select name="rol">
                                @foreach(['LOCAL','SUPERVISOR','ADMIN'] as $rol)<option value="{{ $rol }}" @selected($u->rol === $rol)>{{ $rol }}</option>@endforeach
                            </select>
                            <select name="sucursal_id">
                                @foreach($sucursales as $s)<option value="{{ $s->id }}" @selected($u->sucursal_id === $s->id)>{{ $s->nombre }}</option>@endforeach
                            </select>
                            <label style="font-weight:500"><input type="checkbox" name="activo" value="1" style="width:auto" @checked($u->activo)> Activo</label>
                        </div>
                        <button class="btn btn-sm">Guardar cambios</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
