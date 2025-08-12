<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Ayuda y Manuales
        </h2>
    </x-slot>

    <div class="container mx-auto p-4">
        <h3 class="text-lg font-semibold mb-4">Manual de Usuario</h3>
        <p>Descarga o consulta el manual de usuario desde el siguiente enlace:</p>
        <a href="{{ asset('manuales/Manual de Usuario - Sistema Maguilop.pdf') }}" target="_blank" class="text-blue-600 hover:underline">
            Descargar Manual PDF
        </a>

        <hr class="my-6">

        <h3 class="text-lg font-semibold mb-4">Video Tutorial</h3>
        <p>Mira el video tutorial para aprender a usar el sistema:</p>
        <a href="https://www.youtube.com/watch?v=O6Nbp7hM0Uc&list=PLP9b0NCNevAY_KpnvPlFCSPHfA82Kffhn&pp=gAQB" target="_blank" class="text-blue-600 hover:underline">
            Ver video en YouTube
        </a>

        <div class="mt-4">
    <iframe width="560" height="315" 
        src="https://www.youtube.com/embed/O6Nbp7hM0Uc?list=PLP9b0NCNevAY_KpnvPlFCSPHfA82Kffhn" 
        title="Video tutorial" frameborder="0" allowfullscreen></iframe>
</div>

    </div>
</x-app-layout>