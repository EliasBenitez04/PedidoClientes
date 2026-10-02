@extends('layouts.app')

@section('title', 'Importar catálogo')

@section('content')
<div class="top">
    <div><h1>Importar grupo, color y talle</h1><div class="muted">Carga masiva del catálogo disponible para los locales.</div></div>
</div>

<div class="grid grid-2">
    <div class="card">
        <h2>Importar CSV</h2>
        <p class="muted">Acepta archivos separados por coma, punto y coma o tabulación. La primera fila debe incluir <strong>grupo</strong>, <strong>color</strong> y <strong>talle</strong>.</p>
        <form action="{{ route('catalogo.importar.post') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="field">
                <label>Archivo</label>
                <input type="file" name="archivo" accept=".csv,.txt,text/csv,text/plain" required>
            </div>
            <button class="btn btn-primary" type="submit">Importar catálogo</button>
        </form>
    </div>
    <div class="card">
        <h2>Estado del catálogo</h2>
        <div class="grid grid-2">
            <div class="kpi"><div class="label">Registros totales</div><div class="value">{{ $total }}</div></div>
            <div class="kpi"><div class="label">Activos</div><div class="value">{{ $activos }}</div></div>
        </div>
        <p class="muted" style="margin-top:18px">Ejemplo:</p>
        <pre style="background:#f8fafc;padding:12px;border-radius:8px;overflow:auto">grupo;color;talle
REMERA ECO;NEGRO;M
REMERA ECO;NEGRO;L
REMERA ECO;BLANCO;M</pre>
    </div>
</div>
@endsection
