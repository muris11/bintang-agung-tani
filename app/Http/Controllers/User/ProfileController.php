<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

    $user = auth()->user();

    if ($request->hasFile('profile_photo')) {
      if (! empty($user->profile_photo_path)) {
        Storage::disk('public')->delete($user->profile_photo_path);
      }

      $validated['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
    }

    unset($validated['profile_photo']);

    $user->update($validated);

    return redirect()->route('user.profil.show')->with('success', 'Profil berhasil diperbarui.');
  }

  public function destroyPhoto(): RedirectResponse
  {
    $user = auth()->user();

    if (! empty($user->profile_photo_path)) {
      Storage::disk('public')->delete($user->profile_photo_path);
    }

    $user->update([
      'profile_photo_path' => null,
    ]);

    return redirect()->route('user.profil.show')->with('success', 'Foto profil berhasil dihapus.');
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
