<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->is_admin) {
                return redirect('/admin/dashboard');
            }

            return redirect('/user/dashboard');
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau password salah.',
        ]);
    }

    /**
     * Show registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'terms' => ['accepted'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => false,
        ]);

        Auth::login($user);

        return redirect('/user/dashboard');
    }

    /**
     * Handle logout request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Show forgot password form.
     *
     * @return \Illuminate\View\View
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email tidak terdaftar dalam sistem kami.']);
        }

        // Generate reset token
        $token = Str::random(64);

        // Store token in password_resets table
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Build reset URL (direct link without email for now)
        $resetUrl = url('/reset-password/'.$token.'?email='.urlencode($request->email));

        // For now, we'll show the link in flash message
        // In production, you should send this via email
        return back()
            ->with('status', 'Link reset password telah dibuat. Silakan cek email Anda.')
            ->with('reset_url', $resetUrl)
            ->with('reset_token', $token)
            ->with('reset_email', $request->email);
    }

    /**
     * Show reset password form.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showResetPassword(Request $request, string $token)
    {
        $email = $request->query('email');

        if (! $email) {
            return redirect('/forgot-password')
                ->withErrors(['email' => 'Link reset password tidak valid.']);
        }

        // Verify token exists
        $resetRecord = \DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (! $resetRecord) {
            return redirect('/forgot-password')
                ->withErrors(['email' => 'Link reset password tidak valid atau sudah kedaluwarsa.']);
        }

        // Check if token is valid (not expired, within 60 minutes)
        $createdAt = \Carbon\Carbon::parse($resetRecord->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            return redirect('/forgot-password')
                ->withErrors(['email' => 'Link reset password sudah kedaluwarsa. Silakan minta link baru.']);
        }

        // Verify token hash
        if (! Hash::check($token, $resetRecord->token)) {
            return redirect('/forgot-password')
                ->withErrors(['email' => 'Link reset password tidak valid.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Handle reset password request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        // Find the reset token record
        $resetRecord = \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $resetRecord) {
            return back()
                ->withErrors(['email' => 'Link reset password tidak valid.']);
        }

        // Check token validity
        if (! Hash::check($request->token, $resetRecord->token)) {
            return back()
                ->withErrors(['email' => 'Link reset password tidak valid.']);
        }

        // Check expiration
        $createdAt = \Carbon\Carbon::parse($resetRecord->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            return redirect('/forgot-password')
                ->withErrors(['email' => 'Link reset password sudah kedaluwarsa. Silakan minta link baru.']);
        }

        // Update user password
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()
                ->withErrors(['email' => 'User tidak ditemukan.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the reset token
        \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // Log the user in
        Auth::login($user);

        return redirect('/user/dashboard')
            ->with('success', 'Password berhasil direset. Selamat datang kembali!');
    }
}
