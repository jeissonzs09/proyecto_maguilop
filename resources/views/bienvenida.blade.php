<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-handshake"></i> Bienvenido
        </h2>
    </x-slot>

    <div class="p-8 flex flex-col items-center justify-center text-center bg-gray-100 rounded-lg shadow-inner min-h-[70vh]">
        <div class="bg-white p-10 rounded-2xl shadow-xl max-w-2xl w-full">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">¡Bienvenido a Maguilop!</h1>
            <p class="text-gray-600 mb-6 text-lg">
                Sistema integral de gestion empresarial.  
                Accede al panel de control o explora las secciones disponibles segun tus permisos.
            </p>

            @auth
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-md shadow-md text-lg font-semibold">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesion
                </a>
            @endauth
        </div>
    </div>

    @php
        $toastType = session('error') ? 'error' : (session('success') ? 'success' : null);
        $toastMsg  = session('error') ?: session('success');
    @endphp

    @if($toastType)
        <div
            id="toast-bienvenida"
            role="status"
            aria-live="polite"
            class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                   text-white px-10 py-6 rounded-full shadow-2xl flex items-center gap-5
                   z-50 animate-fadeIn text-xl font-semibold ring-1 ring-white/20
                   max-w-[90vw]"
            style="min-width: 420px; background-color: {{ $toastType === 'error' ? '#dc2626' : '#16a34a' }};"
            onclick="this.remove()"
        >
            @if($toastType === 'error')
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="15" y1="9" x2="9" y2="15" />
                    <line x1="9" y1="9" x2="15" y2="15" />
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M9 12l2 2l4-4" />
                </svg>
            @endif

            <span class="leading-snug break-words">{{ $toastMsg }}</span>
        </div>

        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast-bienvenida');
                if (toast) {
                    toast.style.transition = 'opacity .5s ease, transform .5s ease';
                    toast.style.opacity = '0';
                    toast.style.transform = 'translate(-50%, -50%) scale(.95)';
                    setTimeout(() => toast.remove(), 500);
                }
            }, 3200);
        </script>

        <style>
            @keyframes fadeIn {
                from { opacity: 0; transform: translate(-50%, -48%) scale(.97); }
                to   { opacity: 1; transform: translate(-50%, -50%) scale(1); }
            }
            .animate-fadeIn { animation: fadeIn .28s ease forwards; }
        </style>
    @endif
</x-app-layout>