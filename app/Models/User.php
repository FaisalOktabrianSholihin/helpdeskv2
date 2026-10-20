<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role_id', 'divisi_id', 'karyawan_id', 'active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Nama divisi (dari db_indukk) untuk ditampilkan di UI. Dilakukan lewat
     * query terpisah (bukan relasi Eloquent) karena divisi_id menunjuk ke
     * koneksi database lain.
     */
    public function divisiNama(): ?string
    {
        if (! $this->divisi_id) {
            return null;
        }

        return \App\Models\Indukk\Divisi::find($this->divisi_id)?->nama_divisi;
    }

    /**
     * "Pelapor" = user biasa yang hanya mengajukan tiket: tidak punya hak
     * mengedit maupun menutup tiket. Mereka hanya melihat tiket miliknya
     * sendiri, tidak boleh menghapus/membalas, dan wajib memberi rating.
     */
    public function isRequester(): bool
    {
        return ! $this->hasPermission('tiket', 'edit') && ! $this->hasPermission('tiket', 'close');
    }

    /**
     * Data karyawan di db_indukk milik akun ini (untuk isi otomatis nama
     * kontak & telepon). Dicari lewat karyawan_id, lalu email, lalu nama.
     */
    public function karyawanRecord(): ?\App\Models\Indukk\Karyawan
    {
        $q = fn() => \App\Models\Indukk\Karyawan::select(\App\Models\Indukk\Karyawan::SEPERLUNYA_COLUMNS);

        try {
            if ($this->karyawan_id && ($k = $q()->find($this->karyawan_id))) {
                return $k;
            }
            if ($this->email && ($k = $q()->whereRaw('LOWER(email) = ?', [strtolower($this->email)])->first())) {
                return $k;
            }

            return $q()->whereRaw('LOWER(nama) = ?', [strtolower($this->name)])->first();
        } catch (\Throwable $e) {
            return null; // db_indukk tidak terjangkau -> form diisi manual dari nama akun
        }
    }

    /** Semua agen/staf aktif yang boleh melihat tiket (penerima notifikasi tiket baru). */
    public static function agents(array $exceptIds = [])
    {
        return static::with('role')->where('active', true)
            ->whereNotIn('id', $exceptIds)
            ->get()
            ->filter(fn(self $u) => ! $u->isRequester() && $u->hasPermission('tiket', 'view'))
            ->values();
    }

    public function hasPermission(string $resource, string $action = 'view'): bool
    {
        $permissions = $this->role?->permissions ?? [];

        return in_array("{$resource}.{$action}", $permissions, true);
    }
}
