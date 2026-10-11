<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower(trim($request->string('email')));
        $user = User::where('email', $email)->first();

        if (! $user || ! $user->active || ! Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'ok' => false,
                'message' => 'Email/password salah atau akun sedang nonaktif.',
            ], 422);
        }

        $request->session()->regenerate();
        Auth::login($user, true);

        return response()->json([
            'ok' => true,
            'user' => $this->userPayload($user),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['ok' => true]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['ok' => false], 401);
        }

        return response()->json(['ok' => true, 'user' => $this->userPayload($user)]);
    }

    public function userPayload(User $user): array
    {
        $user->loadMissing('role');

        return [
            'id' => $user->id,
            'nama' => $user->name,
            'email' => $user->email,
            'roleId' => $user->role_id,
            'roleNama' => $user->role?->nama,
            'permissions' => $user->role?->permissions ?? [],
            'divisiId' => $user->divisi_id,
            'divisiNama' => $user->divisiNama(),
            'active' => (bool) $user->active,
        ];
    }
}
