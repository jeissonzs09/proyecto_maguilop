<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Listeners\LogSuccessfulLogin;


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
    ResetPassword::toMailUsing(function ($notifiable, $token) {
        return (new MailMessage)
            ->subject('Restablecimiento de contraseña')
            ->line('Has recibido este correo porque se solicitó un restablecimiento de contraseña para tu cuenta.')
            ->action('Restablecer contraseña', url("/reset-password/{$token}?email={$notifiable->CorreoElectronico}"))
            ->line('Si no solicitaste un restablecimiento, no se requiere ninguna acción.');
    });

    //  escuchar logins exitosos
    Event::listen(
        Login::class,
        [LogSuccessfulLogin::class, 'handle']
    );
}
}
