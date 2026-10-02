@extends('layouts.app')

@section('title', 'Nuevo pedido')

@section('content')
<div class="top">
    <div>
        <h1>Nuevo pedido</h1>
        <div class="muted">Seleccioná la variante solicitada por el cliente.</div>
    </div>
    <a class="btn" href="{{ route('pedidos.index') }}">Ver pedidos</a>
</div>

<div class="card" style="max-width:760px">
    <div class="identity">
        <strong>Origen automático:</strong>
        {{ auth()->user()->name }} · {{ auth()->user()->sucursal?->nombre ?? 'SIN SUCURSAL ASIGNADA' }}
        <div class="muted" style="margin-top:4px">Este dato lo asigna el servidor y no se puede modificar desde el formulario.</div>
    </div>

    <form method="POST" action="{{ route('pedidos.store') }}" id="pedidoForm">
        @csrf
        <div class="grid grid-3">
            <div class="field">
                <label for="grupo">Grupo</label>
                <select name="grupo" id="grupo" required>
                    <option value="">Seleccionar...</option>
                    @foreach($grupos as $grupo)
                        <option value="{{ $grupo }}" @selected(old('grupo') === $grupo)>{{ $grupo }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="color">Color</label>
                <select name="color" id="color" required disabled>
                    <option value="">Elegí primero el grupo</option>
                </select>
            </div>
            <div class="field">
                <label for="talle">Talle</label>
                <select name="talle" id="talle" required disabled>
                    <option value="">Elegí primero el color</option>
                </select>
            </div>
        </div>

        <div class="field">
            <label for="observacion">Observación</label>
            <textarea name="observacion" id="observacion" maxlength="2000" placeholder="Ej.: cliente solicita 2 unidades, llamar cuando llegue, detalle adicional...">{{ old('observacion') }}</textarea>
        </div>

        <button class="btn btn-primary" type="submit">Guardar pedido</button>
    </form>
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

        const r = await fetch(urlColores + '?grupo=' + encodeURIComponent(grupo.value), {headers:{'Accept':'application/json'}});
        const items = await r.json();
        setOptions(color, items, 'Seleccionar color...');
        if (selectOld && oldColor && items.includes(oldColor)) {
            color.value = oldColor;
            await cargarTalles(true);
        }
    }

    async function cargarTalles(selectOld) {
        if (!grupo.value || !color.value) {
            setOptions(talle, [], 'Elegí primero el color');
            return;
        }
        const r = await fetch(urlTalles + '?grupo=' + encodeURIComponent(grupo.value) + '&color=' + encodeURIComponent(color.value), {headers:{'Accept':'application/json'}});
        const items = await r.json();
        setOptions(talle, items, 'Seleccionar talle...');
        if (selectOld && oldTalle && items.includes(oldTalle)) talle.value = oldTalle;
    }

    grupo.addEventListener('change', function(){ cargarColores(false); });
    color.addEventListener('change', function(){ cargarTalles(false); });

    if (grupo.value) cargarColores(true);
})();
</script>
@endpush
