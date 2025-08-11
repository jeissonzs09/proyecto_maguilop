<x-mail::message>
@component('mail::message')
{{-- Logo centrado --}}
<p style="text-align:center;">
    <img src="{{ asset('images/maguilop-logo.png') }}" alt="Maguilop" style="width:150px;">
</p>

# ¡Hola {{ $usuario->NombreUsuario }}!

Te damos la bienvenida a **MAGUILOP**.  
Ya hemos creado tu cuenta en el sistema.

Para entrar por primera vez, por favor crea tu contraseña haciendo clic en el botón de abajo.

@component('mail::button', ['url' => $url, 'color' => 'success'])
Crear contraseña
@endcomponent

Si no esperabas este correo, puedes ignorarlo.

Gracias,  
**El equipo de MAGUILOP**
@endcomponent
</x-mail::message>