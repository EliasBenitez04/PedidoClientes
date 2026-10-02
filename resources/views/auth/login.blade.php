@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<div class="login-page">
    <section class="login-visual">
        <div class="login-brand-logo">
            <img
                src="{{ asset('images/logo-gotitas-blanco.png') }}"
                alt="GOTITAS"
                onerror="this.style.display='none';this.nextElementSibling.style.display='block';"
            >
            <span class="login-logo-fallback">GOTITAS</span>
        </div>

        <h2>Escuchá lo que<br>el cliente está pidiendo.</h2>
        <p>Registrá productos que los clientes buscan y no encuentran en el local para convertir faltantes en información comercial.</p>
    </section>

    <section class="login-panel">
        <div class="login-card">
            <div class="login-logo-light">
                <img
                    src="{{ asset('images/logo-gotitas.png') }}"
                    alt="GOTITAS"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='block';"
                >
                <span class="login-logo-light-fallback">GOTITAS</span>
            </div>

            <h1>GOTITAS</h1>
            <p class="intro">Demanda no cubierta · Ingresá con el usuario asignado a tu tienda.</p>

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="field">
                    <label for="username">Usuario</label>
                    <input
                        id="username"
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="Ej.: TIENDA01"
                        maxlength="80"
                        autocomplete="username"
                        style="text-transform:uppercase"
                        required
                        autofocus
                    >
                </div>

                <div class="field">
                    <label for="password">Contraseña</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Ingresá tu contraseña"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <label style="display:flex;align-items:center;gap:8px;font-weight:500;margin:4px 0 18px;color:#667085">
                    <input type="checkbox" name="remember" value="1" style="width:auto;min-height:auto;margin:0">
                    Mantener sesión iniciada
                </label>

                <button class="btn btn-primary btn-lg" style="width:100%" type="submit">
                    Ingresar
                </button>
            </form>

            <div class="login-footer">GOTITAS · Registro de oportunidades no cubiertas</div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const username = document.getElementById('username');

    if (username) {
        username.addEventListener('input', function () {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9_]/g, '');
        });
    }
});
</script>
@endpush
