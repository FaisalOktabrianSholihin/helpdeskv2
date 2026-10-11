<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Support\PermissionCatalog;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('roles', 'view'), 403);

        $roles = Role::withCount('users')->get()->map(fn ($r) => [
            'id' => $r->id,
            'nama' => $r->nama,
            'permissions' => $r->permissions ?? [],
            'jml' => $r->users_count,
        ]);

        return response()->json(['ok' => true, 'roles' => $roles]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->hasPermission('roles', 'create'), 403);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
        ]);
        $data['permissions'] = PermissionCatalog::sanitize($data['permissions'] ?? []);

        $role = Role::create($data);

        return response()->json(['ok' => true, 'role' => ['id' => $role->id, 'nama' => $role->nama, 'permissions' => $role->permissions, 'jml' => 0]], 201);
    }

    public function update(Request $request, Role $role)
    {
        abort_unless($request->user()->hasPermission('roles', 'edit'), 403);

        $data = $request->validate([
            'nama' => ['sometimes', 'required', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
        ]);
        if (array_key_exists('permissions', $data)) {
            $data['permissions'] = PermissionCatalog::sanitize($data['permissions'] ?? []);
        }

        $role->update($data);

        return response()->json(['ok' => true, 'role' => $role->fresh()]);
    }

    public function updatePermissions(Request $request, Role $role)
    {
        abort_unless($request->user()->hasPermission('roles', 'permissions'), 403);

        $data = $request->validate(['permissions' => ['nullable', 'array']]);
        $role->update(['permissions' => PermissionCatalog::sanitize($data['permissions'] ?? [])]);

        return response()->json(['ok' => true, 'role' => $role->fresh()]);
    }

    public function destroy(Request $request, Role $role)
    {
        abort_unless($request->user()->hasPermission('roles', 'delete'), 403);

        if ($role->users()->exists()) {
            return response()->json(['ok' => false, 'message' => 'Role masih dipakai oleh user, tidak bisa dihapus.'], 422);
        }

        $role->delete();

        return response()->json(['ok' => true]);
    }
}
