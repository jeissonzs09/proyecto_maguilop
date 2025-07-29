<x-guest-layout>
    <style>
        body {
            background: url('{{ asset('images/maguilop-fondo.jpg') }}') no-repeat center center fixed;
            background-size: cover;
        }
    </style>

    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="backdrop-blur-lg bg-white/10 border border-white/30 shadow-2xl rounded-2xl p-8 w-full max-w-md text-white">

            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/logo-maguilop.png') }}" alt="Maguilop Logo"
                     class="h-16 w-auto rounded-md mix-blend-multiply">
            </div>

            <!-- Mensaje -->
            <div class="text-sm text-center mb-4">
                ¿Olvidaste tu contraseña? No te preocupes. Ingresa tu correo y te enviaremos un enlace para restablecerla.
            </div>

            <!-- Estado de sesión -->
            <x-auth-session-status class="mb-4 text-green-300 text-sm" :status="session('status')" />

            <!-- Formulario -->
            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <!-- Correo electrónico -->
                <div>
                    <label for="email" class="block mb-1">Correo electrónico</label>
                    <input id="email" name="email" type="email"
                        class="bg-white/20 w-full px-4 py-2 rounded-lg text-white placeholder-white outline-none"
                        placeholder=""
                        value="{{ old('email') }}" required autofocus />
                    @error('email')
                        <span class="text-red-400 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Botón -->
                <button type="submit"
                    class="w-full bg-white text-purple-800 font-bold py-2 rounded-full hover:bg-gray-200 transition">
                    Enviar enlace de recuperación
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>

