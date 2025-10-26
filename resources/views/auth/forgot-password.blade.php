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
                ¿Olvidaste tu contraseña? No te preocupes. Ingresa tu correo y te enviaremos un enlace para restablecerla.<br>
                <span class="text-white/80 text-xs">
                    Si no recibes el correo, puedes contactar a: 
                    <a href="mailto:maguilop980@gmail.com" class="underline text-blue-300 hover:text-blue-400">maguilop980@gmail.com</a> 
                    o por WhatsApp: 
                    <a href="https://wa.me/50495020203" target="_blank" class="underline text-green-300 hover:text-green-400">+504 9502-0203</a>
                </span>
            </div>

            <!-- Estado de sesión en español -->
            @if (session('status'))
                <div class="mb-4 text-green-300 text-sm text-center">
                    ¡Enlace de recuperación enviado correctamente! Revisa tu correo.
                </div>
            @endif

            <!-- Formulario -->
            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <!-- Correo electrónico -->
                <div>
                    <label for="email" class="block mb-1">Correo electrónico</label>
                    <input id="email" 
                           name="email" 
                           type="email"
                           class="bg-white/20 w-full px-4 py-2 rounded-lg text-white placeholder-white outline-none"
                           placeholder=""
                           value="{{ old('email') }}" 
                           required 
                           autofocus
                           minlength="5" 
                           maxlength="255"
                           oninput="this.value = this.value.trimStart()" />
                    
                    <!-- Errores de backend -->
                    @error('email')
                        <span class="text-white-400 text-sm">{{ $message }}</span>
                    @enderror

                    <!-- Errores dinámicos frontend -->
                    <span id="email-error" class="text-white-400 text-sm"></span>
                </div>

                <!-- Botón -->
                <button type="submit"
                    class="w-full bg-white text-purple-800 font-bold py-2 rounded-full hover:bg-gray-200 transition">
                    Enviar enlace de recuperación
                </button>
            </form>
        </div>
    </div>

    <!-- Script de validación -->
    <script>
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('email-error');

        emailInput.addEventListener('input', function () {
            const value = this.value;

            // Expresión regular para validar correo con dominio
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (value.length < 5) {
                emailError.textContent = "El correo debe tener al menos 5 caracteres.";
            } else if (value.length > 255) {
                emailError.textContent = "El correo no puede tener más de 255 caracteres.";
            } else if (/\s/.test(value)) {
                emailError.textContent = "El correo no debe contener espacios.";
            } else if (!emailRegex.test(value)) {
                emailError.textContent = "El correo debe tener el formato correcto: ejemplo@correo.com";
            } else {
                emailError.textContent = "";
            }
        });
    </script>
</x-guest-layout>