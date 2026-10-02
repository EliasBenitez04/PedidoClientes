@extends('layouts.app')

@section('title', 'Importar catálogos')

@section('content')
<div class="page-heading">
    <div>
        <h1>Importar catálogos</h1>
        <p class="muted">Grupo, color y talle se administran de manera independiente.</p>
    </div>
</div>

<div class="identity" style="margin-bottom:20px">
    <strong>Nuevo funcionamiento:</strong> ya no necesitás un archivo con combinaciones de grupo + color + talle.
    Importá cada catálogo por separado y luego el usuario podrá elegir cualquier valor activo de cada lista.
</div>

<div class="grid grid-3">
    <div class="card card-elevated">
        <div class="card-header">
            <div>
                <h2>Grupos</h2>
                <div class="card-subtitle">{{ $stats['grupos'] }} grupos activos</div>
            </div>
            <div class="kpi-icon" style="--kpi-color:#4338ca;--kpi-soft:#eef2ff">G</div>
        </div>

        <form action="{{ route('catalogo.importar.post') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="tipo" value="grupo">
            <div class="field">
                <label>Archivo de grupos</label>
                <input type="file" name="archivo" accept=".csv,.txt,text/csv,text/plain" required>
                <small class="field-hint">Una columna llamada <strong>grupo</strong>. También admite un archivo de una sola columna sin cabecera.</small>
            </div>
            <button class="btn btn-primary" type="submit">Importar grupos</button>
        </form>

        <pre style="margin:18px 0 0;background:#111827;color:#e5e7eb;padding:13px;border-radius:10px;overflow:auto;font-size:11px">grupo
REMERA ECO
PANTALON
CAMPERA</pre>
    </div>

    <div class="card card-elevated">
        <div class="card-header">
            <div>
                <h2>Colores</h2>
                <div class="card-subtitle">{{ $stats['colores'] }} colores activos</div>
            </div>
            <div class="kpi-icon" style="--kpi-color:#175cd3;--kpi-soft:#eff8ff">C</div>
        </div>

        <form action="{{ route('catalogo.importar.post') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="tipo" value="color">
            <div class="field">
                <label>Archivo de colores</label>
                <input type="file" name="archivo" accept=".csv,.txt,text/csv,text/plain" required>
                <small class="field-hint">Una columna llamada <strong>color</strong>. También admite un archivo de una sola columna sin cabecera.</small>
            </div>
            <button class="btn btn-primary" type="submit">Importar colores</button>
        </form>

        <pre style="margin:18px 0 0;background:#111827;color:#e5e7eb;padding:13px;border-radius:10px;overflow:auto;font-size:11px">color
NEGRO
BLANCO
AZUL</pre>
    </div>

    <div class="card card-elevated">
        <div class="card-header">
            <div>
                <h2>Talles</h2>
                <div class="card-subtitle">{{ $stats['talles'] }} talles activos</div>
            </div>
            <div class="kpi-icon" style="--kpi-color:#067647;--kpi-soft:#ecfdf3">T</div>
        </div>

        <form action="{{ route('catalogo.importar.post') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="tipo" value="talle">
            <div class="field">
                <label>Archivo de talles</label>
                <input type="file" name="archivo" accept=".csv,.txt,text/csv,text/plain" required>
                <small class="field-hint">Una columna llamada <strong>talle</strong>. También admite un archivo de una sola columna sin cabecera.</small>
            </div>
            <button class="btn btn-primary" type="submit">Importar talles</button>
        </form>

        <pre style="margin:18px 0 0;background:#111827;color:#e5e7eb;padding:13px;border-radius:10px;overflow:auto;font-size:11px">talle
S
M
L
XL</pre>
    </div>
</div>
@endsection
