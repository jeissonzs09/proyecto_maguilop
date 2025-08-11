<x-guest-layout>
    <style>
        body {
            background: url('{{ asset('images/maguilop-fondo.jpg') }}') no-repeat center center fixed;
            background-size: cover;
        }
    </style>

    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="backdrop-blur-lg bg-white/10 border border-white/30 shadow-2xl rounded-2xl p-8 w-full max-w-md text-white"
             x-data="{
                 password: '',
                 confirmPassword: '',
                 showPassword: false,
                 showConfirm: false,
                 isValidPassword() {
                     return /^(?=.*[A-Z])(?=.*\d).{8,}$/.test(this.password);
                 },
                 passwordsMatch() {
                     return this.password === this.confirmPassword;
                 }
             }">

            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/logo-maguilop.png') }}" alt="Maguilop Logo" class="h-16 w-auto">
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                @csrf

                <!-- Token -->
                <input type="hidden" name="token" value="{{ request()->route('token') }}">

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Correo electrónico')" class="text-white mb-1"/>
                    <x-text-input id="email" type="email" name="email"
                                  :value="request()->query('email')"
                                  class="bg-white/20 w-full py-2 px-3 rounded-lg text-white placeholder-white outline-none"
                                  required autofocus autocomplete="username" placeholder="Correo electrónico"
                                  readonly />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-200"/>
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Nueva contraseña')" class="text-white mb-1"/>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'"
                               x-model="password"
                               id="password" name="password"
                               class="bg-white/20 w-full py-2 px-3 rounded-lg text-white placeholder-white outline-none pr-10"
                               required autocomplete="new-password" placeholder="Nueva contraseña" />
                        <button type="button" @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-3 flex items-center text-white">
                            👁
                        </button>
                    </div>
                    <p class="text-sm mt-1 font-semibold"
   :class="isValidPassword() ? 'text-green-700' : 'text-red-700'">
    Debe tener al menos 8 caracteres, 1 mayúscula y 1 número.
</p>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-200"/>
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" class="text-white mb-1"/>
                    <div class="relative">
                        <input :type="showConfirm ? 'text' : 'password'"
                               x-model="confirmPassword"
                               id="password_confirmation" name="password_confirmation"
                               class="bg-white/20 w-full py-2 px-3 rounded-lg text-white placeholder-white outline-none pr-10"
                               required autocomplete="new-password" placeholder="Confirmar contraseña" />
                        <button type="button" @click="showConfirm = !showConfirm"
                                class="absolute inset-y-0 right-3 flex items-center text-white">
                            👁
                        </button>
                    </div>
                    
<p class="text-sm mt-1 font-semibold"
   :class="passwordsMatch() ? 'text-green-700' : 'text-red-700'">
    Las contraseñas deben coincidir.
</p>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-200"/>
                </div>

                <!-- Submit -->
                <button type="submit"
                        :disabled="!isValidPassword() || !passwordsMatch()"
                        :class="(!isValidPassword() || !passwordsMatch()) ? 'opacity-50 cursor-not-allowed' : ''"
                        class="w-full bg-white text-purple-800 font-bold py-2 rounded-full hover:bg-gray-200 transition">
                    Restablecer Contraseña
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>