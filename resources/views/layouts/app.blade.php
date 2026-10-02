@php
    $resolveBrandAsset = function (array $candidates) {
        foreach ($candidates as $candidate) {
            $relative = 'images/'.$candidate;
            $absolute = public_path($relative);

            if (file_exists($absolute)) {
                return asset($relative).'?v='.filemtime($absolute);
            }
        }

        return asset('images/'.$candidates[0]);
    };

    $logoGotitasClaro = $resolveBrandAsset([
        'logo-gotitas.png',
        'logo-gotitas.jpg',
        'logo-gotitas.jpeg',
        'logo-gotitas.webp',
        'gotitas.png',
        'logo.png',
    ]);

    $logoGotitasOscuro = $resolveBrandAsset([
        'logo-gotitas-blanco.png',
        'logo-gotitas-blanco.jpg',
        'logo-gotitas-blanco.jpeg',
        'logo-gotitas-blanco.webp',
        'logo-gotitas-white.png',
        'logo-gotitas.png',
        'gotitas.png',
    ]);

    $faviconGotitas = $resolveBrandAsset([
        'favicon-gotitas.png',
        'favicon.png',
        'logo-gotitas.png',
    ]);
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#111827">
    <title>@yield('title', 'GOTITAS') - GOTITAS</title>

    <link rel="icon" type="image/png" href="{{ $faviconGotitas }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
@auth
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo-slot sidebar-logo-slot">
                <img
                    src="{{ $logoGotitasOscuro }}"
                    alt="GOTITAS"
                    class="brand-logo brand-logo-sidebar"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='block';"
                >
                <span class="brand-logo-fallback">GOTITAS</span>
            </div>

            <div class="brand-copy">
                <strong>GOTITAS</strong>
                <span>Demanda no cubierta</span>
            </div>
        </div>

        <nav class="nav">
            <div class="nav-section">Operación</div>

            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">D</span><span>Dashboard</span>
            </a>

            <a href="{{ route('solicitudes.create') }}" class="nav-link {{ request()->routeIs('solicitudes.create') ? 'active' : '' }}">
                <span class="nav-icon">+</span><span>Registrar demanda</span>
            </a>

            <a href="{{ route('solicitudes.index') }}" class="nav-link {{ request()->routeIs('solicitudes.index') ? 'active' : '' }}">
                <span class="nav-icon">H</span><span>Historial</span>
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
                GOTITAS · Registro activo
            </div>

            <div class="branch-chip">
                {{ auth()->user()->sucursal?->nombre ?? 'Sin sucursal asignada' }}
            </div>
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

    document.querySelectorAll('.js-discard-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (form.dataset.confirmed === '1' || !window.Swal) return;

            event.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: '¿Descartar este registro?',
                text: 'Dejará de considerarse como una demanda válida.',
                showCancelButton: true,
                confirmButtonText: 'Sí, descartar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#b42318',
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

    document.querySelectorAll('.js-bulk-review-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (form.dataset.confirmed === '1' || !window.Swal) return;

            event.preventDefault();

            const checked = document.querySelectorAll('.js-row-check:checked').length;

            if (!checked) {
                Swal.fire({
                    icon: 'info',
                    title: 'Seleccioná registros',
                    text: 'Marcá al menos un registro para revisarlo.',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }

            Swal.fire({
                icon: 'question',
                title: '¿Marcar como revisados?',
                text: checked + (checked === 1 ? ' registro seleccionado.' : ' registros seleccionados.'),
                showCancelButton: true,
                confirmButtonText: 'Sí, revisar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#067647',
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

    const selectAll = document.getElementById('selectAllRows');
    const rowChecks = Array.from(document.querySelectorAll('.js-row-check'));
    const bulkButton = document.getElementById('bulkReviewButton');
    const bulkCount = document.getElementById('bulkSelectedCount');

    function updateBulkState() {
        const selected = rowChecks.filter(function (checkbox) { return checkbox.checked; }).length;

        if (bulkButton) {
            bulkButton.disabled = selected === 0;
        }

        if (bulkCount) {
            bulkCount.textContent = selected ? selected + ' seleccionado' + (selected === 1 ? '' : 's') : '';
        }

        if (selectAll) {
            selectAll.checked = rowChecks.length > 0 && selected === rowChecks.length;
            selectAll.indeterminate = selected > 0 && selected < rowChecks.length;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            rowChecks.forEach(function (checkbox) {
                checkbox.checked = selectAll.checked;
            });

            updateBulkState();
        });
    }

    rowChecks.forEach(function (checkbox) {
        checkbox.addEventListener('change', updateBulkState);
    });

    updateBulkState();
});
</script>

@stack('scripts')
</body>
</html>
