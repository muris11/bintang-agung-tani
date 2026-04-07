<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = auth()->user();

        return view('user.profil', compact('user'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        auth()->user()->update($validated);

        return redirect()->route('user.profil.show')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Validate current password
        if (! Hash::check($validated['current_password'], auth()->user()->password)) {
            return redirect()->back()->with('error', 'Password saat ini tidak cocok.');
        }

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('user.profil.show')->with('success', 'Password berhasil diubah.');
    }
}
