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

            <!-- Encabezado -->
            <h2 class="text-xl font-semibold text-center mb-2 flex items-center justify-center gap-1">
                Verificación en dos pasos
            </h2>

            <p class="text-sm text-white/80 text-center mb-6">
                Revisa tu correo electrónico. Ingresa el código de 6 dígitos:
            </p>

            <!-- Formulario de Código -->
            <form method="POST" action="{{ route('2fa.code.verify') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="code" class="block mb-1">Código</label>
                    <input id="code" name="code" type="text" maxlength="6"
                           class="w-full py-2 px-4 rounded-lg bg-white/20 placeholder-white outline-none text-center tracking-widest"
                           placeholder="" required autofocus />
                    @if ($errors->has('code'))
                        <p class="text-red-400 text-sm mt-1">{{ $errors->first('code') }}</p>
                    @endif
                </div>

                <button type="submit"
                    class="w-full bg-white text-purple-800 font-bold py-2 rounded-full hover:bg-gray-200 transition">
                    Verificar
                </button>
            </form>

            <!-- Estado del código reenviado -->
            @if (session('status'))
                <p class="text-green-400 text-center mt-4 text-sm">{{ session('status') }}</p>
            @endif

            <!-- Botón de reenviar -->
            <form method="POST" action="{{ route('2fa.code.resend') }}" class="mt-4">
                @csrf
                <button type="submit"
                    class="w-full bg-white/20 border border-white/30 text-white font-semibold py-2 rounded-full hover:bg-white/30 transition">
                    Reenviar código
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>

