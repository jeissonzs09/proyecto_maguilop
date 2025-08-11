<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Perfil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Tarjeta: Foto de perfil --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <header class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Foto de perfil</h3>
                    <p class="mt-1 text-sm text-gray-600">Sube una imagen para personalizar tu perfil.</p>
                </header>

                <div class="flex items-center gap-4">
                    {{-- Vista previa de la foto actual --}}
                    <img
                        src="{{ auth()->user()->Foto ? asset('storage/'.auth()->user()->Foto) : asset('images/avatar-default.png') }}"
                        alt="Foto de perfil"
                        class="h-16 w-16 rounded-full ring-1 ring-gray-300 object-cover">

                    {{-- Formulario para actualizar --}}
                    <form action="{{ route('perfil.foto') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-3">
                        @csrf
                        <input type="file" name="foto" accept="image/*"
                               class="block text-sm file:mr-3 file:py-2 file:px-3 file:rounded-lg
                                      file:border-0 file:bg-orange-500 file:text-white hover:file:bg-orange-600">
                        <button class="px-4 py-2 rounded-lg bg-orange-500 text-white hover:bg-orange-600">
                            Guardar
                        </button>
                    </form>
                </div>

                @error('foto')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tarjeta: Información del perfil --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    {{-- Este parcial de Breeze ahora usará tus columnas gracias a los accessors/mutators --}}
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Tarjeta: Cambiar contraseña --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
            </div>
        </div>
    </div>
</x-app-layout>