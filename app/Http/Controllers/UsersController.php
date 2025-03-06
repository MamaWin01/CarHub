<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\UserId;
use App\Models\Chat;
use App\Models\VerificationCode;
use App\Models\Config;
use App\Services\StreamChatService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

class UsersController extends Controller
{
    private $streamChat;

    public function __construct(StreamChatService $streamChat)
    {
        $this->streamChat = $streamChat;
    }

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
            'code' => 'required',
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
            $config = Config::where('name', 'isEmailVerify')->first()->value;
            if($config) {
                $code = VerificationCode::where('email', $request->email)->where('code', $request->code)->first();
                if(!$code) {
                    return back()->withErrors([
                        'code' => 'Kode verifikasi salah.',
                    ])->withInput($request->except('password', 'password_confirmation'));
                }

                $code->delete();
            }
            // Create the user
            $user = UserId::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            Auth::login($user);

            $this->streamChat->createUser(strval($user->id), $request->name, $request->email);
            Chat::Create([
                'user_id' => $user->id,
                'user_name' => $request->name,
                'channel_id' => $user->id,
                'unread_count' => 0
            ]);

            return redirect('/vehicle/vehicle_list')->with('success', 'Registrasi berhasil! Selamat datang, ' . $user->name . '.');
        } catch (\Exception $e) {
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
        try {
            $user = Auth::user();

            // Validation
            $request->validate([
                'name' => 'required|string|max:255',
                'photo' => 'nullable|image',
                'current_password' => 'nullable|min:6',
                'new_password' => 'nullable|min:6|confirmed',
            ], [
                'name.required' => 'Nama wajib diisi.',
                'name.string' => 'Nama harus berupa teks.',
                'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
                'new_password.min' => 'Password minimal harus :min karakter.',
                'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
            ]);

            if (!$user) {
                return back()->with('error', 'You are not logged in.');
            }

            // Update name
            $oldName = $user->name;
            $user->name = $request->name;

            // Handle profile photo upload
            if ($request->hasFile('photo')) {
                if ($oldName != $request->name) {
                    $name = $request->name;
                } else {
                    $name = $user->name;
                }

                // Upload new photo
                $path = $request->file('photo')->storeAs(
                    'images/profile_photos',
                    $user->id . '_' . $name . '.png',
                    'public'
                );

                // Delete the old file if the name changed
                if ($oldName != $request->name) {
                    $oldPath = 'images/profile_photos/' . $user->id . '_' . $oldName . '.png';
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
            } else {
                // Rename the file if only the name changes and no new file is uploaded
                if ($oldName != $request->name) {
                    $oldPath = 'images/profile_photos/' . $user->id . '_' . $oldName . '.png';
                    $newPath = 'images/profile_photos/' . $user->id . '_' . $request->name . '.png';

                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->move($oldPath, $newPath);
                    }
                }
            }

            // Update password if required
            if ($request->filled('current_password') && Hash::check($request->current_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
            }

            $user->save();

            return back()->with('update-success', 'Profile berhasil di update!');
        } catch (\Exception $e) {
            return back()->with('update-error', $e->getMessage());
        }
    }

    public function sendCode(Request $request)
    {
        $verificationCode = Str::random(6);

        Mail::to($request->email)->send(new VerificationCodeMail($verificationCode));
        $code = VerificationCode::where('email', $request->email)->first();
        if($code) {
            $code = VerificationCode::where('id', $code->id)->update([
                        'code' => $verificationCode
                    ]);
        } else {
            VerificationCode::create([
                'email' => $request->email,
                'code' => $verificationCode
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Verification code sent successfully!']);
    }
}
