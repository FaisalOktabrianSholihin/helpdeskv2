<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->timestamp('closed_at')->nullable()->after('updated_at');
        });

        // Data lama yang sudah berstatus selesai tidak memiliki histori waktu
        // penutupan. Untuk menjaga grafik historis tetap masuk akal, gunakan
        // updated_at sebagai waktu penutupan awal.
        DB::table('tickets')
            ->where('status', 'selesai')
            ->whereNull('closed_at')
            ->update(['closed_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('closed_at');
        });
    }
};
