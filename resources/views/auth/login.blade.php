<x-guest-layout> 
    <style>
        body {
            background: url('{{ asset('images/maguilop-fondo.jpg') }}') no-repeat center center fixed;
            background-size: cover;
        }

        /* Mensajes de error/advertencia */
        .error-message {
            color: #ffffffff; /* Blanco */
            font-size: 0.9rem;
            margin-top: 5px;
            font-weight: 600;
        }

        /* Mensajes del backend */
        .server-error {
            color: #ffffffff; /* Blanco */
            font-size: 0.9rem;
            text-align: center;
        }
    </style>

    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="backdrop-blur-lg bg-white/10 border border-white/30 shadow-2xl rounded-2xl p-8 w-full max-w-md text-white">
            <!-- Encabezado -->
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/logo-maguilop.png') }}" alt="Maguilop Logo" class="h-16 w-auto">
            </div>

            <!-- Formulario -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5" onsubmit="return validarFormulario()">
                @csrf

                <!-- Usuario -->
                <div>
                    <label for="NombreUsuario" class="block mb-1">Nombre de Usuario</label>
                    <div class="flex items-center bg-white/20 rounded-lg px-3">
                        <svg class="w-5 h-5 text-white opacity-70 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5.121 17.804A6 6 0 0112 15a6 6 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <input id="NombreUsuario" name="NombreUsuario" type="text"
                               class="bg-transparent w-full py-2 outline-none placeholder-white"
                               placeholder="Nombre de Usuario" maxlength="50" required autofocus />
                    </div>
                    <p id="usuarioError" class="error-message"></p>
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="block mb-1">Contraseña</label>
                    <div class="flex items-center bg-white/20 rounded-lg px-3 relative">
                        <svg class="w-5 h-5 text-white opacity-70 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m0 0v2m0-2h2m-2 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <input id="password" name="password" type="password"
                               class="bg-transparent w-full py-2 outline-none placeholder-white pr-10"
                               placeholder="Contraseña" required maxlength="50" />
                        <!-- Ojito -->
                        <button type="button" onclick="togglePassword()" class="absolute right-3 text-white opacity-70 focus:outline-none">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path id="eyePath" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 
                                      9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 
                                      0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <p id="passwordError" class="error-message"></p>
                </div>

                <!-- Recordar y enlace -->
                <div class="flex justify-between text-sm opacity-90">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="remember" class="form-checkbox text-indigo-500 mr-2">
                        Recordar
                    </label>
                    <a href="{{ route('password.request') }}" class="hover:underline">¿Olvidaste tu contraseña?</a>
                </div>

                <!-- Botón -->
                <button type="submit"
                    class="w-full bg-white text-purple-800 font-bold py-2 rounded-full hover:bg-gray-200 transition">
                    Iniciar Sesión
                </button>

                <!-- Mensajes del servidor -->
                @if ($errors->any())
                    <div class="mt-3 server-error">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Mostrar/Ocultar contraseña
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            eyeIcon.innerHTML = isHidden
                ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 
                    0-8.268-2.943-9.542-7a9.961 
                    9.961 0 012.155-3.362m2.386-2.386A9.961 
                    9.961 0 0112 5c4.477 0 8.268 2.943 
                    9.542 7a9.969 9.969 0 01-4.043 
                    5.092M15 12a3 3 0 11-6 0 3 3 0 
                    016 0z" /><path stroke-linecap="round" stroke-linejoin="round" 
                    stroke-width="2" d="M3 3l18 18" />`
                : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 
                    016 0z" /><path stroke-linecap="round" stroke-linejoin="round" 
                    stroke-width="2" d="M2.458 12C3.732 7.943 7.523 
                    5 12 5c4.477 0 8.268 2.943 
                    9.542 7-1.274 4.057-5.065 7-9.542 
                    7-4.477 0-8.268-2.943-9.542-7z" />`;
        }

        // Validación de formulario
        function validarFormulario() {
            const usuario = document.getElementById('NombreUsuario').value.trim();
            const password = document.getElementById('password').value;
            const usuarioError = document.getElementById('usuarioError');
            const passwordError = document.getElementById('passwordError');

            usuarioError.textContent = '';
            passwordError.textContent = '';

            // Validar usuario
            if (/\s/.test(usuario)) {
                usuarioError.textContent = '⚠️ El campo de usuario no admite espacios.';
                return false;
            }
            if (usuario.length < 3) {
                usuarioError.textContent = '⚠️ El usuario debe tener al menos 3 caracteres.';
                return false;
            }
            if (usuario.length > 50) {
                usuarioError.textContent = '⚠️ El usuario no puede superar los 50 caracteres.';
                return false;
            }
            const formato = /^[a-zA-Z0-9._-]+$/;
            if (!formato.test(usuario)) {
                usuarioError.textContent = '⚠️ El usuario ingresado tiene un formato inválido.';
                return false;
            }

            // Validar contraseña
            if (/\s/.test(password)) {
                passwordError.textContent = '⚠️ La contraseña no puede contener espacios.';
                return false;
            }
            if (password.length < 6) {
                passwordError.textContent = '⚠️ La contraseña debe tener al menos 6 caracteres.';
                return false;
            }
            if (password.length > 50) {
                passwordError.textContent = '⚠️ La contraseña no puede superar los 50 caracteres.';
                return false;
            }

            return true;
        }

        // Limitar al escribir en la contraseña
        const passwordInput = document.getElementById('password');
        passwordInput.addEventListener('input', function() {
            if (this.value.length > 50) {
                this.value = this.value.slice(0,50);
            }
            if (/\s/.test(this.value)) {
                this.value = this.value.replace(/\s/g,'');
            }
        });
    </script>
</x-guest-layout>