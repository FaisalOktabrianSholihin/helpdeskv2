<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();

            // Kontak/pelapor: snapshot nama selalu diisi (agar tetap tampil walau
            // data karyawan induk berubah/hapus), karyawan_id opsional sebagai
            // tautan ke db_indukk (beda koneksi, jadi tanpa FK constraint).
            $table->string('kontak_nama');
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->unsignedBigInteger('divisi_id')->nullable();
            $table->string('telepon')->nullable();

            $table->enum('status', ['baru', 'menunggu', 'proses', 'selesai'])->default('baru');
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi'])->default('sedang');
            $table->string('tipe')->default('Incident');
            $table->string('sumber')->default('Web');

            $table->foreignId('agen_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
