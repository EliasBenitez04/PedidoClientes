@extends('layouts.app')

@section('title', 'Nuevo pedido')

@section('content')
<div class="page-heading">
    <div>
        <h1>Nuevo pedido</h1>
        <p class="muted">Registrá exactamente la variante solicitada por el cliente.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('pedidos.index') }}">Ver historial</a>
    </div>
</div>

<div class="order-layout">
    <div class="card form-card card-elevated">
        <div class="form-card-header">
            <h2>Datos de la solicitud</h2>
            <p>Completá los campos en orden para evitar combinaciones inexistentes.</p>
        </div>

        <form method="POST" action="{{ route('pedidos.store') }}" id="pedidoForm" class="form-card-body">
            @csrf

            <div class="form-section">
                <div class="section-title">
                    <div class="step-number">1</div>
                    <div><strong>Elegí la variante</strong><span>Grupo, color y talle disponibles en el catálogo.</span></div>
                </div>

                <div class="grid grid-3">
                    <div class="field">
                        <label for="grupo">Grupo</label>
                        <select name="grupo" id="grupo" required>
                            <option value="">Seleccionar grupo...</option>
                            @foreach($grupos as $grupo)
                                <option value="{{ $grupo }}" @selected(old('grupo') === $grupo)>{{ $grupo }}</option>
                            @endforeach
                        </select>
                        <small class="field-hint">Primero seleccioná el grupo.</small>
                    </div>

                    <div class="field">
                        <label for="color">Color</label>
                        <select name="color" id="color" required disabled>
                            <option value="">Elegí primero el grupo</option>
                        </select>
                        <small class="field-hint">Se filtra según el grupo elegido.</small>
                    </div>

                    <div class="field">
                        <label for="talle">Talle</label>
                        <select name="talle" id="talle" required disabled>
                            <option value="">Elegí primero el color</option>
                        </select>
                        <small class="field-hint">Solo muestra talles disponibles.</small>
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
                <div><strong>Usuario autenticado</strong><span>Se registra quién realizó la carga.</span></div>
            </div>
            <div class="info-row">
                <div class="info-dot">2</div>
                <div><strong>Sucursal protegida</strong><span>El local no se selecciona ni se puede alterar desde el formulario.</span></div>
            </div>
            <div class="info-row">
                <div class="info-dot">3</div>
                <div><strong>Fecha y hora</strong><span>Se guardan automáticamente al registrar.</span></div>
            </div>
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const grupo = document.getElementById('grupo');
    const color = document.getElementById('color');
    const talle = document.getElementById('talle');
    const urlColores = @json(route('catalogo.colores'));
    const urlTalles = @json(route('catalogo.talles'));
    const oldColor = @json(old('color'));
    const oldTalle = @json(old('talle'));

    function setOptions(select, items, placeholder) {
        select.innerHTML = '';
        const first = document.createElement('option');
        first.value = '';
        first.textContent = placeholder;
        select.appendChild(first);

        items.forEach(function (item) {
            const opt = document.createElement('option');
            opt.value = item;
            opt.textContent = item;
            select.appendChild(opt);
        });

        select.disabled = items.length === 0;
    }

    async function cargarColores(selectOld) {
        talle.disabled = true;
        setOptions(talle, [], 'Elegí primero el color');

        if (!grupo.value) {
            setOptions(color, [], 'Elegí primero el grupo');
            return;
        }

        color.disabled = true;
        color.innerHTML = '<option value="">Cargando colores...</option>';

        try {
            const r = await fetch(urlColores + '?grupo=' + encodeURIComponent(grupo.value), {headers:{'Accept':'application/json'}});
            const items = await r.json();
            setOptions(color, items, 'Seleccionar color...');

            if (selectOld && oldColor && items.includes(oldColor)) {
                color.value = oldColor;
                await cargarTalles(true);
            }
        } catch (e) {
            setOptions(color, [], 'No se pudieron cargar los colores');
        }
    }

    async function cargarTalles(selectOld) {
        if (!grupo.value || !color.value) {
            setOptions(talle, [], 'Elegí primero el color');
            return;
        }

        talle.disabled = true;
        talle.innerHTML = '<option value="">Cargando talles...</option>';

        try {
            const r = await fetch(urlTalles + '?grupo=' + encodeURIComponent(grupo.value) + '&color=' + encodeURIComponent(color.value), {headers:{'Accept':'application/json'}});
            const items = await r.json();
            setOptions(talle, items, 'Seleccionar talle...');

            if (selectOld && oldTalle && items.includes(oldTalle)) {
                talle.value = oldTalle;
            }
        } catch (e) {
            setOptions(talle, [], 'No se pudieron cargar los talles');
        }
    }

    grupo.addEventListener('change', function(){ cargarColores(false); });
    color.addEventListener('change', function(){ cargarTalles(false); });

    if (grupo.value) cargarColores(true);
})();
</script>
@endpush
