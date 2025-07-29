@php use App\Helpers\PermisosHelper; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maguilop</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>


<body class="bg-gray-100 text-gray-800 font-sans">

<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-gradient-to-b from-orange-400 to-orange-200 text-gray-800 flex flex-col shadow-lg">
        {{-- LOGO --}}
<div class="flex justify-center items-center px-4 py-5 border-b border-orange-400">
    <img src="{{ asset('images/logo-maguilop.png') }}" alt="Logo" class="h-12 object-contain w-full">
</div>


        {{-- NAVEGACIÓN --}}
        <nav class="flex-1 px-4 py-4 text-sm space-y-2">

            <div class="uppercase text-orange-200 text-xs tracking-wide mb-2">Menú principal</div>

<a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-3 py-2 rounded hover:bg-orange-600 transition">
    <i class="fas fa-tachometer-alt w-4 h-4"></i>
    <span>Dashboard</span>
</a>

            {{-- Gestión --}}
            <div x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded hover:bg-orange-600 transition">
    <span class="flex items-center space-x-2">
        <i class="fas fa-folder-open w-4 h-4"></i>
        <span>Gestión Persona</span>
    </span>
    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 max-h-0"
         x-transition:enter-end="opacity-100 max-h-96"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 max-h-96"
         x-transition:leave-end="opacity-0 max-h-0"
         class="pl-6 mt-1 space-y-1 overflow-hidden">
                    @if(PermisosHelper::tienePermiso('Persona', 'ver'))

   <a href="{{ route('persona.index') }}" class="flex items-center space-x-2 px-6 py-2 hover:bg-orange-600 transition">
    <i class="fas fa-user-tie w-4 h-4"></i>
    <span>Persona</span>
</a>

                    @endif
                    @if(PermisosHelper::tienePermiso('Empleados', 'ver'))                 
                    
<a href="{{ route('empleados.index') }}" class="flex items-center space-x-2 px-6 py-2 hover:bg-orange-600 transition">
    <i class="fas fa-user-tie w-4 h-4"></i>
    <span>Empleados</span>
</a>

                    @endif
                    @if(PermisosHelper::tienePermiso('Proveedores', 'ver'))
<a href="{{ route('proveedores.index') }}" class="flex items-center space-x-2 px-6 py-2 hover:bg-orange-600 transition">
    <i class="fas fa-industry w-4 h-4"></i>
    <span>Proveedores</span>
</a>

                    @endif
                    @if(PermisosHelper::tienePermiso('Clientes', 'ver'))
<a href="{{ route('clientes.index') }}" class="flex items-center space-x-2 px-6 py-2 hover:bg-orange-600 transition">
    <i class="fas fa-users w-4 h-4"></i>
    <span>Clientes</span>
</a>

@if(PermisosHelper::tienePermiso('Empresas', 'ver'))
<a href="{{ route('empresa.index') }}" class="flex items-center space-x-2 px-6 py-2 hover:bg-orange-600 transition">
    <i class="fas fa-building w-4 h-4"></i>
    <span>Empresas</span>
</a>
@endif


                    @endif
                </div>
            </div>




            @if(PermisosHelper::tienePermiso('Reparaciones', 'ver'))
<a href="{{ route('reparaciones.index') }}" class="flex items-center space-x-2 px-3 py-2 rounded hover:bg-orange-600 transition">
    <i class="fas fa-tools w-4 h-4"></i>
    <span>Reparaciones</span>
</a>

            @endif

            @if(PermisosHelper::tienePermiso('Productos', 'ver'))
<a href="{{ route('producto.index') }}" class="flex items-center space-x-2 px-3 py-2 rounded hover:bg-orange-600 transition">
    <i class="fas fa-box-open w-4 h-4"></i>
    <span>Productos</span>
</a>

            @endif

                        @if(PermisosHelper::tienePermiso('Pedidos', 'ver'))
<a href="{{ route('pedidos.index') }}" class="flex items-center space-x-2 px-3 py-2 rounded hover:bg-orange-600 transition">
    <i class="fas fa-clipboard-list w-4 h-4"></i>
    <span>Pedidos</span>
</a>

            @endif


                        {{-- Compra --}}
            <div x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded hover:bg-orange-600 transition">
    <span class="flex items-center space-x-2">
        <i class="fas fa-shopping-cart w-4 h-4"></i>
        <span>Compra</span>
    </span>
    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 max-h-0"
         x-transition:enter-end="opacity-100 max-h-96"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 max-h-96"
         x-transition:leave-end="opacity-0 max-h-0"
         class="pl-6 mt-1 space-y-1 overflow-hidden">
                    @if(PermisosHelper::tienePermiso('Compras', 'ver'))
