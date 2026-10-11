<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['icon' => 'ticket', 'title' => 'Tiket #1 membutuhkan perhatian', 'text' => 'Komputer tidak bisa menyala', 'read_at' => null],
            ['icon' => 'message-circle', 'title' => 'Balasan baru diterima', 'text' => 'Ada balasan baru pada salah satu tiket', 'read_at' => null],
            ['icon' => 'circle-check', 'title' => 'Tiket selesai', 'text' => 'Printer HR sudah ditangani', 'read_at' => now()],
        ];

        foreach ($rows as $r) {
            Notification::updateOrCreate(['user_id' => null, 'title' => $r['title']], $r);
        }
    }
}
