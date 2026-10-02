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
            <p>Cada registro representa una consulta real que no pudo ser cubierta con el stock del local.</p>
        </div>

        <form method="POST" action="{{ route('solicitudes.store') }}" class="form-card-body">
            @csrf

            <div class="form-section">
                <div class="section-title">
                    <div class="step-number">1</div>
                    <div>
                        <strong>Producto solicitado</strong>
                        <span>Seleccioná la combinación que el cliente buscó.</span>
                    </div>
                </div>

                <div class="grid grid-3">
                    <div class="field">
                        <label for="grupo_id">Grupo</label>
                        <select name="grupo_id" id="grupo_id" class="select2" data-placeholder="Buscar grupo..." required>
                            <option value=""></option>
                            @foreach($grupos as $grupo)
                                <option value="{{ $grupo->id }}" @selected((string) old('grupo_id') === (string) $grupo->id)>
                                    {{ $grupo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="color_id">Color</label>
                        <select name="color_id" id="color_id" class="select2" data-placeholder="Buscar color..." required>
                            <option value=""></option>
                            @foreach($colores as $color)
                                <option value="{{ $color->id }}" @selected((string) old('color_id') === (string) $color->id)>
                                    {{ $color->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="talle_id">Talle</label>
                        <select name="talle_id" id="talle_id" class="select2" data-placeholder="Buscar talle..." required>
                            <option value=""></option>
                            @foreach($talles as $talle)
                                <option value="{{ $talle->id }}" @selected((string) old('talle_id') === (string) $talle->id)>
                                    {{ $talle->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="section-title">
                    <div class="step-number">2</div>
                    <div>
                        <strong>Detalle adicional</strong>
                        <span>Es opcional. No hace falta registrar datos personales del cliente.</span>
                    </div>
                </div>

                <div class="field" style="margin-bottom:0">
                    <label for="observacion">Observación</label>
                    <textarea
                        name="observacion"
                        id="observacion"
                        maxlength="2000"
                        placeholder="Ej.: preguntó si llegará nuevamente, buscaba otro modelo parecido, necesitaba para esta semana..."
                    >{{ old('observacion') }}</textarea>
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
                    <strong>Faltante real</strong>
                    <span>Registrá solo cuando el cliente quiso comprar algo que el local no tenía.</span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-dot">2</div>
                <div>
                    <strong>Origen automático</strong>
                    <span>La sucursal se toma del usuario logueado.</span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-dot">3</div>
                <div>
                    <strong>Una consulta por registro</strong>
                    <span>Si otro cliente pregunta lo mismo, registralo de nuevo: eso permite medir frecuencia.</span>
                </div>
            </div>
        </div>
    </aside>
</div>
@endsection
