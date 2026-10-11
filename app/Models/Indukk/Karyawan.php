<?php

namespace App\Models\Indukk;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Data Karyawan milik db_indukk (sistem HR pusat). Helpdesk hanya MEMBACA
 * tabel ini, dan hanya mengambil kolom yang benar-benar diperlukan untuk
 * konteks helpdesk (kontak & identifikasi), bukan seluruh data HR
 * (alamat, no KTP, tanggal lahir, cuti, dst yang ada di db_indukk).
 */
class Karyawan extends Model
{
    protected $connection = 'indukk';

    protected $table = 'karyawans';

    protected $guarded = ['*'];

    /** Kolom yang boleh ikut ter-serialize ke JSON/API. */
    protected $visible = [
        'id', 'nik', 'no_absen', 'nama', 'email', 'no_hp',
        'jabatan_id', 'divisi_id', 'status_aktif', 'jenis_kelamin',
        'jabatan_nama', 'divisi_nama',
    ];

    protected $appends = ['jabatan_nama', 'divisi_nama'];

    /**
     * Kolom yang benar-benar di-SELECT dari database saat listing, supaya
     * kolom sensitif (alamat, no_ktp, tanggal_lahir, dll) tidak pernah
     * ikut ter-tarik dari db_indukk sama sekali.
     */
    public const SEPERLUNYA_COLUMNS = [
        'id', 'nik', 'no_absen', 'nama', 'email', 'no_hp',
        'jabatan_id', 'divisi_id', 'status_aktif', 'jenis_kelamin',
    ];

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    public function getJabatanNamaAttribute(): ?string
    {
        return $this->jabatan?->nama_jabatan;
    }

    public function getDivisiNamaAttribute(): ?string
    {
        return $this->divisi?->nama_divisi;
    }
}
