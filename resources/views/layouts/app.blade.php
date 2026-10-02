<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pedido Clientes') - {{ config('app.name') }}</title>
    <style>
        :root{--nav:#172033;--primary:#2563eb;--bg:#f4f6fa;--text:#172033;--muted:#667085;--border:#e4e7ec;--success:#067647;--danger:#b42318;--warning:#b54708}
        *{box-sizing:border-box}body{margin:0;font-family:Inter,Segoe UI,Arial,sans-serif;background:var(--bg);color:var(--text)}
        a{color:inherit;text-decoration:none}.app{min-height:100vh;display:flex}.sidebar{width:250px;background:var(--nav);color:#fff;padding:24px 16px;position:fixed;inset:0 auto 0 0;overflow:auto}
        .brand{font-size:20px;font-weight:800;margin:0 8px 24px}.brand small{display:block;font-size:11px;font-weight:500;color:#aeb8ca;margin-top:4px}
        .nav a{display:block;padding:11px 12px;border-radius:9px;color:#d9e0ea;margin:3px 0;font-size:14px}.nav a:hover,.nav a.active{background:#25324a;color:#fff}
        .userbox{margin-top:24px;padding:12px;background:#202d43;border-radius:10px;font-size:12px;color:#cbd5e1}.userbox strong{display:block;color:#fff;margin-bottom:4px}
        .content{margin-left:250px;width:calc(100% - 250px);padding:28px}.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;gap:16px}
        h1{font-size:26px;margin:0}h2{font-size:18px;margin:0 0 16px}.muted{color:var(--muted)}.card{background:#fff;border:1px solid var(--border);border-radius:14px;padding:20px;box-shadow:0 1px 2px rgba(16,24,40,.04);margin-bottom:18px}
        .grid{display:grid;gap:16px}.grid-4{grid-template-columns:repeat(4,1fr)}.grid-3{grid-template-columns:repeat(3,1fr)}.grid-2{grid-template-columns:repeat(2,1fr)}
        .kpi .value{font-size:30px;font-weight:800;margin-top:8px}.kpi .label{color:var(--muted);font-size:13px}
        label{display:block;font-size:13px;font-weight:700;margin-bottom:6px}.field{margin-bottom:15px}input,select,textarea{width:100%;padding:10px 11px;border:1px solid #d0d5dd;border-radius:8px;background:#fff;font:inherit;color:#101828}
        input:focus,select:focus,textarea:focus{outline:2px solid #bfdbfe;border-color:#60a5fa}textarea{min-height:90px;resize:vertical}
        .btn{display:inline-flex;align-items:center;justify-content:center;border:0;border-radius:8px;padding:10px 14px;font-weight:700;font-size:13px;cursor:pointer;background:#e5e7eb}
        .btn-primary{background:var(--primary);color:#fff}.btn-danger{background:#fee4e2;color:var(--danger)}.btn-sm{padding:7px 9px;font-size:12px}.btn-link{background:transparent;color:#fff;padding:8px 0}
        table{width:100%;border-collapse:collapse;font-size:13px}th{text-align:left;color:#475467;background:#f9fafb;font-size:12px}th,td{padding:11px 10px;border-bottom:1px solid var(--border);vertical-align:top}
        .badge{display:inline-block;padding:4px 8px;border-radius:999px;font-size:11px;font-weight:800}.badge-pendiente{background:#fff7ed;color:#b54708}.badge-procesado{background:#ecfdf3;color:#067647}.badge-cancelado{background:#fef3f2;color:#b42318}
        .alert{padding:12px 14px;border-radius:9px;margin-bottom:16px;font-size:13px}.alert-success{background:#ecfdf3;color:#067647}.alert-danger{background:#fef3f2;color:#b42318}
        .actions{display:flex;gap:8px;flex-wrap:wrap}.identity{background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px 14px;margin-bottom:18px;font-size:13px}
        .login-wrap{min-height:100vh;display:grid;place-items:center;padding:20px}.login-card{width:100%;max-width:420px;background:#fff;border:1px solid var(--border);border-radius:16px;padding:28px;box-shadow:0 10px 30px rgba(16,24,40,.08)}
        .pagination{display:flex;justify-content:flex-end;gap:8px;margin-top:15px}.empty{padding:30px;text-align:center;color:var(--muted)}
        @media(max-width:1000px){.grid-4{grid-template-columns:repeat(2,1fr)}.grid-3{grid-template-columns:1fr}.sidebar{width:210px}.content{margin-left:210px;width:calc(100% - 210px)}}
        @media(max-width:760px){.app{display:block}.sidebar{position:static;width:auto}.content{margin:0;width:100%;padding:16px}.grid-4,.grid-2{grid-template-columns:1fr}.top{align-items:flex-start;flex-direction:column}.card{overflow:auto}}
    </style>
</head>
<body>
@auth
<div class="app">
    <aside class="sidebar">
        <div class="brand">Pedido Clientes<small>Registro por sucursal</small></div>
        <nav class="nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('pedidos.create') }}" class="{{ request()->routeIs('pedidos.create') ? 'active' : '' }}">Nuevo pedido</a>
            <a href="{{ route('pedidos.index') }}" class="{{ request()->routeIs('pedidos.index') ? 'active' : '' }}">Pedidos</a>
            <a href="{{ route('reportes.index') }}" class="{{ request()->routeIs('reportes.*') ? 'active' : '' }}">Reportes</a>
            @if(in_array(auth()->user()->rol, ['ADMIN','SUPERVISOR']))
                <a href="{{ route('catalogo.importar') }}" class="{{ request()->routeIs('catalogo.*') ? 'active' : '' }}">Importar catálogo</a>
            @endif
            @if(auth()->user()->rol === 'ADMIN')
                <a href="{{ route('usuarios.index') }}" class="{{ request()->routeIs('usuarios.*') ? 'active' : '' }}">Usuarios y roles</a>
                <a href="{{ route('sucursales.index') }}" class="{{ request()->routeIs('sucursales.*') ? 'active' : '' }}">Sucursales</a>
            @endif
        </nav>
        <div class="userbox">
            <strong>{{ auth()->user()->name }}</strong>
            {{ auth()->user()->rol }} · {{ auth()->user()->sucursal?->nombre ?? 'Sin sucursal' }}
            <form action="{{ route('logout') }}" method="POST" style="margin-top:8px">
                @csrf
                <button type="submit" class="btn btn-link">Cerrar sesión</button>
            </form>
        </div>
    </aside>
    <main class="content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Revisá los datos:</strong>
                <ul style="margin:7px 0 0 18px;padding:0">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
</div>
@else
    @yield('content')
@endauth
@stack('scripts')
</body>
</html>
