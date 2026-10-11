<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['company', 'email', 'categories', 'statuses', 'backup', 'general'])]
class Setting extends Model
{
    protected function casts(): array
    {
        return [
            'company' => 'array',
            'email' => 'array',
            'categories' => 'array',
            'statuses' => 'array',
            'backup' => 'array',
            'general' => 'array',
        ];
    }

    /** Tabel ini singleton: selalu satu baris dengan id = 1. */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'company' => ['name' => 'PT. Mitratani Dua Tujuh', 'email' => 'helpdesk@mitratani27.co.id', 'phone' => '0331-xxxxxx', 'address' => 'Jember, Jawa Timur'],
            'email' => ['host' => 'smtp.mitratani27.co.id', 'port' => '587', 'sender' => 'helpdesk@mitratani27.co.id', 'enabled' => true],
            'categories' => ['Incident', 'Request', 'Problem', 'Feature Request'],
            'statuses' => ['Baru', 'Menunggu', 'Proses', 'Selesai'],
            'backup' => ['last' => 'Belum pernah', 'frequency' => 'Harian'],
            'general' => ['timezone' => 'Asia/Jakarta', 'language' => 'Bahasa Indonesia', 'autoRefresh' => true],
        ]);
    }
}
