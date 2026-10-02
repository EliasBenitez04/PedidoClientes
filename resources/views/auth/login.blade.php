@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<div class="login-page">
    <section class="login-visual">
        <div class="brand-mark">PC</div>
        <h2>Pedidos claros.<br>Origen identificado.</h2>
        <p>Centralizá las solicitudes de clientes y sabé automáticamente qué usuario y qué sucursal registraron cada pedido.</p>
    </section>

    <section class="login-panel">
        <div class="login-card">
            <h1>Bienvenido</h1>
            <p class="intro">Ingresá con tu usuario para acceder al sistema.</p>

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="field">
                    <label for="email">Correo electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="usuario@empresa.com" required autofocus>
                </div>

                <div class="field">
                    <label for="password">Contraseña</label>
                    <input id="password" type="password" name="password" placeholder="Ingresá tu contraseña" required>
                </div>

                <label style="display:flex;align-items:center;gap:8px;font-weight:500;margin:4px 0 18px;color:#667085">
                    <input type="checkbox" name="remember" value="1" style="width:auto;min-height:auto;margin:0">
                    Mantener sesión iniciada
                </label>

                <button class="btn btn-primary btn-lg" style="width:100%" type="submit">Ingresar al sistema</button>
            </form>

            <div class="login-footer">Pedido Clientes · Acceso seguro por usuario</div>
        </div>
    </section>
</div>
@endsection