<a href="{{ route('compras.index') }}" class="flex items-center space-x-2 px-6 py-2 hover:bg-orange-600 transition">
    <i class="fas fa-receipt w-4 h-4"></i>
    <span>Compras</span>
</a>
                    @endif
                    @if(PermisosHelper::tienePermiso('DetalleCompras', 'ver'))
<a href="{{ route('detallecompras.index') }}" class="flex items-center space-x-2 px-6 py-2 hover:bg-orange-600 transition">
    <i class="fas fa-box-open w-4 h-4"></i>
    <span>Detalle Compras</span>
</a>
                    @endif
                </div>
            </div>


            @if(PermisosHelper::tienePermiso('Ventas', 'ver'))
<a href="{{ route('ventas.index') }}" class="flex items-center space-x-2 px-3 py-2 rounded hover:bg-orange-600 transition">
    <i class="fas fa-cash-register w-4 h-4"></i>
    <span>Ventas</span>
</a>

            @endif



            @if(PermisosHelper::tienePermiso('Factura', 'ver'))
<a href="{{ route('facturas.index') }}" class="flex items-center space-x-2 px-3 py-2 rounded hover:bg-orange-600 transition">
    <i class="fas fa-file-invoice w-4 h-4"></i>
    <span>Facturas</span>
</a>

            @endif


                {{-- Seguridad --}}
<div x-data="{ open: false }">
    <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded hover:bg-orange-600 transition">
        <span class="flex items-center space-x-2">
            <i class="fas fa-shield-alt w-4 h-4"></i>
            <span>Modulo Seguridad</span>
        </span>
        <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }" style="transition: transform 0.3s;"></i>
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 max-h-0"
         x-transition:enter-end="opacity-100 max-h-96"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 max-h-96"
         x-transition:leave-end="opacity-0 max-h-0"
         class="pl-6 mt-1 space-y-1 overflow-hidden">

        @if(PermisosHelper::tienePermiso('Usuarios', 'ver'))
        <a href="{{ route('usuarios.index') }}" class="flex items-center gap-2 px-10 py-2 hover:bg-white/10">
            <i class="fas fa-user w-4 h-4"></i>
            Usuarios
        </a>
        @endif

        @if(PermisosHelper::tienePermiso('Roles', 'ver'))
        <a href="{{ route('roles.index') }}" class="flex items-center gap-2 px-10 py-2 hover:bg-white/10">
            <i class="fas fa-user-shield w-4 h-4"></i>
            Roles
        </a>
        @endif

        @if(PermisosHelper::tienePermiso('Permisos por Rol', 'ver'))
        <a href="{{ route('roles.permisos') }}" class="flex items-center gap-2 px-10 py-2 hover:bg-white/10">
            <i class="fas fa-key w-4 h-4"></i>
            Permisos
        </a>
        @endif

        @if(PermisosHelper::tienePermiso('Backups', 'ver'))
        <a href="{{ route('backups.index') }}" class="flex items-center gap-2 px-10 py-2 hover:bg-white/10">
            <i class="fas fa-database w-4 h-4"></i>
            Backups
        </a>
        @endif

        @if(PermisosHelper::tienePermiso('Bitacora', 'ver'))
        <a href="{{ route('bitacoras.index') }}" class="flex items-center gap-2 px-10 py-2 hover:bg-white/10">
            <i class="fas fa-clipboard-list w-4 h-4"></i>
            Bitácoras
        </a>
        @endif

    </div>
</div>

        </nav>

        <div class="p-4 text-xs text-orange-200 border-t border-orange-500">Versión 1.0</div>
    </aside>




    {{-- CONTENIDO PRINCIPAL --}}
<div class="flex-1 flex flex-col overflow-hidden">

        {{-- ENCABEZADO --}}
<header class="bg-gradient-to-r from-orange-300 to-orange-100 text-gray-800 shadow px-6 py-4 flex justify-between items-center sticky top-0 z-40">
            <h1 class="text-lg font-semibold">{{ $header ?? 'Panel' }}</h1>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center space-x-2 hover:bg-orange-500 px-3 py-2 rounded">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5.121 17.804A13.937 13.937 0 0112 15c2.577 0 4.97.728 6.879 1.975M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="hidden md:inline">{{ Auth::user()->name }}</span>
                </button>
                <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded shadow-md z-50">
                    <div class="px-4 py-2 border-b">
                        <div class="font-semibold">{{ Auth::user()->name }}</div>
<div class="text-sm text-gray-500">{{ Auth::user()->NombreUsuario }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
<button type="submit"
        class="w-full px-4 py-2 text-left text-red-600 hover:bg-red-100 flex items-center gap-2">
    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
</button>

                    </form>
                </div>
            </div>
        </header>

        {{-- CONTENIDO --}}
        <main class="p-6 flex-1 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>