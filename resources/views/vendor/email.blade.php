<x-mail::message>
{{-- HEADER CON LOGO Y SLOGAN --}}
<div style="text-align:center; margin-bottom:25px; background-color:#ffffff; padding:20px; border-radius:10px;">
    <img src="{{ asset('images/logo-maguilop.png') }}" alt="Maguilop Logo" style="height:100px; object-fit:contain; margin-bottom:10px;">
    <div style="background-color:#ff6f00; color:#ffffff; font-size:16px; font-weight:600; display:inline-block; padding:6px 18px; border-radius:6px;">
        Soluciones en electrodomésticos
    </div>
</div>

{{-- INTRODUCCIÓN --}}
@foreach ($introLines as $line)
<p style="font-family:'Segoe UI', Arial, sans-serif; color:#333333; font-size:16px; line-height:1.7; margin-bottom:15px;">
    {{ $line }}
</p>
@endforeach

{{-- BOTÓN FUNCIONAL --}}
@isset($actionText)
<div style="text-align:center; margin:35px 0;">
    <a href="{{ $actionUrl }}" 
       style="
            background-color:#ff6f00;
            color:#ffffff;
            padding:14px 45px;
            border-radius:8px;
            font-weight:bold;
            font-size:17px;
            text-transform:uppercase;
            letter-spacing:0.5px;
            text-decoration:none;
            font-family:'Segoe UI', Arial, sans-serif;
            display:inline-block;
            box-shadow:0px 4px 10px rgba(0,0,0,0.15);
            transition:all 0.3s ease;
       "
       onmouseover="this.style.backgroundColor='#e65c00'; this.style.cursor='pointer';"
       onmouseout="this.style.backgroundColor='#ff6f00';"
    >
        {{ $actionText }}
    </a>
</div>
@endisset

{{-- INSTRUCCIONES DE SEGURIDAD --}}
<p style="font-family:'Segoe UI', Arial, sans-serif; color:#333333; font-size:15px; line-height:1.8;">
    Este enlace es único y temporal. Por tu seguridad:
</p>
<ul style="text-align:left; display:inline-block; margin-bottom:25px; font-size:15px; color:#333333;">
    <li>No compartas este enlace con nadie.</li>
    <li>Tu nueva contraseña debe tener al menos 8 caracteres y un carácter especial.</li>
    <li>No reutilices una contraseña anterior.</li>
</ul>
<p style="font-family:'Segoe UI', Arial, sans-serif; color:#333333; font-size:15px; line-height:1.7;">
    Si no realizaste esta solicitud, puedes ignorar este correo y tu contraseña permanecerá segura.
</p>

{{-- SOPORTE Y CONTACTO --}}
<div style="background-color:#ffefeb; border-radius:8px; padding:20px; margin-top:25px;">
    <p style="font-family:'Segoe UI', Arial, sans-serif; color:#333333; font-size:15px; line-height:1.7;">
        Si tienes algún problema o no recibes el correo, comunícate con nuestro equipo de soporte:
    </p>
    <p style="font-family:'Segoe UI', Arial, sans-serif; color:#333333; font-size:15px; line-height:1.7; text-align:center;">
        📧 <strong>Correo:</strong> <a href="mailto:maguilop2397292@yahoo.com" style="color:#ff6f00;">maguilop2397292@yahoo.com</a><br>
        ☎ <strong>Teléfono:</strong> 2239-7292<br>
        💬 <strong>WhatsApp:</strong> <a href="https://wa.me/50495020203" style="color:#ff6f00;">+504-95020203</a><br>
        🕐 <strong>Horario:</strong> 8:00 AM – 5:00 PM
    </p>
</div>

{{-- DESPEDIDA --}}
@if (!empty($salutation))
<p style="font-family:'Segoe UI', Arial, sans-serif; color:#333333; font-size:15px;">
    {{ $salutation }}
</p>
@else
<p style="font-family:'Segoe UI', Arial, sans-serif; color:#333333; font-size:15px;">
    Atentamente,<br>
    <strong>El equipo de Maguilop</strong>
</p>
@endif

{{-- SUBCOPY --}}
@isset($actionText)
<x-slot:subcopy>
<p style="font-family:'Segoe UI', Arial, sans-serif; font-size:12px; color:#999999;">
    Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
    <a href="{{ $actionUrl }}" style="color:#ff6f00; word-break:break-all;">{{ $displayableActionUrl }}</a>
</p>
</x-slot:subcopy>
@endisset

{{-- FOOTER --}}
<div style="margin-top:25px; font-size:13px; color:#555555; text-align:center; line-height:1.5;">
    © 2025 <strong>Maguilop</strong>. Todos los derechos reservados.
    <div style="margin-top:8px;">
        <a href="{{ url('/contacto') }}" style="color:#ff6f00; text-decoration:none;">Contacto</a> |
        <a href="{{ url('/politica-privacidad') }}" style="color:#ff6f00; text-decoration:none;">Política de privacidad</a>
    </div>
</div>
</x-mail::message>