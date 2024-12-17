<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\UserId;

class UsersController extends Controller
{
    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        // Validate the input
        $request->validate([
            'login-email' => 'required|email',
            'login-password' => 'required|min:6',
        ], [
            'login-email.required' => 'Email wajib diisi.',
            'login-email.email' => 'Format email tidak valid.',
            'login-password.required' => 'Password wajib diisi.',
            'login-password.min' => 'Password minimal harus :min karakter.',
        ]);

        // Extract login credentials
        $credentials = [
            'email' => $request->input('login-email'),
            'password' => $request->input('login-password'),
        ];

        if (Auth::attempt($credentials)) {
            // Authentication passed, redirect to intended page or dashboard
            return redirect()->back()->with('success', 'Login berhasil!');
        }

        // Authentication failed
        return back()->withErrors([
            'login' => 'Email atau password yang Anda masukan salah.',
        ])->withInput($request->except('login-password'));
    }

    /**
     * Handle the registration request.
     */
    public function register(Request $request)
    {
        // Validate the form input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan, silakan gunakan email lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus :min karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        try {
            // Create the user
            $user = UserId::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Log in the user after registration
            Auth::login($user);

            return redirect('/vehicle_list')->with('success', 'Registrasi berhasil! Selamat datang, ' . $user->name . '.');
        } catch (\Exception $e) {
            // Log the exception if necessary (optional)
            // \Log::error('Registration Error: ' . $e->getMessage());

            return back()->withErrors([
                'register' => 'Terjadi kesalahan saat melakukan registrasi. Silakan coba lagi nanti.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah berhasil logout!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'current_password' => 'nullable|min:6',
            'new_password' => 'nullable|min:6|confirmed',
        ]);

        $user = Auth::user();
        dd($request->all());

        // Update name
        $user->name = $request->name;

        // Handle profile photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile_photos', 'public');
            $user->photo = $path;
        }

        // Update password if current_password and new_password are provided
        if ($request->filled('current_password') && Hash::check($request->current_password, $user->password)) {
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}
