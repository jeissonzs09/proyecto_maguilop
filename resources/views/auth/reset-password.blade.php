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
                                  :value="old('email', request()->email)"
                                  class="bg-white/20 w-full py-2 px-3 rounded-lg text-white placeholder-white outline-none"
                                  required autofocus autocomplete="username" placeholder="Correo electrónico" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-200"/>
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Contraseña')" class="text-white mb-1"/>
                    <x-text-input id="password" type="password" name="password"
                                  class="bg-white/20 w-full py-2 px-3 rounded-lg text-white placeholder-white outline-none"
                                  required autocomplete="new-password" placeholder="Nueva contraseña" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-200"/>
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" class="text-white mb-1"/>
                    <x-text-input id="password_confirmation" type="password" name="password_confirmation"
                                  class="bg-white/20 w-full py-2 px-3 rounded-lg text-white placeholder-white outline-none"
                                  required autocomplete="new-password" placeholder="Confirmar contraseña" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-200"/>
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="w-full bg-white text-purple-800 font-bold py-2 rounded-full hover:bg-gray-200 transition">
                    Restablecer Contraseña
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>