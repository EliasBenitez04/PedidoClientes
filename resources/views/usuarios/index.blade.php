@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="page-heading">
    <div>
        <h1>Usuarios y roles</h1>
        <p class="muted">Cada tienda puede iniciar sesión con un código simple como TIENDA01.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h2>Nuevo usuario</h2>
            <div class="card-subtitle">El correo es opcional. El acceso se realiza con el campo Usuario.</div>
        </div>
    </div>

    <form action="{{ route('usuarios.store') }}" method="POST">
        @csrf
        <div class="grid grid-3">
            <div class="field">
                <label>Nombre descriptivo</label>
                <input name="name" value="{{ old('name') }}" placeholder="Ej.: Tienda Shopping Pinedo" required>
            </div>

            <div class="field">
                <label>Usuario</label>
                <input name="username" value="{{ old('username') }}" class="js-username" placeholder="Ej.: TIENDA01" maxlength="80" required>
                <small class="field-hint">Solo letras, números y guion bajo. Se guarda en mayúsculas.</small>
            </div>

            <div class="field">
                <label>Contraseña</label>
                <input type="password" name="password" minlength="8" placeholder="Ej.: TIENDA01" required>
            </div>

            <div class="field">
                <label>Correo <span class="muted">(opcional)</span></label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="correo@empresa.com">
            </div>

            <div class="field">
                <label>Rol</label>
                <select name="rol" class="select2-no-search" data-placeholder="Seleccionar rol" required>
                    <option value=""></option>
                    @foreach(['LOCAL','SUPERVISOR','ADMIN'] as $rol)
                        <option value="{{ $rol }}" @selected(old('rol') === $rol)>{{ $rol }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Sucursal</label>
                <select name="sucursal_id" class="select2" data-placeholder="Buscar sucursal..." required>
                    <option value=""></option>
                    @foreach($sucursales as $s)
                        <option value="{{ $s->id }}" @selected((string) old('sucursal_id') === (string) $s->id)>{{ $s->codigo }} - {{ $s->nombre }}</option>
                    @endforeach
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
            <thead>
                <tr><th>Usuario</th><th>Rol</th><th>Sucursal</th><th>Estado</th><th>Editar</th></tr>
            </thead>
            <tbody>
            @foreach($usuarios as $u)
                <tr>
                    <td>
                        <span class="cell-title">{{ $u->username }}</span>
                        <span class="cell-meta">{{ $u->name }}</span>
                        @if($u->email)<span class="cell-meta">{{ $u->email }}</span>@endif
                    </td>
                    <td>{{ $u->rol }}</td>
                    <td>{{ $u->sucursal?->nombre ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $u->activo ? 'badge-procesado' : 'badge-cancelado' }}">
                            {{ $u->activo ? 'ACTIVO' : 'INACTIVO' }}
                        </span>
                    </td>
                    <td style="min-width:560px">
                        <form action="{{ route('usuarios.update', $u) }}" method="POST">
                            @csrf @method('PUT')

                            <div class="grid grid-3">
                                <input name="name" value="{{ $u->name }}" required>
                                <input name="username" value="{{ $u->username }}" class="js-username" maxlength="80" required>
                                <input type="password" name="password" placeholder="Nueva clave (opcional)">

                                <input type="email" name="email" value="{{ $u->email }}" placeholder="Correo opcional">

                                <select name="rol" class="select2-no-search" data-allow-clear="false">
                                    @foreach(['LOCAL','SUPERVISOR','ADMIN'] as $rol)
                                        <option value="{{ $rol }}" @selected($u->rol === $rol)>{{ $rol }}</option>
                                    @endforeach
                                </select>

                                <select name="sucursal_id" class="select2" data-allow-clear="false">
                                    @foreach($sucursales as $s)
                                        <option value="{{ $s->id }}" @selected($u->sucursal_id === $s->id)>{{ $s->nombre }}</option>
                                    @endforeach
                                </select>

                                <label style="font-weight:500">
                                    <input type="checkbox" name="activo" value="1" style="width:auto;min-height:auto" @checked($u->activo)>
                                    Activo
                                </label>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-username').forEach(function (input) {
        input.addEventListener('input', function () {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9_]/g, '');
        });
    });
});
</script>
@endpush
