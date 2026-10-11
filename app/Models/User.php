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

    public function hasPermission(string $resource, string $action = 'view'): bool
    {
        $permissions = $this->role?->permissions ?? [];

        return in_array("{$resource}.{$action}", $permissions, true);
    }
}

