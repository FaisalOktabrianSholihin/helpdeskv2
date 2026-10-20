<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Rating 1-5 dari pelapor saat konfirmasi tiket selesai.
            $table->unsignedTinyInteger('rating')->nullable()->after('reopened_count');
            $table->timestamp('rated_at')->nullable()->after('rating');
        });

        Schema::table('notifications', function (Blueprint $table) {
            // Supaya klik notifikasi bisa langsung membuka tiketnya.
            $table->unsignedBigInteger('ticket_id')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['rating', 'rated_at']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('ticket_id');
        });
    }
};
