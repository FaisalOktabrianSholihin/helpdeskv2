<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        // current() otomatis membuat baris default (id=1) kalau belum ada.
        Setting::current();
    }
}
