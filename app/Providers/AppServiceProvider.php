<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;

use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogLogout;
use App\Listeners\LogFailedLogin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Personalizar email de restablecimiento de contraseña
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            return (new MailMessage)
                ->subject('Restablecimiento de contraseña')
                ->line('Has recibido este correo porque se solicitó un restablecimiento de contraseña para tu cuenta.')
                ->action('Restablecer contraseña', url("/reset-password/{$token}?email={$notifiable->CorreoElectronico}"))
                ->line('Si no solicitaste un restablecimiento, no se requiere ninguna acción.');
        });

        // 🔹 Escuchar login exitoso
        Event::listen(
            Login::class,
            [LogSuccessfulLogin::class, 'handle']
        );

        // 🔹 Escuchar logout
        Event::listen(
            Logout::class,
            [LogLogout::class, 'handle']
        );

        // 🔹 Escuchar login fallido
        Event::listen(
            Failed::class,
            [LogFailedLogin::class, 'handle']
        );
    }
}