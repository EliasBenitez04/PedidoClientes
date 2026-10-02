@extends('layouts.app')

@section('title', 'Sucursales')

@section('content')
<div class="top"><div><h1>Sucursales</h1><div class="muted">Locales que originan los pedidos.</div></div></div>

<div class="card">
    <h2>Nueva sucursal</h2>
    <form action="{{ route('sucursales.store') }}" method="POST">
        @csrf
        <div class="grid grid-3">
            <div class="field"><label>Código</label><input name="codigo" maxlength="30" required></div>
            <div class="field"><label>Nombre</label><input name="nombre" maxlength="120" required></div>
        </div>
        <button class="btn btn-primary">Crear sucursal</button>
    </form>
</div>

<div class="card">
    <h2>Sucursales registradas</h2>
    <table>
        <thead><tr><th>Código</th><th>Nombre</th><th>Estado</th><th>Editar</th></tr></thead>
        <tbody>
        @foreach($sucursales as $s)
            <tr>
                <td>{{ $s->codigo }}</td><td>{{ $s->nombre }}</td><td>{{ $s->activo ? 'ACTIVA' : 'INACTIVA' }}</td>
                <td>
                    <form action="{{ route('sucursales.update', $s) }}" method="POST" class="actions">
                        @csrf @method('PUT')
                        <input name="codigo" value="{{ $s->codigo }}" required style="max-width:120px">
                        <input name="nombre" value="{{ $s->nombre }}" required style="max-width:260px">
                        <label style="font-weight:500"><input type="checkbox" name="activo" value="1" style="width:auto" @checked($s->activo)> Activa</label>
                        <button class="btn btn-sm">Guardar</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
