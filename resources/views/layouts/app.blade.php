<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#111827">
    <title>@yield('title', 'Pedido Clientes') - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@auth
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-mark">PC</div>
            <div class="brand-copy">
                <strong>Pedido Clientes</strong>
                <span>Gestión por sucursal</span>
            </div>
        </div>

        <nav class="nav">
            <div class="nav-section">Operación</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">D</span><span>Dashboard</span>
            </a>
            <a href="{{ route('pedidos.create') }}" class="nav-link {{ request()->routeIs('pedidos.create') ? 'active' : '' }}">
                <span class="nav-icon">+</span><span>Nuevo pedido</span>
            </a>
            <a href="{{ route('pedidos.index') }}" class="nav-link {{ request()->routeIs('pedidos.index') ? 'active' : '' }}">
                <span class="nav-icon">P</span><span>Pedidos</span>
            </a>
            <a href="{{ route('reportes.index') }}" class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                <span class="nav-icon">R</span><span>Reportes</span>
            </a>

            @if(in_array(auth()->user()->rol, ['ADMIN','SUPERVISOR']))
                <div class="nav-section">Catálogo</div>
                <a href="{{ route('catalogo.importar') }}" class="nav-link {{ request()->routeIs('catalogo.*') ? 'active' : '' }}">
                    <span class="nav-icon">I</span><span>Importar catálogo</span>
                </a>
            @endif

            @if(auth()->user()->rol === 'ADMIN')
                <div class="nav-section">Administración</div>
                <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                    <span class="nav-icon">U</span><span>Usuarios y roles</span>
                </a>
                <a href="{{ route('sucursales.index') }}" class="nav-link {{ request()->routeIs('sucursales.*') ? 'active' : '' }}">
                    <span class="nav-icon">S</span><span>Sucursales</span>
                </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-profile">
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div class="user-info">
                        <strong>{{ auth()->user()->name }}</strong>
                        <span>{{ auth()->user()->sucursal?->nombre ?? 'Sin sucursal' }}</span>
                    </div>
                </div>
                <span class="role-pill">{{ auth()->user()->rol }}</span>
                <form action="{{ route('logout') }}" method="POST" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-link">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </aside>

    <section class="main-shell">
        <header class="app-topbar">
            <div class="topbar-status">
                <span class="status-dot"></span>
                Sistema operativo
            </div>
            <div class="branch-chip">{{ auth()->user()->sucursal?->nombre ?? 'Sin sucursal asignada' }}</div>
        </header>

        <main class="content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Revisá los datos ingresados.</strong>
                    <ul style="margin:6px 0 0 16px;padding:0">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </section>
</div>
@else
    @yield('content')
@endauth

@stack('scripts')
</body>
</html>
