@extends('layouts.app')

@section('title', 'Importar catálogo')

@section('content')
<div class="page-heading">
    <div>
        <h1>Importar catálogo</h1>
        <p class="muted">Actualizá las combinaciones disponibles de grupo, color y talle.</p>
    </div>
</div>

<div class="grid grid-2">
    <div class="card card-elevated">
        <div class="card-header">
            <div>
                <h2>Cargar archivo</h2>
                <div class="card-subtitle">CSV o TXT de hasta 10 MB</div>
            </div>
        </div>

        <div class="identity" style="margin-bottom:18px">
            La primera fila debe contener las columnas <strong>grupo</strong>, <strong>color</strong> y <strong>talle</strong>.
        </div>

        <form action="{{ route('catalogo.importar.post') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="field">
                <label>Archivo de catálogo</label>
                <input type="file" name="archivo" accept=".csv,.txt,text/csv,text/plain" required>
                <small class="field-hint">Se acepta coma, punto y coma o tabulación como separador.</small>
            </div>
            <button class="btn btn-primary btn-lg" type="submit">Importar catálogo</button>
        </form>
    </div>

    <div>
        <div class="grid grid-2">
            <div class="kpi-card kpi-primary">
                <div class="kpi-top"><span class="kpi-label">Registros totales</span><span class="kpi-icon">T</span></div>
                <div class="kpi-value">{{ $total }}</div>
            </div>
            <div class="kpi-card kpi-success">
                <div class="kpi-top"><span class="kpi-label">Activos</span><span class="kpi-icon">✓</span></div>
                <div class="kpi-value">{{ $activos }}</div>
            </div>
        </div>

        <div class="card" style="margin-top:16px">
            <div class="card-header">
                <div>
                    <h2>Formato esperado</h2>
                    <div class="card-subtitle">Ejemplo de tres combinaciones válidas</div>
                </div>
            </div>
            <pre style="margin:0;background:#111827;color:#e5e7eb;padding:16px;border-radius:11px;overflow:auto;font-size:12px">grupo;color;talle
REMERA ECO;NEGRO;M
REMERA ECO;NEGRO;L
REMERA ECO;BLANCO;M</pre>
        </div>
    </div>
</div>
@endsection
