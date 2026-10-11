<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel ini disengaja hanya berisi SATU baris (id = 1), berperan sebagai
        // singleton pengaturan aplikasi. Setiap kolom JSON merepresentasikan satu
        // section di halaman Setting front-end (company, email, categories, dst).
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->json('company');
            $table->json('email');
            $table->json('categories');
            $table->json('statuses');
            $table->json('backup');
            $table->json('general');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
