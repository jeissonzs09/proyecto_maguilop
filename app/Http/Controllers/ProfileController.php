<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */

public function update(ProfileUpdateRequest $request): RedirectResponse
{
    $user = $request->user();

    // Mapear los campos del form a tu tabla `usuario`
    $user->NombreUsuario     = $request->input('name');
    $user->CorreoElectronico = $request->input('email');
    // si también tienes columna `email` en la tabla, sincronízala:
    if ($user->getAttribute('email') !== null || array_key_exists('email', $user->getAttributes())) {
        $user->email = $request->input('email');
    }

    $user->save();

    return Redirect::route('profile.edit')->with('status', 'profile-updated');
}


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

// ...
/**
 * Update user's avatar (Foto).
 */
public function updateAvatar(Request $request): RedirectResponse
{
    $request->validate([
        'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $user = $request->user();

    // Borra la foto anterior si existe
    if ($user->Foto && Storage::disk('public')->exists($user->Foto)) {
        Storage::disk('public')->delete($user->Foto);
    }

    // Guarda nueva foto en storage/app/public/avatars
    $path = $request->file('foto')->store('avatars', 'public');

    // Actualiza BD (columna Foto)
    $user->Foto = $path;
    $user->save();

    return back()->with('success', 'Foto de perfil actualizada.');
}

}