<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-scroll"></i> Bitácoras
        </h2>
    </x-slot>


    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    <div class="p-4">
        {{-- Si en algún caso deseas agregar nuevas bitácoras manualmente --}}
        {{-- @if($permisos::tienePermiso('Bitacora', 'crear'))
            <a href="{{ route('bitacoras.create') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow">
                <i class="fas fa-plus-circle"></i> Nueva Bitácora
            </a>
        @endif --}}


@php
    $config = \App\Models\Configuracion::first();
@endphp

<div class="flex justify-end mb-4">

{{-- Toast estilo alerta roja para errores (por ejemplo: bitácora inactiva) --}}
@if(session('error'))
    <div 
        x-data="{ show: true }" 
        x-show="show" 
        x-transition:enter="transform ease-out duration-300 transition"
        x-transition:enter-start="scale-95 opacity-0"
        x-transition:enter-end="scale-100 opacity-100"
        x-transition:leave="transform ease-in duration-300 transition"
        x-transition:leave-start="scale-100 opacity-100"
        x-transition:leave-end="scale-95 opacity-0"
        x-init="setTimeout(() => show = false, 3000)" 
        class="fixed inset-0 flex items-center justify-center z-50"
    >
        <div class="bg-red-600 text-white px-8 py-5 rounded-full shadow-2xl flex items-center space-x-6">
            
            {{-- Ícono con borde blanco grueso y X blanca --}}
            <span class="flex items-center justify-center w-10 h-10 border-4 border-white rounded-full bg-transparent">
                <svg class="w-5 h-5" fill="none" stroke="white" stroke-width="5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </span>

            {{-- Texto del mensaje --}}
            <span class="text-lg font-semibold">{{ session('error') }}</span>
        </div>
    </div>
@endif




{{-- Botones a la izquierda --}}
    <div class="flex gap-3">
        <a href="{{ route('bitacoras.exportarPDF', ['search' => request('search')]) }}"
   class="flex items-center gap-2 px-4 py-2 rounded-md shadow text-white bg-red-600 hover:bg-red-700">
   <i class="fas fa-file-pdf"></i> Exportar PDF
</a>



        <a href="{{ route('bitacoras.exportarExcel') }}"
           class="flex items-center gap-2 px-4 py-2 rounded-md shadow text-white bg-green-600 hover:bg-green-700">
           <i class="fas fa-file-excel"></i> Exportar Excel
        </a>

        <form action="{{ route('bitacoras.destroyPDF') }}" method="POST"
      onsubmit="return confirm('¿Estás seguro de eliminar toda la bitácora? Se descargará un PDF antes de borrarla.')">
    @csrf
    @method('DELETE')
    <button type="submit"
            class="flex items-center gap-2 px-4 py-2 rounded-md shadow text-black bg-gray-700 hover:bg-gray-800">
        <i class="fas fa-trash-alt"></i> Eliminar y descargar PDF
    </button>
</form>

    </div>




 <div class="ml-auto">
    <form action="{{ route('bitacoras.toggle') }}" method="POST">
        @csrf
        <button type="submit"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-md shadow text-white
            {{ $config && $config->bitacora_activa ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }}">
            <i class="fas fa-power-off"></i>
            {{ $config && $config->bitacora_activa ? 'Desactivar Bitácora' : 'Activar Bitácora' }}
        </button>
    </form>
</div>
</div>






        <div class="overflow-x-auto mt-4 bg-white rounded-lg shadow">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white text-sm uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Usuario ID</th>
                        <th class="px-4 py-3 text-left">Acción</th>
                        <th class="px-4 py-3 text-left">Tabla</th>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Descripción</th>
                        <th class="px-4 py-3 text-left">Datos Previos</th>
                        <th class="px-4 py-3 text-left">Datos Nuevos</th>
                        <th class="px-4 py-3 text-left">Módulo</th>
                        <th class="px-4 py-3 text-left">Resultado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($bitacoras as $bitacora)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2">{{ $bitacora->BitacoraID }}</td>
                            <td class="px-4 py-2">{{ $bitacora->UsuarioID }}</td>
                            <td class="px-4 py-2">{{ $bitacora->Accion }}</td>
                            <td class="px-4 py-2">{{ $bitacora->TablaAfectada }}</td>
                            <td class="px-4 py-2">{{ $bitacora->FechaAccion }}</td>
                            <td class="px-4 py-2">{{ $bitacora->Descripcion }}</td>
                            <td class="px-4 py-2 whitespace-pre-line">{{ $bitacora->DatosPrevios }}</td>
                            <td class="px-4 py-2 whitespace-pre-line">{{ $bitacora->DatosNuevos }}</td>
                            <td class="px-4 py-2">{{ $bitacora->Modulo }}</td>
                            <td class="px-4 py-2">{{ $bitacora->Resultado }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-gray-500">
                                No hay registros en la bitácora.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
    {{ $bitacoras->links() }}
</div>
        </div>
    </div>
</x-app-layout>