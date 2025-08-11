<section 
    x-data="{
        currentPassword: '',
        newPassword: '',
        confirmPassword: '',
        showCurrent: false,
        showNew: false,
        showConfirm: false,

        isValidPassword() {
            return /^(?=.*[A-Z])(?=.*\d).{8,}$/.test(this.newPassword);
        },
        passwordsMatch() {
            return this.newPassword === this.confirmPassword;
        }
    }"
>
    <!-- Encabezado -->
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Actualizar contraseña') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Asegúrate de que tu cuenta esté usando una contraseña larga y aleatoria para mantenerla segura.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <!-- Contraseña actual -->
        <div>
            <x-input-label for="update_password_current_password" value="Contraseña actual" />
            <div class="relative">
                <input :type="showCurrent ? 'text' : 'password'" 
                       x-model="currentPassword" 
                       id="update_password_current_password" 
                       name="current_password"
                       class="mt-1 block w-full pr-10 border-gray-300 rounded-md shadow-sm" />
                <button type="button" 
                        @click="showCurrent = !showCurrent" 
                        class="absolute inset-y-0 right-3 flex items-center text-gray-500">
                    👁
                </button>
            </div>
        </div>

        <!-- Nueva contraseña -->
        <div>
            <x-input-label for="update_password_password" value="Nueva contraseña" />
            <div class="relative">
                <input :type="showNew ? 'text' : 'password'" 
                       x-model="newPassword" 
                       id="update_password_password" 
                       name="password"
                       class="mt-1 block w-full pr-10 border-gray-300 rounded-md shadow-sm" />
                <button type="button" 
                        @click="showNew = !showNew" 
                        class="absolute inset-y-0 right-3 flex items-center text-gray-500">
                    👁
                </button>
            </div>
            <p class="text-sm mt-1" 
               :class="isValidPassword() ? 'text-green-600' : 'text-red-600'">
                Debe tener al menos 8 caracteres, 1 mayúscula y 1 número.
            </p>
        </div>

        <!-- Confirmar contraseña -->
        <div>
            <x-input-label for="update_password_password_confirmation" value="Confirmar contraseña" />
            <div class="relative">
                <input :type="showConfirm ? 'text' : 'password'" 
                       x-model="confirmPassword" 
                       id="update_password_password_confirmation" 
                       name="password_confirmation"
                       class="mt-1 block w-full pr-10 border-gray-300 rounded-md shadow-sm" />
                <button type="button" 
                        @click="showConfirm = !showConfirm" 
                        class="absolute inset-y-0 right-3 flex items-center text-gray-500">
                    👁
                </button>
            </div>
            <p class="text-sm mt-1" 
               :class="passwordsMatch() ? 'text-green-600' : 'text-red-600'">
                Las contraseñas deben coincidir.
            </p>
        </div>

<!-- Botón y mensaje de éxito -->
<div class="flex items-center gap-4">
    <x-primary-button 
        class="bg-orange-500 hover:bg-orange-600 focus:ring-orange-400 inline-flex w-auto"
        x-bind:disabled="!isValidPassword() || !passwordsMatch() || !currentPassword"
        x-bind:class="(!isValidPassword() || !passwordsMatch() || !currentPassword) ? 'opacity-50 cursor-not-allowed' : ''">
        Guardar
    </x-primary-button>

    @if (session('status') === 'password-updated')
        <p class="text-sm text-green-600 font-semibold border border-green-400 bg-green-50 px-3 py-2 rounded-lg">
            Contraseña actualizada correctamente.
        </p>
    @endif
</div>
    </form>
</section>