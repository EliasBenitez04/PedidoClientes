@extends('layouts.app')

@section('title', 'Registrar demanda')

@section('content')
<div class="page-heading">
    <div>
        <h1>Registrar demanda no cubierta</h1>
        <p class="muted">Usá esta pantalla cuando un cliente solicite algo que no está disponible en el local.</p>
    </div>

    <div class="page-actions">
        <a class="btn" href="{{ route('solicitudes.index') }}">Ver historial</a>
    </div>
</div>

<div class="order-layout">
    <div class="card form-card card-elevated">
        <div class="form-card-header">
            <h2>¿Qué estaba buscando el cliente?</h2>
            <p>Color y talle son obligatorios. Si el grupo no existe en el catálogo, dejalo vacío y describí el producto en Observación.</p>
        </div>

        <form method="POST" action="{{ route('solicitudes.store') }}" class="form-card-body">
            @csrf

            <div class="form-section">
                <div class="section-title">
                    <div class="step-number">1</div>
                    <div>
                        <strong>Producto solicitado</strong>
                        <span>Seleccioná todo lo que conozcas de la solicitud.</span>
                    </div>
                </div>

                <div class="grid grid-3">
                    <div class="field">
                        <label for="grupo_id">Grupo <span class="muted">(opcional)</span></label>
                        <select
                            name="grupo_id"
                            id="grupo_id"
                            class="select2"
                            data-placeholder="Buscar grupo o dejar vacío..."
                        >
                            <option value=""></option>
                            @foreach($grupos as $grupo)
                                <option value="{{ $grupo->id }}" @selected((string) old('grupo_id') === (string) $grupo->id)>
                                    {{ $grupo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <small class="field-hint">Si no encontrás el grupo, dejalo vacío.</small>
                    </div>

                    <div class="field">
                        <label for="color_id">Color <strong>*</strong></label>
                        <select name="color_id" id="color_id" class="select2" data-placeholder="Buscar color..." required>
                            <option value=""></option>
                            @foreach($colores as $color)
                                <option value="{{ $color->id }}" @selected((string) old('color_id') === (string) $color->id)>
                                    {{ $color->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <small class="field-hint">Obligatorio.</small>
                    </div>

                    <div class="field">
                        <label for="talle_id">Talle <strong>*</strong></label>
                        <select name="talle_id" id="talle_id" class="select2" data-placeholder="Buscar talle..." required>
                            <option value=""></option>
                            @foreach($talles as $talle)
                                <option value="{{ $talle->id }}" @selected((string) old('talle_id') === (string) $talle->id)>
                                    {{ $talle->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <small class="field-hint">Obligatorio.</small>
                    </div>
                </div>

                <div id="grupoNoCatalogadoAviso" class="identity" style="margin-top:4px;display:none">
                    <strong>Grupo no catalogado</strong>
                    <div style="margin-top:4px">
                        No hay problema. Completá Color y Talle y escribí abajo qué producto pidió el cliente.
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="section-title">
                    <div class="step-number">2</div>
                    <div>
                        <strong>¿Qué producto pidió?</strong>
                        <span id="observacionAyuda">La observación es opcional cuando seleccionaste un grupo.</span>
                    </div>
                </div>

                <div class="field" style="margin-bottom:0">
                    <label for="observacion">
                        Observación
                        <span id="observacionRequiredLabel" class="muted">(opcional)</span>
                    </label>
                    <textarea
                        name="observacion"
                        id="observacion"
                        maxlength="2000"
                        placeholder="Ej.: Camisa manga larga de vestir, color blanco, talle M..."
                    >{{ old('observacion') }}</textarea>
                    <small class="field-hint" id="observacionHint">
                        Si el grupo no existe, describí acá claramente qué solicitó el cliente.
                    </small>
                </div>
            </div>

            <div class="form-actions">
                <a class="btn" href="{{ route('solicitudes.index') }}">Cancelar</a>
                <button class="btn btn-primary btn-lg" type="submit">Registrar demanda →</button>
            </div>
        </form>
    </div>

    <aside>
        <div class="card info-card">
            <div class="card-header">
                <div>
                    <h2>Qué estamos midiendo</h2>
                    <div class="card-subtitle">Una consulta = una señal de demanda</div>
                </div>
            </div>

            <div class="identity">
                <strong>{{ auth()->user()->sucursal?->nombre ?? 'SIN SUCURSAL ASIGNADA' }}</strong>
                <div style="margin-top:4px">{{ auth()->user()->name }}</div>
            </div>

            <div class="info-row">
                <div class="info-dot">1</div>
                <div>
                    <strong>Grupo opcional</strong>
                    <span>Si no está catalogado, describí el producto en Observación.</span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-dot">2</div>
                <div>
                    <strong>Color y talle obligatorios</strong>
                    <span>Esos dos datos deben registrarse siempre.</span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-dot">3</div>
                <div>
                    <strong>Una consulta por cliente</strong>
                    <span>Si otro cliente pregunta lo mismo, registralo nuevamente para medir la frecuencia.</span>
                </div>
            </div>
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const grupo = document.getElementById('grupo_id');
    const observacion = document.getElementById('observacion');
    const aviso = document.getElementById('grupoNoCatalogadoAviso');
    const ayuda = document.getElementById('observacionAyuda');
    const requiredLabel = document.getElementById('observacionRequiredLabel');

    function actualizarReglaGrupo() {
        const sinGrupo = !grupo.value;

        observacion.required = sinGrupo;
        aviso.style.display = sinGrupo ? 'block' : 'none';
        requiredLabel.textContent = sinGrupo ? '(obligatoria)' : '(opcional)';
        ayuda.textContent = sinGrupo
            ? 'Como no seleccionaste un grupo, describí obligatoriamente qué producto pidió el cliente.'
            : 'La observación es opcional cuando seleccionaste un grupo.';
    }

    grupo.addEventListener('change', actualizarReglaGrupo);

    if (window.jQuery) {
        $('#grupo_id').on('change.select2', actualizarReglaGrupo);
    }

    actualizarReglaGrupo();
});
</script>
@endpush
