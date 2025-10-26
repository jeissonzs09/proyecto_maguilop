<x-guest-layout> 
    <style>
        body {
            background: url('{{ asset('images/maguilop-fondo.jpg') }}') no-repeat center center fixed;
            background-size: cover;
        }

        .support-link {
            color: #ffffff;
            text-decoration: underline;
            font-weight: 600;
        }

        .support-link:hover {
            color: #d1d1d1;
        }

        .otp-warning {
            color: #ffffff;
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 12px;
            font-weight: 600;
            transition: color 0.5s ease-in-out;
        }

        .otp-warning.expiring {
            color: #ff4d4d; /* rojo alerta */
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 50%, 100% { opacity: 1; }
            25%, 75% { opacity: 0.3; }
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

            <!-- Advertencia OTP -->
            <p id="otpWarning" class="otp-warning">
                ⚠️ El código OTP expira y no debe compartirse con nadie.
            </p>

            <p class="text-sm text-white/80 text-center mb-6">
                Revisa tu correo electrónico. Ingresa el código de 6 dígitos:
            </p>

            <!-- Formulario de Código -->
            <form method="POST" action="{{ route('2fa.code.verify') }}" class="space-y-5" onsubmit="return validarOTP()">
                @csrf

                <div>
                    <label for="code" class="block mb-1">Código</label>
                    <input id="code" name="code" type="text" maxlength="6"
                           class="w-full py-2 px-4 rounded-lg bg-white/20 placeholder-white outline-none text-center tracking-widest"
                           placeholder="" required autofocus
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                    <p id="otpError" class="text-red-400 text-sm mt-1"></p>
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

            <!-- Errores generales -->
            @if ($errors->any())
                <div class="text-red-400 text-sm text-center mt-2">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Botón de reenviar -->
            <form method="POST" action="{{ route('2fa.code.resend') }}" class="mt-4">
                @csrf
                <button type="submit"
                    class="w-full bg-white/20 border border-white/30 text-white font-semibold py-2 rounded-full hover:bg-white/30 transition">
                    Reenviar código
                </button>
            </form>

            <!-- Opción de contactar soporte -->
            <p class="text-center text-sm mt-4">
                ¿No recibiste el correo? Contacta soporte:
                <a href="mailto:maguilop980@gmail.com" class="support-link">Correo</a> o
                <a href="https://wa.me/50495020203" target="_blank" class="support-link">WhatsApp</a>
            </p>
        </div>
    </div>

    <script>
        // Alerta visual cuando el OTP está por expirar
        setTimeout(() => {
            const otpWarning = document.getElementById('otpWarning');
            otpWarning.classList.add('expiring');
        }, 240000); // 4 minutos

        // Validación del OTP antes de enviar
        function validarOTP() {
            const codeInput = document.getElementById('code');
            const otpError = document.getElementById('otpError');
            otpError.textContent = '';

            if (!/^\d{6}$/.test(codeInput.value)) {
                otpError.textContent = '❌ El OTP debe contener 6 números.';
                return false;
            }
            return true;
        }
    </script>
</x-guest-layout>