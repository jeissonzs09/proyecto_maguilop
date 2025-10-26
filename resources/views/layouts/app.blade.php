@php use App\Helpers\PermisosHelper; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maguilop</title>

    {{-- Iconos / librerías --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

    {{-- Vite + Alpine --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        html, body { height: 100%; }
        body { overflow-y: auto; } /* asegura scroll global */
    </style>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">
<div
    x-data="{
        sidebarOpen: JSON.parse(localStorage.getItem('sidebarOpen') ?? (window.innerWidth < 768 ? 'false' : 'true')),
        toggleSidebar(){
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('sidebarOpen', JSON.stringify(this.sidebarOpen));
        },
        init(){
            // Ajuste opcional al cambiar tamaño
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768 && localStorage.getItem('sidebarOpen') === null) {
                    this.sidebarOpen = true;
                }
            });
        }
    }"
    class="min-h-screen flex"
>

    {{-- SIDEBAR --}}
    <aside
  class="fixed inset-y-0 left-0 z-30 w-64 bg-gradient-to-b from-orange-400 to-orange-200 text-gray-800 flex flex-col shadow-lg border-r border-orange-300
         transform transition-transform duration-200 ease-in-out"
  :class="{'-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen}"
>
        {{-- LOGO + BOTÓN (botón a la derecha del logo, dentro del sidebar) --}}
        <div class="flex items-center gap-3 px-4 py-5 border-b border-orange-400">
            <img src="{{ asset('images/logo-maguilop.png') }}" alt="Maguilop" class="h-12 object-contain">
            <button
    x-show="sidebarOpen"
    @click="toggleSidebar"
    aria-label="Ocultar menú"
    title="Ocultar menú"
    class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-white text-gray-800 shadow hover:bg-orange-300/50"
>
    <i class="fa-solid fa-bars"></i>
