<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function index(): View
    {
        $addresses = auth()->user()->addresses()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();

        return view('user.alamat', compact('addresses'));
    }

    public function store(StoreAddressRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        // If this is set as default, unset other defaults
        if (! empty($validated['is_default'])) {
            auth()->user()->addresses()->where('is_default', true)->update(['is_default' => false]);
        }

        // If this is the first address, make it default
        if (auth()->user()->addresses()->count() === 0) {
            $validated['is_default'] = true;
        }

        Address::create($validated);

        return redirect()->route('user.alamat.index')->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function update(StoreAddressRequest $request, Address $address): RedirectResponse
    {
        // Check authorization
        if ($address->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah alamat ini.');
        }

        $validated = $request->validated();

        // If this is set as default, unset other defaults
        if (! empty($validated['is_default']) && ! $address->is_default) {
            auth()->user()->addresses()->where('is_default', true)->update(['is_default' => false]);
        }

        $address->update($validated);

        return redirect()->route('user.alamat.index')->with('success', 'Alamat berhasil diperbarui.');
    }

    public function destroy(Address $address): RedirectResponse
    {
        // Check authorization
        if ($address->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus alamat ini.');
        }

        $wasDefault = $address->is_default;
        $address->delete();

        // If the deleted address was default, set another as default if exists
        if ($wasDefault) {
            $newDefault = auth()->user()->addresses()->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        return redirect()->route('user.alamat.index')->with('success', 'Alamat berhasil dihapus.');
    }

    public function setDefault(Address $address): RedirectResponse
    {
        // Check authorization
        if ($address->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah alamat ini.');
        }

        // Unset other defaults
        auth()->user()->addresses()->where('is_default', true)->update(['is_default' => false]);

        // Set this as default
        $address->update(['is_default' => true]);

        return redirect()->route('user.alamat.index')->with('success', 'Alamat utama berhasil diubah.');
    }
}
