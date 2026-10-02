<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#111827">
    <title>@yield('title', 'Pedido Clientes') - {{ config('app.name') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
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
                <form action="{{ route('logout') }}" method="POST" class="logout-form js-confirm-logout">
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
            @yield('content')
        </main>
    </section>
</div>
@else
    @yield('content')
@endauth

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery && $.fn.select2) {
        $('.select2').each(function () {
            const $select = $(this);
            $select.select2({
                width: '100%',
                placeholder: $select.data('placeholder') || 'Seleccionar...',
                allowClear: $select.data('allow-clear') !== false,
                language: {
                    noResults: function () { return 'No se encontraron resultados'; },
                    searching: function () { return 'Buscando...'; }
                }
            });
        });

        $('.select2-no-search').each(function () {
            const $select = $(this);
            $select.select2({
                width: '100%',
                minimumResultsForSearch: Infinity,
                placeholder: $select.data('placeholder') || 'Seleccionar...',
                allowClear: $select.data('allow-clear') !== false
            });
        });
    }

    const successMessage = @json(session('success'));
    if (successMessage && window.Swal) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: successMessage,
            showConfirmButton: false,
            timer: 3200,
            timerProgressBar: true
        });
    }

    const validationErrors = @json($errors->all());
    if (validationErrors.length && window.Swal) {
        Swal.fire({
            icon: 'error',
            title: 'Revisá los datos',
            html: '<div style="text-align:left">' + validationErrors.map(function (error) {
                return '<div style="margin:6px 0">• ' + $('<div>').text(error).html() + '</div>';
            }).join('') + '</div>',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#4f46e5'
        });
    }

    document.querySelectorAll('.js-confirm-logout').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (form.dataset.confirmed === '1' || !window.Swal) return;

            event.preventDefault();

            Swal.fire({
                icon: 'question',
                title: '¿Cerrar sesión?',
                text: 'Vas a salir del sistema.',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#667085',
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.dataset.confirmed = '1';
                    form.submit();
                }
            });
        });
    });

    document.querySelectorAll('.js-state-select').forEach(function (select) {
        select.addEventListener('change', function () {
            const form = select.closest('form');
            const previous = select.dataset.current;
            const next = select.value;

            if (!window.Swal) {
                form.submit();
                return;
            }

            Swal.fire({
                icon: 'question',
                title: '¿Cambiar estado?',
                text: 'El pedido pasará de ' + previous + ' a ' + next + '.',
                showCancelButton: true,
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#667085',
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                } else {
                    select.value = previous;
                }
            });
        });
    });
});
</script>

@stack('scripts')
</body>
</html>