</button>
        </div>

        {{-- MENÚ (scroll propio) --}}
        <nav class="flex-1 px-4 py-4 text-sm space-y-2 overflow-y-auto h-[calc(100vh-5rem)]">
            <div class="uppercase text-orange-200 text-xs tracking-wide mb-2">Menú principal</div>
            @if(PermisosHelper::tienePermiso('Dashboard', 'ver'))
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-3 py-2 rounded hover:bg-orange-600/70 transition">
                <i class="fas fa-tachometer-alt w-4 h-4"></i>
                <span>Dashboard</span>
            </a>
            @endif

            {{-- Gestión Persona --}}
            <div x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded hover:bg-orange-600/70 transition">
                    <span class="flex items-center space-x-2">
                        <i class="fas fa-folder-open w-4 h-4"></i>
                        <span>Gestión Persona</span>
                    </span>
                    <i class="fas fa-chevron-down text-xs" :class="{'rotate-180': open}" style="transition: transform .2s;"></i>
                </button>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 max-h-0"
                     x-transition:enter-end="opacity-100 max-h-96"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 max-h-96"
                     x-transition:leave-end="opacity-0 max-h-0"
                     class="pl-6 mt-1 space-y-1 overflow-hidden">
                    @if(PermisosHelper::tienePermiso('Persona', 'ver'))
                        <a href="{{ route('persona.index') }}" class="flex items-center space-x-2 px-6 py-2 rounded hover:bg-orange-600/70 transition">
                            <i class="fas fa-user-tie w-4 h-4"></i>
                            <span>Persona</span>
                        </a>
                    @endif
                    @if(PermisosHelper::tienePermiso('Empleados', 'ver'))
                        <a href="{{ route('empleados.index') }}" class="flex items-center space-x-2 px-6 py-2 rounded hover:bg-orange-600/70 transition">
                            <i class="fas fa-user-tie w-4 h-4"></i>
                            <span>Empleados</span>
                        </a>
                    @endif
                    @if(PermisosHelper::tienePermiso('Proveedores', 'ver'))
                        <a href="{{ route('proveedores.index') }}" class="flex items-center space-x-2 px-6 py-2 rounded hover:bg-orange-600/70 transition">
                            <i class="fas fa-industry w-4 h-4"></i>
                            <span>Proveedores</span>
                        </a>
                    @endif
                    @if(PermisosHelper::tienePermiso('Clientes', 'ver'))
                        <a href="{{ route('clientes.index') }}" class="flex items-center space-x-2 px-6 py-2 rounded hover:bg-orange-600/70 transition">
                            <i class="fas fa-users w-4 h-4"></i>
                            <span>Clientes</span>
                        </a>
                        @if(PermisosHelper::tienePermiso('Empresas', 'ver'))
                            <a href="{{ route('empresa.index') }}" class="flex items-center space-x-2 px-6 py-2 rounded hover:bg-orange-600/70 transition">
                                <i class="fas fa-building w-4 h-4"></i>
                                <span>Empresas</span>
                            </a>
                        @endif
                    @endif
                </div>
            </div>

            @if(PermisosHelper::tienePermiso('Reparaciones', 'ver'))
                <a href="{{ route('reparaciones.index') }}" class="flex items-center space-x-2 px-3 py-2 rounded hover:bg-orange-600/70 transition">
                    <i class="fas fa-tools w-4 h-4"></i>
                    <span>Reparaciones</span>
                </a>
            @endif

            @if(PermisosHelper::tienePermiso('Productos', 'ver'))
                <a href="{{ route('producto.index') }}" class="flex items-center space-x-2 px-3 py-2 rounded hover:bg-orange-600/70 transition">
                    <i class="fas fa-box-open w-4 h-4"></i>
                    <span>Productos</span>
                </a>
            @endif

            @if(PermisosHelper::tienePermiso('Pedidos', 'ver'))
                <a href="{{ route('pedidos.index') }}" class="flex items-center space-x-2 px-3 py-2 rounded hover:bg-orange-600/70 transition">
                    <i class="fas fa-clipboard-list w-4 h-4"></i>
                    <span>Pedidos</span>
                </a>
            @endif

            {{-- Compra --}}
            <div x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded hover:bg-orange-600/70 transition">
                    <span class="flex items-center space-x-2">
                        <i class="fas fa-shopping-cart w-4 h-4"></i>
                        <span>Compra</span>
                    </span>
                    <i class="fas fa-chevron-down text-xs" :class="{'rotate-180': open}" style="transition: transform .2s;"></i>
                </button>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 max-h-0"
                     x-transition:enter-end="opacity-100 max-h-96"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 max-h-96"
                     x-transition:leave-end="opacity-0 max-h-0"
                     class="pl-6 mt-1 space-y-1 overflow-hidden">
                    @if(PermisosHelper::tienePermiso('Compras', 'ver'))
                        <a href="{{ route('compras.index') }}" class="flex items-center space-x-2 px-6 py-2 rounded hover:bg-orange-600/70 transition">
                            <i class="fas fa-receipt w-4 h-4"></i>
                            <span>Compras</span>
                        </a>
                    @endif
                    @if(PermisosHelper::tienePermiso('DetalleCompras', 'ver'))
                        <a href="{{ route('detallecompras.index') }}" class="flex items-center space-x-2 px-6 py-2 rounded hover:bg-orange-600/70 transition">
                            <i class="fas fa-box-open w-4 h-4"></i>
                            <span>Detalle Compras</span>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Gestión de Ventas --}}
            <div x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded hover:bg-orange-600/70 transition">
                    <span class="flex items-center space-x-2">
                        <i class="fas fa-file-invoice-dollar w-4 h-4"></i>
                        <span>Gestión de Ventas</span>
                    </span>
                    <i class="fas fa-chevron-down text-xs" :class="{'rotate-180': open}" style="transition: transform .2s;"></i>
                </button>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 max-h-0"
                     x-transition:enter-end="opacity-100 max-h-96"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 max-h-96"
                     x-transition:leave-end="opacity-0 max-h-0"
                     class="pl-6 mt-1 space-y-1 overflow-hidden">
                    @if(PermisosHelper::tienePermiso('Factura', 'ver'))
                        <a href="{{ route('facturas.index') }}" class="flex items-center gap-2 px-6 py-2 rounded hover:bg-orange-600/70 transition">
                            <i class="fas fa-file-invoice w-4 h-4"></i>
                            <span>Ventas</span>
                        </a>
                    @endif
                    @if(PermisosHelper::tienePermiso('CAI', 'ver'))
                        <a href="{{ route('cai.index') }}" class="flex items-center gap-2 px-6 py-2 rounded hover:bg-orange-600/70 transition">
                            <i class="fas fa-clipboard-check w-4 h-4"></i>
                            <span>CAI</span>
                        </a>
                    @endif
                    @if(PermisosHelper::tienePermiso('CuentasPorCobrar', 'ver'))
                        <a href="{{ route('cuentas-por-cobrar.index') }}" class="flex items-center gap-2 px-6 py-2 rounded hover:bg-orange-600/70 transition">
                            <i class="fas fa-money-check-alt w-4 h-4"></i>
                            <span>Cuentas por Cobrar</span>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Seguridad --}}
            <div x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded hover:bg-orange-600/70 transition">
                    <span class="flex items-center space-x-2">
                        <i class="fas fa-shield-alt w-4 h-4"></i>
                        <span>Modulo Seguridad</span>
                    </span>
                    <i class="fas fa-chevron-down text-xs" :class="{'rotate-180': open}" style="transition: transform .2s;"></i>
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 max-h-0"
                     x-transition:enter-end="opacity-100 max-h-96"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 max-h-96"
                     x-transition:leave-end="opacity-0 max-h-0"
                     class="pl-6 mt-1 space-y-1 overflow-hidden">
                    @if(PermisosHelper::tienePermiso('Usuarios', 'ver'))
                        <a href="{{ route('usuarios.index') }}" class="flex items-center gap-2 px-10 py-2 rounded hover:bg-white/20">
                            <i class="fas fa-user w-4 h-4"></i> Usuarios
                        </a>
                    @endif
                    @if(PermisosHelper::tienePermiso('Roles', 'ver'))
                        <a href="{{ route('roles.index') }}" class="flex items-center gap-2 px-10 py-2 rounded hover:bg-white/20">
                            <i class="fas fa-user-shield w-4 h-4"></i> Roles
                        </a>
                    @endif
                    @if(PermisosHelper::tienePermiso('Permisos por Rol', 'ver'))
                        <a href="{{ route('roles.permisos') }}" class="flex items-center gap-2 px-10 py-2 rounded hover:bg-white/20">
                            <i class="fas fa-key w-4 h-4"></i> Permisos
                        </a>
                    @endif
                    @if(PermisosHelper::tienePermiso('Backups', 'ver'))
                        <a href="{{ route('backups.index') }}" class="flex items-center gap-2 px-10 py-2 rounded hover:bg-white/20">
                            <i class="fas fa-database w-4 h-4"></i> Backups
                        </a>
                    @endif
                    @if(PermisosHelper::tienePermiso('Bitacora', 'ver'))
                        <a href="{{ route('bitacoras.index') }}" class="flex items-center gap-2 px-10 py-2 rounded hover:bg-white/20">
                            <i class="fas fa-clipboard-list w-4 h-4"></i> Bitácoras
                        </a>
                    @endif
                </div>
            </div>
        </nav>

        <div class="p-4 text-xs text-orange-200 border-t border-orange-500">Versión 1.0</div>
    </aside>

    {{-- Overlay móvil --}}
    <div
        class="fixed inset-0 bg-black/40 z-20 md:hidden transition-opacity"
        x-show="sidebarOpen"
        x-transition.opacity
        @click="toggleSidebar"
        style="display:none"
    ></div>

    {{-- CONTENIDO PRINCIPAL --}}
    <div
  class="flex-1 flex flex-col min-w-0 transition-all duration-200"
  :class="{'md:ml-64': sidebarOpen, 'md:ml-0': !sidebarOpen}"
