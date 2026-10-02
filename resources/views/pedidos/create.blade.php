@extends('layouts.app')

@section('title', 'Nuevo pedido')

@section('content')
<div class="page-heading">
    <div>
        <h1>Nuevo pedido</h1>
        <p class="muted">Seleccioná grupo, color y talle de sus catálogos independientes.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('pedidos.index') }}">Ver historial</a>
    </div>
</div>

<div class="order-layout">
    <div class="card form-card card-elevated">
        <div class="form-card-header">
            <h2>Datos de la solicitud</h2>
            <p>Los tres catálogos son independientes. Podés buscar escribiendo dentro de cada selector.</p>
        </div>

        <form method="POST" action="{{ route('pedidos.store') }}" id="pedidoForm" class="form-card-body">
            @csrf

            <div class="form-section">
                <div class="section-title">
                    <div class="step-number">1</div>
                    <div><strong>Elegí lo solicitado</strong><span>Grupo, color y talle disponibles.</span></div>
                </div>

                <div class="grid grid-3">
                    <div class="field">
                        <label for="grupo_id">Grupo</label>
                        <select name="grupo_id" id="grupo_id" class="select2" data-placeholder="Buscar grupo..." required>
                            <option value=""></option>
                            @foreach($grupos as $grupo)
                                <option value="{{ $grupo->id }}" @selected((string) old('grupo_id') === (string) $grupo->id)>{{ $grupo->nombre }}</option>
                            @endforeach
                        </select>
                        <small class="field-hint">{{ $grupos->count() }} opciones disponibles.</small>
                    </div>

                    <div class="field">
                        <label for="color_id">Color</label>
                        <select name="color_id" id="color_id" class="select2" data-placeholder="Buscar color..." required>
                            <option value=""></option>
                            @foreach($colores as $color)
                                <option value="{{ $color->id }}" @selected((string) old('color_id') === (string) $color->id)>{{ $color->nombre }}</option>
                            @endforeach
                        </select>
                        <small class="field-hint">{{ $colores->count() }} opciones disponibles.</small>
                    </div>

                    <div class="field">
                        <label for="talle_id">Talle</label>
                        <select name="talle_id" id="talle_id" class="select2" data-placeholder="Buscar talle..." required>
                            <option value=""></option>
                            @foreach($talles as $talle)
                                <option value="{{ $talle->id }}" @selected((string) old('talle_id') === (string) $talle->id)>{{ $talle->nombre }}</option>
                            @endforeach
                        </select>
                        <small class="field-hint">{{ $talles->count() }} opciones disponibles.</small>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="section-title">
                    <div class="step-number">2</div>
                    <div><strong>Agregá una observación</strong><span>Opcional. Usala para cualquier detalle relevante del cliente.</span></div>
                </div>

                <div class="field" style="margin-bottom:0">
                    <label for="observacion">Observación</label>
                    <textarea name="observacion" id="observacion" maxlength="2000" placeholder="Ej.: cliente solicita 2 unidades, avisar cuando llegue, detalle adicional...">{{ old('observacion') }}</textarea>
                    <small class="field-hint">Máximo 2.000 caracteres.</small>
                </div>
            </div>

            <div class="form-actions">
                <a class="btn" href="{{ route('pedidos.index') }}">Cancelar</a>
                <button class="btn btn-primary btn-lg" type="submit">Guardar pedido →</button>
            </div>
        </form>
    </div>

    <aside>
        <div class="card info-card">
            <div class="card-header">
                <div>
                    <h2>Origen del pedido</h2>
                    <div class="card-subtitle">Asignado automáticamente</div>
                </div>
            </div>

            <div class="identity">
                <strong>{{ auth()->user()->sucursal?->nombre ?? 'SIN SUCURSAL ASIGNADA' }}</strong>
                <div style="margin-top:4px">{{ auth()->user()->name }}</div>
            </div>

            <div class="info-row">
                <div class="info-dot">1</div>
                <div><strong>Búsqueda rápida</strong><span>Los selectores permiten escribir para encontrar grupo, color o talle.</span></div>
            </div>
            <div class="info-row">
                <div class="info-dot">2</div>
                <div><strong>Sucursal protegida</strong><span>El local se toma del usuario autenticado y no se puede modificar.</span></div>
            </div>
            <div class="info-row">
                <div class="info-dot">3</div>
                <div><strong>Registro auditable</strong><span>Usuario, fecha y hora quedan guardados automáticamente.</span></div>
            </div>
        </div>
    </aside>
</div>
@endsection
