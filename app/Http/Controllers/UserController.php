<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('users', 'view'), 403);

        $users = User::with('role')->get()->map(fn ($u) => $this->payload($u));

        return response()->json(['ok' => true, 'users' => $users]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->hasPermission('users', 'create'), 403);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6'],
            'roleId' => ['required', 'integer', 'exists:roles,id'],
            'divisiId' => ['nullable', 'integer'],
            'active' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $data['nama'],
            'email' => strtolower($data['email']),
            'password' => Hash::make($data['password']),
            'role_id' => $data['roleId'],
            'divisi_id' => $data['divisiId'] ?? null,
            'active' => $data['active'] ?? true,
        ]);

        return response()->json(['ok' => true, 'user' => $this->payload($user->load('role'))], 201);
    }

    public function update(Request $request, User $user)
    {
        abort_unless($request->user()->hasPermission('users', 'edit'), 403);

        $data = $request->validate([
            'nama' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'roleId' => ['sometimes', 'required', 'integer', 'exists:roles,id'],
            'divisiId' => ['nullable', 'integer'],
            'active' => ['nullable', 'boolean'],
        ]);

        $update = [];
        if (isset($data['nama'])) $update['name'] = $data['nama'];
        if (isset($data['email'])) $update['email'] = strtolower($data['email']);
        if (! empty($data['password'])) $update['password'] = Hash::make($data['password']);
        if (isset($data['roleId'])) $update['role_id'] = $data['roleId'];
        if (array_key_exists('divisiId', $data)) $update['divisi_id'] = $data['divisiId'];
        if (array_key_exists('active', $data)) $update['active'] = $data['active'];

        $user->update($update);

        return response()->json(['ok' => true, 'user' => $this->payload($user->fresh('role'))]);
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless($request->user()->hasPermission('users', 'delete'), 403);

        if ($user->id === $request->user()->id) {
            return response()->json(['ok' => false, 'message' => 'Tidak bisa menghapus akun yang sedang digunakan.'], 422);
        }

        $user->delete();

        return response()->json(['ok' => true]);
    }

    private function payload(User $u): array
    {
        return [
            'id' => $u->id,
            'nama' => $u->name,
            'email' => $u->email,
            'roleId' => $u->role_id,
            'divisiId' => $u->divisi_id,
            'divisiNama' => $u->divisiNama(),
            'active' => (bool) $u->active,
        ];
    }
}
