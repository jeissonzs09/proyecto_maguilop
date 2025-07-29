<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">Asignación de Permisos por Rol y Módulo</h2>
    </x-slot>

    <div class="p-4">
        @if(session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded-md mb-4 shadow">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('roles.permisos.guardar') }}">
            @csrf

            <div class="overflow-x-auto">
                <table class="table-auto w-full mt-4 border rounded-lg shadow text-sm">
                    <thead class="bg-orange-600 text-white">
    <tr>
        <th class="border px-4 py-2 text-left">Módulo</th>
        @foreach ($roles as $rol)
            <th colspan="4" class="text-center border px-2 py-2">{{ $rol->Descripcion }}</th>
        @endforeach
    </tr>
    <tr>
        <th class="border px-4 py-2"></th>
        @foreach ($roles as $rol)
            <th class="border px-2 py-2 text-center">Ver</th>
            <th class="border px-2 py-2 text-center">Crear</th>
            <th class="border px-2 py-2 text-center">Editar</th>
            <th class="border px-2 py-2 text-center">Eliminar</th>
        @endforeach
    </tr>
</thead>

                    <tbody>
                        @foreach ($modulos as $modulo)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-4 py-2 font-semibold">{{ $modulo }}</td>
                                @foreach ($roles as $rol)
                                    @php
                                        $permiso = $permisos[$rol->ID_Rol][$modulo] ?? ['ver' => 0, 'crear' => 0, 'editar' => 0, 'eliminar' => 0];
                                    @endphp

                                    @foreach (['ver', 'crear', 'editar', 'eliminar'] as $accion)
                                        <td class="border px-2 py-2 text-center">
                                            <input type="hidden" name="permisos[{{ $rol->ID_Rol }}][{{ $modulo }}][{{ $accion }}]" value="0">
                                            <input 
                                                type="checkbox" 
                                                name="permisos[{{ $rol->ID_Rol }}][{{ $modulo }}][{{ $accion }}]" 
                                                value="1" 
                                                {{ $permiso[$accion] ? 'checked' : '' }}
                                                class="form-checkbox h-5 w-5 text-blue-600 rounded-full"
                                            >
                                        </td>
                                    @endforeach
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 text-right">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg shadow transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</x-app-layout>