>
{{-- HEADER SUPERIOR: título siempre centrado --}}
{{-- HEADER: título SIEMPRE centrado --}}
<header class="bg-gradient-to-r from-orange-300 to-orange-100 text-gray-800 shadow px-4 md:px-6 py-3 md:py-4 sticky top-0 z-40 relative flex items-center">

  {{-- Botón hamburguesa (aparece cuando el sidebar está cerrado) --}}
  <button
      x-cloak
      x-show="!sidebarOpen"
      x-transition
      @click="toggleSidebar"
      aria-label="Mostrar menú"
      title="Mostrar menú"
      class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-white text-gray-800 shadow hover:bg-orange-300/50"
  >
      <i class="fa-solid fa-bars"></i>
  </button>

  {{-- Título centrado absoluto (no se mueve) --}}
  <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none">
    <h1 class="text-base md:text-lg font-semibold text-center">
      {{ $header ?? 'Dashboard General' }}
    </h1>
  </div>

  {{-- Usuario a la derecha --}}
  <div x-data="{ open: false }" class="ml-auto relative">
      <button @click="open = !open" class="flex items-center gap-2 hover:bg-orange-300/50 px-3 py-2 rounded">
          <img
              src="{{ Auth::user()->Foto ? asset('storage/'.Auth::user()->Foto) : asset('images/avatar-default.png') }}"
              alt="Avatar" class="w-10 h-10 rounded-full border border-gray-300">
          <span class="hidden md:inline font-medium text-base">
              {{ Auth::user()->NombreUsuario }}
          </span>
      </button>

      <div x-show="open" x-transition
           class="absolute right-0 mt-2 w-64 bg-white text-gray-800 rounded shadow-md z-50">
          <div class="px-4 py-3 border-b">
              <div class="flex items-center gap-3">
                  <img
                      src="{{ Auth::user()->Foto ? asset('storage/'.Auth::user()->Foto) : asset('images/avatar-default.png') }}"
                      alt="Avatar" class="w-12 h-12 rounded-full border border-gray-300">
                  <div class="min-w-0">
                      <div class="font-semibold truncate">{{ Auth::user()->name }}</div>
                      <div class="text-sm text-gray-500 truncate">{{ Auth::user()->CorreoElectronico }}</div>
                      <div class="text-xs text-gray-400 truncate">{{ Auth::user()->TipoUsuario }}</div>
                  </div>
              </div>
          </div>

          <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-blue-600 hover:bg-blue-100">
              Editar perfil
          </a>
          <a href="{{ route('help.index') }}" class="block px-4 py-2 text-blue-600 hover:bg-blue-100">
              Ayuda
          </a>
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

        {{-- CONTENIDO (con scroll) --}}
        <main class="p-4 md:p-6 flex-1 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        document.body.style.overflowY = 'auto';
        document.documentElement.style.overflowY = 'auto';
    });
</script>

@stack('scripts')
</body>
</html>