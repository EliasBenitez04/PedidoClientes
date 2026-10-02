@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<div class="login-wrap">
    <div class="login-card">
        <h1 style="margin-bottom:8px">Pedido Clientes</h1>
        <p class="muted" style="margin-top:0;margin-bottom:22px">Ingresá con tu usuario. La sucursal se identifica automáticamente desde tu cuenta.</p>

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="field">
                <label>Correo</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="field">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>
            <label style="font-weight:500;margin:10px 0 18px">
                <input type="checkbox" name="remember" value="1" style="width:auto;margin-right:6px"> Mantener sesión
            </label>
            <button class="btn btn-primary" style="width:100%" type="submit">Ingresar</button>
        </form>
    </div>
</div>
@endsection
