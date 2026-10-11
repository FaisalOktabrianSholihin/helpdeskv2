<?php

namespace App\Support;

/**
 * Katalog permission resource/action, harus selalu sinkron dengan
 * `permissionCatalog` di assets/js/app.js (front-end). Dipakai untuk:
 * - Memvalidasi array permission yang dikirim saat simpan role.
 * - Menentukan permission default untuk role "Superadmin".
 */
class PermissionCatalog
{
    /**
     * @return array<string, array<int, string>> resource => [action, ...]
     */
    public static function map(): array
    {
        return [
            'dashboard' => ['view'],
            'tiket'     => ['view', 'create', 'edit', 'delete', 'reply', 'close', 'print'],
            'divisi'    => ['view'],
            'karyawan'  => ['view'],
            'analitik'  => ['view'],
            'users'     => ['view', 'create', 'edit', 'delete'],
            'setting'   => ['view', 'edit'],
            'roles'     => ['view', 'create', 'edit', 'delete', 'permissions'],
        ];
    }

    /**
     * @return array<int, string> semua permission key "resource.action" yang valid
     */
    public static function allKeys(): array
    {
        $keys = [];
        foreach (self::map() as $resource => $actions) {
            foreach ($actions as $action) {
                $keys[] = "{$resource}.{$action}";
            }
        }

        return $keys;
    }

    /**
     * Saring array permission input, buang key yang tidak valid.
     *
     * @param  array<int, mixed>  $permissions
     * @return array<int, string>
     */
    public static function sanitize(array $permissions): array
    {
        $valid = self::allKeys();

        return array_values(array_unique(array_filter(
            $permissions,
            fn ($p) => is_string($p) && in_array($p, $valid, true)
        )));
    }
}
