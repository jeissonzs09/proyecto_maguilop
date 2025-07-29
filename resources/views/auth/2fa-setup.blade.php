<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Configurar verificación en dos pasos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <p class="mb-4">Escanea este código QR con Google Authenticator o Authy:</p>

                <div class="flex justify-center">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($qrCodeUrl) }}&size=200x200" alt="QR Code">
                </div>

                <p class="mt-4">O ingresa este código manualmente: <strong>{{ $secret }}</strong></p>
            </div>
        </div>
    </div>
</x-app-layout>
